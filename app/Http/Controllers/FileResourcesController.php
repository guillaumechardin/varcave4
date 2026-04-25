<?php

namespace App\Http\Controllers;

use App\Helpers\VarcaveApiResponse;
use App\Models\FileResource;
use App\Models\FileResourceGroup;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class FileResourcesController extends Controller
{
    /**
     * () get resources details and call dedicated view
     * 
     */
    public function show(Request $request): View
    {
        Log::debug(__METHOD__ . ' Show file resources view');
        /*
        $fileResources = FileResource::orderBy('sort_order', 'desc')
            ->with('fileResourceGroup:name,id')
            ->with('user:username,firstname,id')
            ->get();

        $resourceGroups = array();
        foreach($fileResources as $fr){
            $resourceGroups[$fr->group_id]['name'] = $fr->group_name; 
            $resourceGroups[$fr->group_id]['data'][] = $fr;
        }
        */

        $fileResourceByGroup = FileResourceGroup::orderBy('sort_order', 'desc')
            ->with('fileResource.user')
            ->get();

        $roles = Role::all();

        return view('varcave.resources',
        [
            'pageTitle' => __('varcave.resources.page_title'),
            'roles' => $roles,
            'fileResourceByGroup' => $fileResourceByGroup,
        ]);
    }

    /**
     * () create new resource linked to file-group
     * 
     */
    public function store(Request $request, User $user)
    {
        Log::debug(__METHOD__ . ' create new resource');
        if ($request->user()->cannot('isResourceAdmin', FileResource::class)) {
            Log::warning('User: ' . $request->user()->username . ' is not authorized');
            abort(403);
        }

        $validator = Validator::make($request->all(), [
            'file' => [
                'required',
                File::types(json_decode(Setting::get('authorized_resources_file_type')))
            ],
            'new-group' => [
                'string',
                'max:64',
                'required_without:group',
            ],
            'group' => [
                'string',
                'required_without:new-group',
            ],
            'file-title-name' => ['required', 'string', 'max:64'],
            'access_rights' => ['required', 'array'],
            'access_rights.*' => ['integer'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        if($validator->fails()) {
            Log::debug('File validation fail',[$validator->errors()]);
            return redirect()->back()
                ->withErrors($validator, 'upload')
                ->withInput();
        }
        
        $validated = $validator->validated();

        $file = $request->file('file');
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $originalFilename = Str::slug($file->getClientOriginalName());
        $path = $file->storeAs('file_resources', $filename, 'public');

        //create group if non existant
        if (!empty($validated['new-group'])) {
            Log::info('Create or use new file resource group: ' . $validated['new-group']);
            $group = FileResourceGroup::firstOrCreate(
                ['name' => Str::lower( trim($validated['new-group'])) ],
                [
                    'sort_order' => 0,
                    'created_at' => now(),
                ]
            );
        } else {

            Log::info('Using existing group');
            $group = FileResourceGroup::findOrFail($validated['group'] );
        }

        
        //create Resource
        FileResource::create([
            'user_id' => $request->user()->id,
            'file_resource_group_id' => $group->id,
            'name' => $validated['file-title-name'],
            'file_path' => $path,
            'original_file_name' => $originalFilename,
            'description' => $validated['description'],
            'access_rights' => $validated['access_rights'],
            'is_hidden' => 0,
            'created_at' => now(),
        ]);

        Log::info('File save succesfully');

        return redirect()
            ->back()
            ->with('success', __('varcave.general.opSuccess'));
            
    }

    public function update(FileResource $fileResource, Request $request)
    {
        Log::debug(__METHOD__ . ' create new resource');
        if ($request->user()->cannot('isResourceAdmin', FileResource::class)) {
            Log::warning('User: ' . $request->user()->username . ' is not authorized');
            abort(403);
        }

        $validated = $request->validate([
            'access_rights' => ['sometimes', 'array'],
            'is_hidden'     => ['sometimes', 'boolean'],
            'sort_order'    => ['sometimes', 'integer', 'min:0'],
        ]);

        $fileResource->update($validated);

        return redirect()
            ->back()
            ->with('success', __('varcave.general.opSuccess'));

    }

    public function destroy(FileResource $fileResource, Request $request)
    {
        Log::debug(__METHOD__ . ' delete resource:'.$fileResource->id);
        if ($request->user()->cannot('isResourceAdmin', FileResource::class)) {
            Log::warning('User: ' . $request->user()->username . ' is not authorized');
            abort(403);
        }
        
        //remove physical and db data  
        Storage::disk('public')->delete($fileResource->file_path);
        $fileResource->delete();

        Log::info('Resource deleted');
        
        return VarcaveApiResponse::ajaxResponse(
            'success',
            'success',
            'element supprimé',
            '',
            200,
            '',
            route('varcave.resources.file-show')
        );
    }

    public function get(FileResource $fileResource, Request $request)
    {
        Log::debug(__METHOD__ . ' get resource file:'.$fileResource->id);
        if ($request->user()?->cannot('getResource', $fileResource)) {
            Log::warning('User: ' . ($request->user()?->username ?? 'guest') . ' is not authorized');
            abort(403);
        }
        
        return Storage::disk('public')
            ->download($fileResource->file_path, $fileResource->original_file_name);
        
    }
}

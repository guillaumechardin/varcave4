<?php

namespace App\Http\Controllers;

use App\Helpers\VarcaveApiResponse;
use App\Models\Cave;
use App\Models\FileResource;
use App\Models\FileResourceGroup;
use App\Models\Page;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
use App\Services\CaveService;
use App\Services\GpxService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class FileResourcesController extends Controller
{
    /**
     * get resources details and call dedicated view
     * 
     */
    public function show(Request $request): View
    {
        Log::debug(__METHOD__ . ' Show file resources view');

        $fileResourceByGroup = FileResourceGroup::orderBy('sort_order', 'desc')
            ->with('fileResource.user')
            ->get();

        $roles = Role::all();

        $countAllCaves = Cave::all()->count();

        return view('varcave.resources',
        [
            'pageTitle' => __('varcave.resources.page_title'),
            'roles' => $roles,
            'fileResourceByGroup' => $fileResourceByGroup,
            'countAllCaves' => $countAllCaves,
        ]);
    }

    /**
     * create new resource linked to file-group
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
            'access_rights.*' => ['string', 'exists:roles,name'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        if($validator->fails()) {
            Log::debug('File validation fail',[$validator->errors()]);
            return redirect()->to(url()->previous() . '#tab=tab-create-resource')
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
            'no msg',
            '',
            200,
            '',
            route('varcave.resource.show')
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

    /**
     * Build the complete GPX dataset containing all caves accessible to a standard user.
     *
     * This method:
     * - creates a temporary system user with the "user" role;
     * - temporarily impersonates this user to ensure cave visibility rules are respected;
     * - generates the GPX document using {@see GpxService};
     * - restores the previous authenticated user.
     *
     * This operation is intended to be executed by a resource administrator and
     * may require increased execution time and memory limits.
     *
     * @return void
     */
    public function buildGpxFullData(request $request, int $timeLimit = 250, int $memoryLimit = 400){
        Log::info('Build complete gpx file');
        
        Gate::authorize('isResourceAdmin', FileResource::class); 

        set_time_limit($timeLimit);
        $memLimitMB = $memoryLimit.'M';
        ini_set('memory_limit', $memLimitMB);

        //Create a dummy user
        $systemUser = new User([
            'id' => 0,
            'username' => 'System',
            'firstname' => 'System User',
        ]);

        $role = Role::where('name', 'user')->get();
        $systemUser->setRelation('roles', collect($role));
        
        /**
         * impersonate with dummy user to 
         * get correct data to be generated from CaveService 
         * (ie hidden/absent coordinates for protected Caves)
        */
        //Save auth state for later restore
        $previousUser = Auth::user();
        Auth::setUser($systemUser);

        $caves = Cave::where('id', '>', 0)
        //->limit(30)
        ->get(['uuid', 'name', 'length', 'max_depth']);
        $page = new Page()->setPageModelFor('gpx-build', 'main', true);

        //translate data to CaveService Caves
        $caveData = array();
        foreach($caves as $cave){
            $cs = new CaveService($cave, $systemUser, CaveService::ADD_COORDS);
            $caveData[] = $cs->renderForPage($page);
        }

        //restaure Auth State
        Auth::setUser($previousUser);
        $gpxService = new GpxService();
        $gpxData = $gpxService->createGPX($caveData);

        $group = FileResourceGroup::firstOrCreate([
            'name' => 'SIG',
        ]);

        $filename = Str::slug(env('APP_NAME', '')) . '_' . now()->format('Y-m-d_H-i-s') . '.gpx';

        $fname =  Str::uuid() . '.gpx';
        $filepath = 'file_resources/' . $fname;
        Storage::disk('public')->put(
                $filepath,
                $gpxData
        );

        $path = Storage::disk('public')->path($filename);

        FileResource::create([
            'user_id' => $request->user()->id,
            'file_resource_group_id' => $group->id,
            'name' => $filename,
            'file_path' => $filepath,
            'original_file_name' => $filename,
            'description' => __('varcave.resources.gpx_file_description'),
            'access_rights' => ['user', 'admin'],
            'is_hidden' => 0,
            'created_at' => now(),
        ]);

        return VarcaveApiResponse::ajaxResponse(
            'success',
            'success',
            '',
            '',
            200,
            '',
            route('varcave.resource.show'),
        );



    }
}

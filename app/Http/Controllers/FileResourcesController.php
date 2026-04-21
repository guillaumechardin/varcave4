<?php

namespace App\Http\Controllers;

use App\Models\FileResource;
use App\Models\FileResourceGroup;
use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
}

<?php

namespace App\Http\Controllers;

use App\Models\Cave;
use App\Models\Page;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;



class CaveController extends Controller
{
    /**
     * () get cave details. This does not fetch details from cave_files or cave_coordinates
     * 
     */
    public function show(string $uuid, Request $request): View
    {
        $cave = Cave::getByuuid($uuid);
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.cave.caveNotFound') ) ); 
        }
        
        $pageKey = 'display';
        $page = Page::pageFieldsFor($pageKey);

        

        if($request->query('display') == 'legacy') {
            return view('varcave.caveshowLegacy', ['caveData' => $cave, 'pageFields' => $page ]);
        }
        return view('varcave.caveshowv4', ['caveData' => $cave, 'pageFields' => $page ]);
        
    }
}

<?php

namespace App\Http\Controllers;

use App\ViewModels\CaveViewModel;
use App\Models\Cave;
use App\Models\Page;

use App\Models\CaveFile;
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
        
        $page = Page::pageFieldsFor('display', 'main');
        
        $vm = new CaveViewModel(
            Cave::getByuuid($uuid),
            Page::pageFieldsFor('display', 'main')
        );

        if($request->query('display') == 'legacy') {
            return view('varcave.caveshowLegacy', 
                [
                    'caveData'   => $cave, 
                    'caveFields' => $vm->getFields(),
                    'cave_maps'  => CaveFile::get($cave->uuid, 'cave_maps')->toArray(),
                ]
            );
        }
        return view('varcave.caveshowv4',
            [
                'caveData'   => $cave, 
                'caveFields' => $vm->getFields(),
                'cave_maps' => CaveFile::get($cave->uuid, 'cave_maps')->toArray(),
                'cave_docs' => CaveFile::get($cave->uuid, 'documents')->toArray(),
            ]
        );
        
    }
}

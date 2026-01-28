<?php

namespace App\Http\Controllers;

use App\Models\Cave;
use App\Models\Page;
use App\Models\Setting;
use App\Models\CaveFile;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Models\CaveCoordinates;
use App\ViewModels\CaveViewModel;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        
        $caveInfo = new CaveViewModel(
            $cave,
            Page::pageFieldsFor('display', 'main')
        );

         $caveBibliography = new CaveViewModel(
            $cave,
            Page::pageFieldsFor('display', 'bibliography')
        );
        
        $caveDescription = [];
        $caveCoordinates = null;
        $nearCaves = null;
        $caveAccess = null;
        $caveMaps = null;
        $caveDocs = null;


        if (Gate::allows('showAllCaveDetails', $cave) ) {
            $caveDescription = new CaveViewModel(
                $cave,
                Page::pageFieldsFor('display', 'description')
            );
            $caveDescription = $caveDescription->getFields();

            $caveCoordinates = CaveCoordinates::get($cave->uuid);
            if ($caveCoordinates->first()['x'] != 0) { //this cave have at least 1 set of coordinates defined !
                $nearCaves = CaveCoordinates::findNearCaves($caveCoordinates, Setting::get('near_caves_max_radius'), Setting::get('near_caves_max_number'), $cave->id);
                $caveCoordinates = $caveCoordinates->toArray();
            }

            $caveAccess = new CaveViewModel(
                $cave,
                Page::pageFieldsFor('display', 'access')
            );

            $caveMaps = CaveFile::get($cave->uuid, 'cave_maps')->toArray();
			$caveDocs = CaveFile::get($cave->uuid, 'documents')->toArray();
        }

        //return only required raw data to view
        $cave2 = (object) [
            'name' => $cave->name,
            'uuid' => $cave->uuid,
            //'bibliography' => $cave->bibliography,
        ];

        return view('varcave.caveshowv4',
            [
                'caveObj' => $cave,
                'cave'   => $cave2, 
                'caveInfo' => $caveInfo->getFields(),
                'caveBibliography' => $caveBibliography->getFields(),
                'caveDescription' => $caveDescription,
                'caveCoordinates' => $caveCoordinates,
                'nearCaves' => $nearCaves,
                'caveAccess' => $caveAccess?->getFields(),
                'caveMaps' => $caveMaps,
                'caveDocs' => $caveDocs,
            ]
        );  
    }

    public function search(Request $request): View|JsonResponse {
        $query = Cave::query();
        $page = Page::pageFieldsFor('search', 'main');
        
        $fields = [];
       foreach($page->pageFields as $pageField ){
                $fields[] = $pageField->field->key;
                //dd($pageField->field);
            
        
        };
        //dd($fields);
        Log::debug('fields:',$fields);

        // Appliquer filtres dynamiques
        foreach ($fields as $field) {
            Log::debug('request',$request->toArray());
            $value = $request->input('value_'.$field);
            $type = $request->input('type_'.$field);
            Log::debug('if::::'.'value_'.$field);
            

            if ($value !== null && $value !== '') {
                switch ($type) {
                    case 'LIKE': $query->where($field, 'like', "%$value%"); break;
                    case '=': $query->where($field, $value); break;
                    case 'NOTEQUAL': $query->where($field, '!=', $value); break;
                    case '>': case '<': case '>=': case '<=': $query->where($field, $type, $value); break;
                }
            }
        }

        if ($request->ajax()) {
            return response()->json($query->get()); // JSON pour DataTables
        }

        
        return view('varcave.cavesearch',
            [
                "page" => $page,
            ]
        );
    }
}

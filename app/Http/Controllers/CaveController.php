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
use Illuminate\Validation\ValidationException;
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
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }
        
        $mainCaveFields = new Page();
        $mainCaveFields->setPageModelFor('display', 'main');
        $vm = new CaveViewModel($mainCaveFields);
        $caveData = $vm->render($cave);

        //get bibliography
        $caveBibliographyFields = new Page();
        $caveBibliographyFields->setPageModelFor('display', 'bibliography');
        $vm = new CaveViewModel($caveBibliographyFields);
        $caveBibliography = $vm->render($cave);
       
        

        $caveDescription = [];
        $caveCoords = null;
        $caveAccess = null;
        $caveMaps = null;
        $caveDocs = null;


        if (Gate::allows('showAllCaveDetails', $cave) ) {
            $caveDescriptionFields = new Page();
            $caveDescriptionFields->setPageModelFor('display', 'description');
            $vm = new CaveViewModel($caveDescriptionFields);
            $caveDescription = $vm->render($cave);
            
            $caveCoordinates = CaveCoordinates::get($cave->uuid);
            if ($caveCoordinates->first()['x'] != 0) { //this cave have at least 1 set of coordinates defined !
                $nearCaves = CaveCoordinates::findNearCaves($caveCoordinates, Setting::get('near_caves_max_radius'), Setting::get('near_caves_max_number'), $cave->id);
                $caveCoords['near_caves'] = $nearCaves->toArray() ;
            }
            $caveCoords['cave_coords'] = $caveCoordinates->toArray(); 

            $caveAccessFields = new Page();
            $caveAccessFields->setPageModelFor('display', 'access');
            $vm = new CaveViewModel($caveAccessFields);
            $caveAccess = $vm->render($cave);
            

            $caveMaps = CaveFile::get($cave->uuid, 'cave_maps')->toArray();
			$caveDocs = CaveFile::get($cave->uuid, 'documents')->toArray();
        }

        return view('varcave.caveshowv4',
            [
                'pageTitle' => $cave->name,
                'caveData' => $caveData,
                'caveObj' => $cave,
                
                'caveDescription' => $caveDescription,

                'caveAccess' => $caveAccess,
                'caveCoords' => $caveCoords,
            
                
                
                'caveBibliography' => $caveBibliography,
                'caveMaps' => $caveMaps,
                'caveDocs' => $caveDocs,
            ]
        );  
    }

    public function search(Request $request): View|JsonResponse {
        
        $allCaves = strtolower($request->query('caves')) === 'all';

        //get fields for search form
        $pageSearchForm = new Page();
        $pageSearchForm->setPageModelFor('search', 'main',1);
        $formFields = $pageSearchForm->getModelFields();        

        
        //fetch cols for datatables results only, must be present for view form construction
        $pageDatatablesTable = new Page();
        $pageDatatablesTable->setPageModelFor('searchResultsColumns', 'main', 1);
        $datatablesFields = $pageDatatablesTable->getModelFields();

        $datatablesLang = json_encode(__('varcave.searchPage.datatables'), JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE) ;
        
        //Reply to a user search request Form
        if ( $request->expectsJson() | $allCaves != false) {
            //Check is form not empty
            if (
                //form empty send empty data set
                collect($request->all())
                    ->filter(fn ($v, $k) => str_starts_with($k, 'value_') && filled($v))
                    ->isEmpty()){
                return response()->json(
                    []
                );
            }

            //Prepare to build query and prepare a list a fields that are available to user
            $query = Cave::query();
            $searchFormFields = array_keys($formFields); // we only query fields that will be available in datatables
            //$searchFormFields[] = 'uuid'; //add uuid to click on tr datatables
            $query->select($searchFormFields);
            

            if(! $allCaves){
                // Search and apply dynamic filters
                foreach ($searchFormFields as $field) {
                    //Log::debug('request',$request->toArray());
                    $value = $request->input('value_'.$field);
                    $type = $request->input('type_'.$field);

                    if ($value !== null && $value !== '') {
                        switch ($type) {
                            case 'LIKE': $query->where($field, 'like', "%$value%"); break;
                            case '=': $query->where($field, $value); break;
                            case 'NOTEQUAL': $query->where($field, '!=', $value); break;
                            case '>': case '<': case '>=': case '<=': $query->where($field, $type, $value); break;
                        }
                    }
                    
                }
            }
            
            $caves = [];

            //$pageDatatablesTable = new Page();
            //$pageDatatablesTable->setPageModelFor('searchResultsColumns', 'main', true);
            $vm = new CaveViewModel($pageDatatablesTable);
            foreach ($query->get() as $cave) {
                $caves[] = $vm->render($cave, CaveViewModel::AS_DATATABLES);
            }
            
            // JSON return for DataTables
            return response()->json(
                $caves
            ); 
        }

        return view('varcave.cavesearch',
            [
                "formFields" => $formFields,
                "datatablesFields" => $datatablesFields,
                'datatablesLang' => $datatablesLang,
                'request' => $request,
            ]
        );
    }

    
    public function quicksearch(Request $request){
        
        $query = Cave::query();
        $qs_value = $request->input('term');
        
        $query->where('name', 'like', "%$qs_value%")->select(['name','uuid']);
        
        return response()->json(
            $query->get(['name', 'uuid'])->map(fn ($cave) => [
                'label' => $cave->name,
                'value' => $cave->name,
                'uuid'  => $cave->uuid,
            ])
        );
    }
}

<?php
namespace App\Http\Controllers;

use App\Models\Cave;
use App\Models\CoordinateSystemHandler;
use App\Models\Page;
use App\Models\Setting;
use App\Models\CaveStat;
use App\Models\User;
use App\Services\CavePdfService;
use App\Services\CaveService;
use App\Services\GpxService;
use App\ViewModels\CaveViewModel;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;

class CaveController extends Controller
{
    /**
     * () get cave details and call dedicated view
     * 
     */
    public function show(string $uuid, Request $request): View
    {
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        $csOptions = 0;
        if($request->user() != null &&  $request->user()->can('showAllCaveDetails', Cave::class))
        {
            $csOptions = CaveService::ADD_ALL;
        }
        $cs = new CaveService($cave, $request->user(), $csOptions);

        if(Setting::get('collect_cave_stats'))
        {
            CaveStat::updateStat($cave, $request->user());
        }


        $pageMain = new Page()->setPageModelFor('display', 'main', true);
        $caveData = $cs->renderForPage($pageMain);

        $pageDescription = new Page()->setPageModelFor('display', 'description');
        $caveDescription = $cs->renderForPage($pageDescription);

        $pageBiblio = new Page()->setPageModelFor('display', 'bibliography');
        $caveBibliography = $cs->renderForPage($pageBiblio);

        $pageAccess = new Page()->setPageModelFor('display', 'access');
        $caveAccess = $cs->renderForPage($pageAccess);

        $crs = CoordinateSystemHandler::getAllCrs();

        $cave->refresh()->load('changelog');
        $cave->getViewCount();

        /**
         * Isolate cave docs, resulting in 2 array
         *   documents "photos"
         *   documents that are not photos (pdf, docx, pdf,)
         */

        $caveDocsPhotos = array();
        $caveDocsFiles = array();
        
        //or unauthenticated user
        if($caveData['caveFiles'] === null) $caveData['caveFiles'] = array();

        foreach($caveData['caveFiles']  as $key => $docTypes){
            if( in_array($key, ['cave_maps','photos']) ) continue; //skip specific documents type
            
            foreach($docTypes as $doc){
                //$filename = storage_path('app/public/'.$doc['file_path']);
                $photosFilesExt = ['jpg','jpeg','png','webp'];
                $doc['extension'] = pathinfo($doc['file_path'], PATHINFO_EXTENSION);
                if(in_array($doc['extension'], $photosFilesExt))
                {
                    $doc['is_img'] = true;
                    $caveDocsFiles[] = $doc;
                    
                }else{
                    $doc['is_img'] = false;
                    $caveDocsFiles[] = $doc;
                }
            }
        }

        return view('varcave.caveshowv4',
            [
                'pageTitle' => $caveData['attributes']['data']['name'],
                'caveObj' => $cave,
                'caveData' => $caveData,
                'caveDocsPhotos' => $caveDocsPhotos,
                'caveDocsFiles' => $caveDocsFiles,
                'rescueFiles' => $caveData['caveFiles']['rescue_files'] ?? [],
                'caveBibliography' => $caveBibliography ?? null,
                'caveDescription' => $caveDescription ?? null,
                'caveAccess' => $caveAccess ?? null,
                'crs' => $crs,
                'changeHistory' => $cave->changelog,
            ]
        );  
    }

    public function search(Request $request): View|JsonResponse
    {
        Log::debug(__METHOD__ . 'called');
        Log::debug('request',['$request' => $request->toArray()]);
        
        $allCaves = strtolower($request->query('caves')) === 'all';

        //get fields for search form
        $page = new Page();
        $pmSearchForm= $page->setPageModelFor('search', 'main', true)->getModelFields();
        $availFormFields = array_keys($pmSearchForm); // we only query fields that will be available in datatables

        //fetch cols for datatables results only, must be present for view form construction
        $pageDatatable = new Page();
        $pmDatatablesTable = $pageDatatable->setPageModelFor('searchResultsColumns', 'main', true)->getModelFields();

        $datatablesLang = json_encode(__('varcave.searchPage.datatables'), JSON_PRETTY_PRINT |  JSON_UNESCAPED_UNICODE) ;
        
        //Reply to a user search request Form
        if ( $request->expectsJson() | $allCaves != false) {
            //Check is form not empty
            if (!$allCaves &&
                //User send an empty data set. So we return the same
                collect($request->all())
                    ->filter(fn ($v, $k) => str_starts_with($k, 'value_') && filled($v))
                    ->isEmpty()){
                return response()->json(
                    []
                );
            }

            //Prepare to build query and prepare a list a fields that are available to user
            $query = Cave::query();
            $query->select($availFormFields);            

            if(! $allCaves){ //overload query if user search param present, if not, query return all caves
                // Search and apply dynamic filters
                foreach ($availFormFields as $field) {
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
            
            $caves = null;
            $caveObj = Cave::find(1)->firstOrFail();  //"random" cave just to get required fields list

            //handling limits
            $start  = (int) $request->input('start', 0);
            $length = (int) $request->input('length', 5);
            $draw   = (int) $request->input('draw', 1);

            $totalRecords = $query->count(); // count before limit

            $cavesSrch = $query
            ->offset($start)
            ->limit($length)
            ->get();

            Log::debug(' Sql query:', [$query->toSql(), 'bindings' => $query->getBindings(),]);
            //set_time_limit(220); // 120 secondes, allcaves can be very long to process
            $cs = new CaveService($caveObj, $request->user(), true);
            foreach ($cavesSrch as $cave) {
                $_cave = array();
                foreach($pmDatatablesTable as $key => $field){
                    $_cave[$key] = $cs->formatValue($cave->{$key}, $key, $field, $field );

                }
                $caves[] = $_cave;
            }
            
            // JSON return for DataTables
            return response()->json(
                [
                    "draw" => $draw,
                    "recordsTotal" => $totalRecords,
                    "recordsFiltered" => $totalRecords,
                    "data" => $caves,
                ]
            ); 
        }

        return view('varcave.cavesearch',
            [
                "searchFormFields" => $pmSearchForm ?? null,
                "datatablesFields" => $pmDatatablesTable ?? null,
                'datatablesLang' => $datatablesLang,
                'request' => $request,
            ]
        );
    }

    
    public function quicksearch(Request $request)
    {
        
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

    public function getGpx(string $uuid, Request $request, $filename = null)
    {
        $gpxService = new GpxService();
        $cave = Cave::getByUuid($uuid);
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }
       
        $pageMain = new Page()->setPageModelFor('display', 'main', true);
        
        $cs = new CaveService($cave, $request->user(), CaveService::ADD_COORDS);
        
        $gpxFile = $gpxService->createGPX( array($cs->renderForPage($pageMain))  );

        return response($gpxFile, 200)
            ->header('Content-Type', 'application/xml')
            ->header('Content-Disposition', 'attachment; filename="' . Str::limit( Str::slug($cave->name), 40, '') . '.gpx"');
    }

    public function getPdf(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');

        $cave = Cave::getByUuid($uuid);
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        $user = $request->user();
        if(! $user->can('downloadPdf', $cave)){
            abort(403, 'Unauthorized');
        }

        $csOptions = CaveService::ADD_ALL;
        $cs = new CaveService($cave, $request->user(), $csOptions); 
        $pagePdf = new Page()->setPageModelFor('pdf', 'main', true);
        $caveData = $cs->renderForPage($pagePdf);

        $pdf = new CavePdfService($caveData);

        $pdf->render();
    }

    public function viewStats(Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $stats = CaveStat::getGlobalStats();
        return view('varcave.statistics',
            [
                "pageTitle" => __('varcave.statistics.pageTitle'),
                "statistics" => $stats,
            ]
        );
    }

}

<?php
namespace App\Http\Controllers;

use App\Helpers\Tools;
use App\Helpers\VarcaveApiResponse;
use App\Mail\ContactFormMail;
use App\Models\Cave;
use App\Models\CaveChangelog;
use App\Models\CaveCoordinates;
use App\Models\CaveFile;
use App\Models\CaveStat;
use App\Models\CoordinateSystemHandler;
use App\Models\Field;
use App\Models\ListValue;
use App\Models\Page;
use App\Models\Setting;
use App\Models\User;
use App\Services\CaveService;
use App\Services\GpxService;
use App\Services\StaticMapService;
use App\Services\VarcaveTcpdf;
use Com\Tecnick\Pdf\Tcpdf;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
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


        $pageMain = new Page()->setPageModelFor('display', 'main', false);
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
                'pageTitle' => $cave->name,
                'caveObj' => $cave,
                'caveName' => $cave->name,
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
        $pagePdf = new Page()->setPageModelFor('pdf', 'main');
        $caveData = $cs->renderForPage($pagePdf);

        $pageBiblio = new Page()->setPageModelFor('pdf', 'bibliography');
        $bib = $cs->renderForPage($pageBiblio);  
        $caveData['bibliography'] = $bib['attributes'];
        
        $pageDescription = new Page()->setPageModelFor('pdf', 'description');
        $descr = $cs->renderForPage($pageDescription);
        $caveData['description'] = $descr['attributes'];
        
        $caveData['raw'] = $cave->toArray();
        
        $privateStore = (string) realpath( config('filesystems.disks.local.root') );
        $publicStore = (string) realpath( config('filesystems.disks.public.root') );

        $fileOptions = [
            'allowedPaths' => [
                (string) realpath(sys_get_temp_dir()),
                $privateStore,
                $publicStore,
            ],
            /*
            'markupAllowedPaths' => [   
            ],
            */
        ];
       
        $pdf = new VarcaveTcpdf(
            'mm', // string $unit = 'mm',
            true, // bool $isunicode = true,);
            fileOptions: $fileOptions,
        );
        $pdf->build($caveData);
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

    public function getStaticMap(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        
        $cave = Cave::getByUuid($uuid);
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        $map = new StaticMapService($request->user(), $cave);

    }

    public function getMap(string $uuid, Request $request)
    {
        $cave = Cave::getByuuid($uuid);
        $cs = new CaveService($cave, $request->user(), CaveService::ADD_COORDS);
        
        $pageMain = new Page()->setPageModelFor('display', 'main', true);
        $caveData = $cs->renderForPage($pageMain);
        $sms = new StaticMapService($caveData);
        $sms->getmap();

    }

    public function caveEditPage(string $uuid, Request $request)
    {
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        //permit access to users with roles
        Gate::authorize('updateCave', $cave);

        $csOptions = CaveService::ADD_FILES;
        $cs = new CaveService($cave, $request->user(), $csOptions);

        $pageMain = new Page()->setPageModelFor('edit', 'main');
        $caveData = $cs->renderForPage($pageMain);

        $pageDescription = new Page()->setPageModelFor('edit', 'description');
        $caveDescription = $cs->renderForPage($pageDescription);

        $pageBiblio = new Page()->setPageModelFor('edit', 'bibliography');
        $caveBibliography = $cs->renderForPage($pageBiblio);

        //access and coords
        $csOptions = CaveService::ADD_COORDS;
        $cs = new CaveService($cave, $request->user(), $csOptions);
        $pageAccess = new Page()->setPageModelFor('edit', 'access');
        $caveAccess = $cs->renderForPage($pageAccess);



       //Files, if no files default to empty array
        if($caveData['caveFiles'] === null) $caveData['caveFiles'] = array();
        $caveFileList = ListValue::getByListName('cave_files.file_type', false);

        $caveDocsFiles = array();
        //reprocess file array to add some details
        foreach($caveData['caveFiles']  as $key => &$docTypes){
            foreach($docTypes as &$doc){
                $photosFilesExt = ['jpg','jpeg','png','webp'];
                $doc['extension'] = pathinfo($doc['file_path'], PATHINFO_EXTENSION);
                if(in_array($doc['extension'], $photosFilesExt))
                {
                    $doc['is_img'] = true;
                    
                }else{
                    $doc['is_img'] = false;
                    $doc['icon-class'] = Tools::getBiIcon($doc['extension']);
                }
            }
        }

        return view('varcave.caveupdate',
        [
            'pageTitle' => $caveData['attributes']['data']['name'],
            'caveObj' => $cave,
            'caveData' => $caveData,
            'caveDescription' => $caveDescription ?? '',
            'caveAccess' => $caveAccess ?? null,
            'caveBibliography' => $caveBibliography ?? null,
            'caveFiles' => $caveData['caveFiles'],
            'caveFileList' => $caveFileList,
            'changelog' => $cave->changelog,
            'fileTypeList' => ListValue::getByListName('cave_files.file_type'),
        ]);

    }

    public function updateCaveData(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        //permit access to users with roles
        Gate::authorize('updateCave', $cave); 

        $type = Field::where('key', $request->fieldname)->sole('data_type');

        //re-affect right validation rule
        switch($type->data_type){
            case 'bool':
                $dataType = 'boolean';
                break;
            
            case 'date':
                $dataType = 'date';
                break;
            
            case 'delimitedArray':
                $dataType = 'json';
                break;
            
            case 'number':
                $dataType = 'numeric';
                break;

            default:
                $dataType = 'string';
        }
        
        $validated = $request->validate([
            'fieldname' => ['required', 'string'],
            'value' => ['required', $dataType],
        ]);

        Log::info('Update cave '. $validated['fieldname'] . ' with value: '. Str::limit($validated['value'], 15));
        try
        {
            $f = $validated['fieldname'];
            $cave->$f = $validated['value'];
            $cave->save();

            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.settings.settings_saved'));
            $data = $validated['value'];
            $code = 200;
        }
        catch(Exception $e)
        {
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = Str::ucfirst(__('varcave.cave_update.save_fail') . '(' . $e->getMessage() . ')');
            $data = $cave->$f;
            $code = 500;
        }
        
        return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                $data,
                $code,
        );
    }

    public function addCoord(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        //permit access to users with roles
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'lon' => ['required', 'numeric'],
            'lat' => ['required', 'numeric'],
            'z' => ['required', 'numeric'],
        ]);

        try{
            $coord = CaveCoordinates::create([
                'cave_id' => $cave->id,
                'location' => DB::raw("POINT({$validated['lon']}, {$validated['lat']})"),
                'z' => $validated['z'],
            ]);

            $cs = new CaveService($cave, $request->user(), CaveService::ADD_COORDS);
            //instanciate "dummy" page since only coords are needed in this case
            $pageMain = new Page()->setPageModelFor('display', 'main', false);
            $caveData = $cs->renderForPage($pageMain);

            $result = null;
            foreach ($caveData['coordinates']['entrance'] as $entrance) {
                if ($entrance['id'] == $coord['id']) {
                    $result = $entrance;
                    break;
                }
            }

            $html = view('varcave.template.caveupdate.coord-wrapper', [
                'coord' => $result,
                'loopNbr' => false,
            ])->render();

            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.settings.settings_saved'));
            $data = $html;
            $code = 200;
        }
        catch(Exception $e){
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = Str::ucfirst(__('varcave.cave_update.save_fail') . ' (' . $e->getMessage() . ')');
            $data = '';
            $code = 500;
        }

        return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                $data,
                $code,
        );
        
    }

    public function destroyCoord(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        //permit access to users with roles
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'coord_id' => ['required', 'integer'],
        ]);
        
       
        $cave->load('caveCoordinates')->toArray();
        $coords = $cave->caveCoordinates->toArray();

        if (!in_array($validated['coord_id'], array_column($coords, 'id'))) {
            Log::warning($validated['coord_id'] . ' is not in cave coord set');
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = ('Coord id: ' . $validated['coord_id'] . ' is not related to cave ' . $cave->uuid);

            return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                data: $validated['coord_id'],
                code: 400,
            );
        }

        Log::info('Delete coord set: '. $validated['coord_id']);

        try{
            CaveCoordinates::destroy([
                $validated['coord_id']
            ]);
            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.cave_update.coord_deleted'));
            $code = 200;

        }catch(Exception $e){
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = Str::ucfirst(__('varcave.cave_update.coord_not_deleted'));
            $code = '500'; 
        }

        return VarcaveApiResponse::ajaxResponse(
            $success,
            $title,
            $msg,
            data: $validated['coord_id'],
            code: $code,
        );
    }

    public function updateCoord(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        //permit access to users with roles
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'lon' => ['required', 'numeric'],
            'lat' => ['required', 'numeric'],
            'z' => ['required', 'numeric'],
            'coordId' => ['required', 'integer'],
        ]);

        try{

            $coord = CaveCoordinates::findOrFail($validated['coordId']);
            
            $coord->update([
                'cave_id' => $cave->id,
                'location' => DB::raw("POINT({$validated['lon']}, {$validated['lat']})"),
                'z' => $validated['z'],
            ]);

            $cs = new CaveService($cave, $request->user(), CaveService::ADD_COORDS);
            //instanciate "dummy" page since only coords are needed in this case
            $pageMain = new Page()->setPageModelFor('display', 'main', false);
            $caveData = $cs->renderForPage($pageMain);

            $result = null;
            foreach ($caveData['coordinates']['entrance'] as $entrance) {
                if ($entrance['id'] == $coord['id']) {
                    $result = $entrance;
                    break;
                }
            }

            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.settings.settings_saved'));
            $data = $result;
            $code = 200;
        }
        catch(Exception $e){
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = Str::ucfirst(__('varcave.cave_update.save_fail') . ' (' . $e->getMessage() . ')');
            $data = '';
            $code = 500;
        }

        return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                $data,
                $code,
        );
    }

    public function destroyFile(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        //permit access to users with roles
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'fileId' => ['required', 'integer'],
        ]);

        Log::info('start deletion of file:'.$validated['fileId']);

        /*
         * Check that file is owned by cave
         */
        $cave->load('caveFiles');
        $isOwned = false;
        $targetFile = null;
        foreach($cave->caveFiles as $file)
        {
            $fArr = $file->toArray();
            Log::debug('find: '. $validated['fileId'].' in'.$fArr['id']);
            if ($validated['fileId'] == $fArr['id']){
                $isOwned = true;
                $targetFile = $file;
                break;
            }
        }

        if(!$isOwned){
            Log::error('File is not owned by cave');
            return redirect()
                ->back()
                ->with('error', __('varcave.cave_update.file_not_owned'));
        }

        $path = Storage::disk('public')->path($targetFile['file_path']);
        Log::info('Try to delete file: ' . $path);
        if(!Storage::disk('public')->exists($targetFile['file_path'])){
            Log::warning('File does not exists');
        }
        Storage::disk('public')->delete($targetFile['file_path']);
        Log::info('File deleted');
        
        //clear database
        $targetFile->delete();
        Log::info('File deleted from db');
        
        return redirect()
            ->back()
            ->with('success', Str::ucfirst(__('varcave.general.opSuccess')) . '. '. __('varcave.general.file_deleted'));
    }

    public function patchFile(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        //permit access to users with roles
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'file-note' => ['nullable', 'string'],
            'fileId' => ['required', 'integer'],
        ]);

        Log::info('update file note');
        /*
         * Check that CaveFile is owned by cave
         */
        $cave->load('caveFiles');
        $isOwned = false;
        $targetCaveFile = null;
        foreach($cave->caveFiles as $file)
        {
            $fArr = $file->toArray();
            Log::debug('find: '. $validated['fileId'].' in'.$fArr['id']);
            if ($validated['fileId'] == $fArr['id']){
                $isOwned = true;
                $targetCaveFile = $file;
                break;
            }
        }

        if(!$isOwned){
            Log::error('CaveFile is not owned by cave');
            return redirect()
                ->back()
                ->with('error', __('varcave.cave_update.note_not_owned'));
        }

        $targetCaveFile->file_note = $validated['file-note'] ?? '';
        $targetCaveFile->save();

        
        return redirect()
            ->back()
            ->with('success', Str::ucfirst(__('varcave.general.opSuccess')) . '. '. __('varcave.cave_update.note_updated'));
        
    }

    public function createFile(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        //permit access to users with roles
        Gate::authorize('updateCave', $cave);

        $authorizedCaveFilesTypes = json_decode(Setting::get('authorized_cave_file_type')) ;

        $validator = Validator::make($request->all(), [
            'new-file' => [
                'required',
                File::types($authorizedCaveFilesTypes),
            ],
            'file-group' => [
                'string',
                'max:64',
                'required',
            ],
            'file-note' => ['nullable', 'string', 'max:255'],
        ]);

        if($validator->fails()) {
            Log::debug('File validation fail',[$validator->errors()]);
            return redirect()->back()
                ->withErrors($validator, 'upload')
                ->withInput();
        }
        
        $validated = $validator->validated();

        try{
            Log::info('Cave file upload: validation complete');
            $file = $request->file('new-file');
            
            $fileNoExt = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $filename = Str::slug($fileNoExt) . '.' . $file->getClientOriginalExtension();
            
            $catName = CaveFile::folderCategory((int)$validated['file-group']);
            $baseDir = 'caves/'. $cave->uuid . '/' . $catName;
            $fullPath = $baseDir . '/' . $filename;

            Log::info('Add new file to: ' . $baseDir . '/'. $filename);
            if (Storage::disk('public')->exists($fullPath)) {
                Log::warning('File already exists: ' . $fullPath);
                
                // Interruption de l'opération
                throw new \Exception(__('varcave.general.file_already_exists'));
            }
            $path = $file->storeAs($baseDir, $filename, 'public');

            Log::info('File save succesfully');

            //create Resource
            CaveFile::create([
                'cave_id' => $cave->id,
                'file_type' => $catName,
                'file_path' => $path,
                'file_note' => $validated['file-note'],
                'created_at' => now(),
            ]);

            return redirect()
                ->back()
                ->with('success', Str::ucfirst(__('varcave.general.opSuccess') . ' ' . __('varcave.cave_update.file_added')));
        }
        catch(Exception $e){
            Log::error('File add failed:'.$e->getMessage());
            return redirect()
                ->back()
                ->with('error', Str::ucfirst(__('varcave.general.opFailed') . ': '. $e->getMessage())) ;
        }

        


    }

    public function copy(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        //permit access to users with roles
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'new-name' => ['required', 'string'],
            'new-ref'  => ['required', 'integer', 'unique:caves,cave_ref'],
        ]);

        try{
            Log::info('Start copy cave process');
            $excludedcopyfields = json_decode(Setting::get('excludedcopyfields'));
            
            //first create the basic
            $cpyData = array();
            foreach($cave->getAttributes() as $attr => $value){
                //skip attrib
                if(in_array($attr, $excludedcopyfields)){
                    continue;
                }
                $cpyData[$attr] = $value;
                 
            }
            $cpyData['name'] = $validated['new-name'];
            $cpyData['cave_ref'] = $validated['new-ref'];
            $cpyData['uuid'] = Str::uuid();
            $cpyData['created_at'] = now();

            $newCave = Cave::create($cpyData);

            //insert an empty coordinates set
            CaveCoordinates::add($newCave->id, 0, 0, 0);
            
            Log::info('Cave copied successfully');
            
            //redirect to new cave
            return redirect(route('varcave.caves.show', ['uuid' => $newCave->uuid]))
                ->with('success', __('varcave.caveshow.cave_copy_success') );

        }
        catch(Exception $e){
            return redirect()
                ->back()
                ->with('error', Str::ucfirst(__('varcave.general.opFailed')) . ' ' . $e->getMessage() );
        }
    }

    public function create(Request $request)
    {
        Log::debug(__METHOD__ . ' called.');

        //permit access to users with roles
        $cave = new Cave();
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'new-name' => ['required', 'string'],
            'new-ref'  => ['required', 'integer', 'unique:caves,cave_ref'],
        ]);

        try{

            Log::info('Start create cave process');
            
            $newCave = array();

            $newCave['name'] = $validated['new-name'];
            $newCave['cave_ref'] = $validated['new-ref'];
            $newCave['uuid'] = Str::uuid();
            $newCave['created_at'] = now();

            $newCave = Cave::create($newCave);

            //insert an empty coordinates set
            CaveCoordinates::add($newCave->id, 0, 0, 0);
            
            Log::info('Cave created successfully');
            
            //redirect to new cave
            return redirect(route('varcave.caves.show', ['uuid' => $newCave->uuid]))
                ->with('success', __('varcave.cave_update.cave_created') );

        }
        catch(Exception $e){
            return redirect()
                ->back()
                ->with('error', Str::ucfirst(__('varcave.general.opFailed')) . ' ' . $e->getMessage() );
        }
    }

    public function createChangelog(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'modification_note' => ['required', 'string'],
            'is_homepage_visible' => ['required', 'boolean'],
        ]);


        try
        {
            $user = $request->user();

            Log::info('Update cave changelog');
            $change = CaveChangelog::create([
                'cave_id' => $cave->id,
                'modification_note' => $validated['modification_note'],
                'author' => $user->firstname . ' ' . $user->lastname ,
                'is_homepage_visible' => $validated['is_homepage_visible'],
                'is_deleted' => 0,
            ]);

           $html = view(
                'components.varcave.caveupdate.tab-changehistory-chglog-item',
                compact('change')
            )->render();


            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.cave_update.changelog_added'));
            $data = $html;
            $code = 200;
        }
        catch(Exception $e)
        {
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = Str::ucfirst(__('varcave.cave_update.save_fail') . '(' . $e->getMessage() . ')');
            $data = 'null';
            $code = 500;
        }
        
        return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                $data,
                $code,
        );
    }

    public function updateChangelog(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'id' => ['required', 'exists:cave_changelogs,id'],
            'is_homepage_visible' => ['required', 'boolean'],
        ]);

        try
        {
            $changelog = $cave->changelog()
            ->where('id', $validated['id'])
            ->first();

            if (!$changelog) {
                Log::error('Changelog is not owned by cave');

                Throw new Exception( __('varcave.cave_update.changelog_not_owned'));
            }
            Log::info('Update cave changelog');
            
            $changelog->is_homepage_visible = $validated['is_homepage_visible'];
            $changelog->save();

            $response = [
                'id' => $changelog->id,
                'visibility' => $changelog->is_homepage_visible,
            ];

            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.cave_update.changelog_updated'));
            $data = $response;
            $code = 200;
        }
        catch(Exception $e)
        {
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = Str::ucfirst(__('varcave.cave_update.save_fail') . '(' . $e->getMessage() . ')');
            $data = 'null';
            $code = 500;
        }
        
        return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                $data,
                $code,
        );

    }

    public function createBibliography(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'text' => ['required', 'string'],
        ]);

        try
        {
            Log::info('Add new cave bibliography item');

            $bibliography = json_decode($cave->bibliography);
            
            $item = new \stdClass();
            $item->id = Str::ulid();
            $item->text = $validated['text'];
            $item->url = '';

            $bibliography[] = $item;
            
            $cave->bibliography = json_encode($bibliography, JSON_UNESCAPED_UNICODE);
            $cave->save();

            $html = view(
                'components.varcave.caveupdate.tab-bibliography-tag-item',
                compact('item')
            )->render();

            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.cave_update.bibliography_added'));
            $data = $html;
            $code = 200;
        }
        catch(Exception $e)
        {
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = Str::ucfirst(__('varcave.cave_update.save_fail') . '(' . $e->getMessage() . ')');
            $data = 'null';
            $code = 500;
        }
        
        return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                $data,
                $code,
        );
    }

    public function updateBibliography(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'id' => ['required', 'string'],
            'text' => ['required', 'string'],
            'url' => ['nullable', 'url:http,https'],
        ]);

        try
        {
            Log::info('Update cave bibliography item:' . $validated['id']);

            $bibliography = json_decode($cave->bibliography);
            
            //search if bibliography-id exists in json data
            $index = array_search($validated['id'], array_column($bibliography, 'id'), true);

            Log::debug('Given id is at json index: ' . $index);
            if ($index == false) {
                Log::debug('inexistant bibliography id');
                throw new Exception(__('varcave.cave_update.nonexistant_bibliography_id'));
            }

            $bibliography[$index] = [
                'id' => $validated['id'], //should not change :)
                'text' => $validated['text'],
                'url' => $validated['url'],
            ];
            
            $cave->bibliography = json_encode($bibliography, JSON_UNESCAPED_UNICODE);
            $cave->save();

            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.cave_update.bibliography_updated'));
            $data = $bibliography[$index];
            $code = 200;
        }
        catch(Exception $e)
        {
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = Str::ucfirst(__('varcave.cave_update.save_fail') . ' (' . $e->getMessage() . ')');
            $data = 'null';
            $code = 500;
        }
        
        return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                $data,
                $code,
        );
    }

    public function removeBibliography(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }
        Gate::authorize('updateCave', $cave);

        $validated = $request->validate([
            'id' => ['required', 'string'],
        ]);

        try
        {
            Log::info('Delete cave bibliography item:' . $validated['id']);

            $bibliography = json_decode($cave->bibliography);
            
            //search if bibliography-id exists in json data
            $index = array_search($validated['id'], array_column($bibliography, 'id'), true);

            Log::debug('Given id is at json index: ' . $index);
            if ($index === false) {
                Log::debug('inexistant bibliography id');
                throw new Exception(__('varcave.cave_update.nonexistant_bibliography_id'));
            }
           
            unset($bibliography[$index]);
            $bibliography = array_values($bibliography); //force reindex to prevent json corruption on concurent requests
            
            $cave->bibliography = json_encode($bibliography, JSON_UNESCAPED_UNICODE);
            $cave->save();

            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.cave_update.bibliography_deleted'));
            $data = $validated['id'];
            $code = 200;
        }
        catch(Exception $e)
        {
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = Str::ucfirst(__('varcave.cave_update.save_fail') . ' (' . $e->getMessage() . ')');
            $data = 'null';
            $code = 500;
        }
        
        return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                $data,
                $code,
        );
    }

    public function emailUpdateRequest(string $uuid, Request $request)
    {
        Log::debug(__METHOD__ . ' called.');
        $cave = Cave::getByuuid($uuid);
  
        if(!$cave)
        {
            abort(404, Str::ucfirst( __('varcave.general.caveNotFound') ) ); 
        }

        $validated = $request->validate([
            'name'              => ['required', 'string'],
            'mail-from'         => ['required', 'email:rfc'],
            'subject'           => ['required', 'string'],
            'body'              => ['required', 'string'],
            'sendCopyToUser'    => ['required', 'boolean'],
        ]);

        //add some more data for email preparation
        $validated['caveName'] = $cave->name;
        $validated['uuid'] = $cave->uuid;

        try
        {
            $recipients = json_decode(Setting::get('smtp_cave_update_recipients'));
            
            if($validated['sendCopyToUser']){
                $recipients[] = $validated['mail-from'];
            }

            Log::info('Send email on cave update request');
            $mail = new ContactFormMail($validated);
            Mail::to($recipients)->send($mail);

            $success = 'success';
            $title = Str::ucfirst(__('varcave.general.opSuccess'));
            $msg = Str::ucfirst(__('varcave.general.email_sent'));
            $data = null;
            $code = 200;
        }
        catch(Exception $e)
        {
            Log::error("Failure while sending email: \n" .  $e->getmessage());
            $success = 'fail';
            $title = Str::ucfirst(__('varcave.general.opFailed'));
            $msg = Str::ucfirst(__('varcave.general.email_not_sent') . '  (' . $e->getMessage() . ')');
            $data = 'null';
            $code = 500;
        }
        
        return VarcaveApiResponse::ajaxResponse(
                $success,
                $title,
                $msg,
                $data,
                $code,
        );


    }
}

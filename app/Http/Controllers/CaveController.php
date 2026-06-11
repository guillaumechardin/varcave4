<?php
namespace App\Http\Controllers;

use App\Models\Cave;
use App\Models\CaveStat;
use App\Models\CoordinateSystemHandler;
use App\Models\Page;
use App\Models\Setting;
use App\Services\CaveService;
use App\Services\GpxService;
use App\Services\StaticMapService;
use App\Services\VarcaveTcpdf;
use Com\Tecnick\Pdf\Tcpdf;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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

    public function getmap(string $uuid, Request $request )
    {
        $cave = Cave::getByuuid($uuid);
        $cs = new CaveService($cave, $request->user(), CaveService::ADD_COORDS);
        
        $pageMain = new Page()->setPageModelFor('display', 'main', true);
        $caveData = $cs->renderForPage($pageMain);
        $sms = new StaticMapService($caveData);
        $sms->getmap();

    }

    public function getPdftest(Request $request)
    {
        Log::debug(__METHOD__ . ' called.');

        $fontPath = storage_path('app/private/pdf/fonts');
		\define('K_PATH_FONTS', realpath($fontPath));

        $pdf = new Tcpdf(
            unit: 'mm',
            isunicode: true,
        );

        $docPageOrientation = ['P', 'L', 'P', 'P', 'P',];
        //$docPageOrientation = ['P', 'P', 'P', 'P', 'P',];
        //$docPageOrientation = ['L', 'L', 'L', 'L', 'L',];
        
        $margin = 0;

        foreach($docPageOrientation as $orientation){
            $pdf->addPage([
                'orientation' => $orientation,
                'format' => 'A4',
                'margin' => [
                    'CT' => $margin,
                    'PR' => $margin,
                    'CB' => $margin,
                    'PL' => $margin,
                    'HB' => $margin,
                    'FT' => $margin,
                ],
            ]);
            $font = $pdf->font->insert($pdf->pon, 'casualmemories', '', 12);
            $pdf->page->addContent($font['out']);

		    $pdf->page->addContent($pdf->color->getPdfColor('black'));

            $page = $pdf->page->getPage();

            $pdf->page->addContent($pdf->getTextLine(
                'This text is located at X:30, Y:30 on page ' . $page['pid'] +1,
                30,
                30,
            ));

             $pdf->page->addContent($pdf->getTextLine(
                'This text is centered  on page :' . $page['pid'] +1,
                $page['width'] /2,
                $page['height'] /2,
            ));

            $pdf->page->addContent($pdf->getTextLine(
                'Page dim: width/height:' . round($page['width'], 2) . '/'. round($page['height'],2),
                $page['width'] /2 ,
                $page['height'] /2 +20,
            ));
        }

        $pages = $pdf->page->getPages();

        $pageNumberPrefix  = 'Page';
        $x = 120;
        $y = 15;

        foreach ($pages as $page) {
            $totalPages = count($pages);
            $pageNumber = $page['pid'] + 1;
            $pdf->page->addContent($pdf->color->getPdfColor('red'), $page['pid']);
            if($page['orientation'] == 'P'){
                $pdf->addTextCellXY(
                    $pageNumberPrefix . ' : ' . $pageNumber . ' / ' . $totalPages . " ($x, $y)",
                    $page['pid'],
                    $x,
                    $y,
                    drawcell:false,
                );
            }
            else{
                $pdf->addTextCellXY(
                    $pageNumberPrefix . ' : ' . $pageNumber . ' / ' . $totalPages . " ($x, $y Orientation L)",
                    $page['pid'],
                    $x,
                    $y,
                    drawcell:false,
                );
            }

        }

        $rawpdf = $pdf->getOutPDFString();

        $pdf->renderPDF(rawpdf: $rawpdf);
    }
    

    public function getPdftest2(Request $request)
    {
        Log::debug(__METHOD__ . ' called.');

        $fontPath = storage_path('app/private/pdf/fonts');
		\define('K_PATH_FONTS', realpath($fontPath));

        $pdf = new Tcpdf(
            unit: 'mm',
            isunicode: true,
        );

        $font = $pdf->font->insert($pdf->pon, 'casualmemories', '', 10);
        
        $pdf->addPage([
			'orientation' => 'P',
			'format' => 'A4',
			'margin' => [
				'PL' => 10,
                'PR' => 10,
                'CT' => 10,
                'CB' => 10,
			],
            'region' => [
                [
                    'RX' => 20,
                    'RY' => 50,
                    'RW' => 190,
                    'RH' => 297.0 - 50,
                ],
            ],
		]);

        $pdf->page->addContent($font['out']);
        $lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
        $str='';
        for($i=0;$i<=50;$i++){
            $str .= $lorem . "\n";
        }

        $LINE_STYLE_DEFAULT = [
            'all'=> [
                'lineWidth' => 0.4,
                'lineCap' => 'butt',
                'lineJoin' => 'miter',
                'dashArray' => [],
                'dashPhase' => 0,
                'lineColor' => '#000000',
                'fillColor' => '',
                ]
        ];

        $firstPage = $pdf->page->getPage();
        $note = $pdf->getTextLine(
				'page id:' . $firstPage['pid'],
				10,
				10,
		);
		$pdf->page->addContent($note);


        $pdf->addTextCell(
			$str,// string $txt,
			-1, // int $pid = -1,
			0, // float $posx = 0,
			0, // float $posy = 0,
			0, // float $width = 0,
			0, // float $height = 0,
			0, // float $offset = 0,
			0, // float $linespace = 0,
			'T', // string $valign = 'T',
			'L', // string $halign = '',
			null, // ?array $cell = null,
			$LINE_STYLE_DEFAULT, // array $styles = [],
			0, // float $strokewidth = 0,
			0, // float $wordspacing = 0,
			0, // float $leading = 0,
			0, // float $rise = 0,
			true, // bool $jlast = true,
			true, // bool $fill = true,
			false, // bool $stroke = false,
			false, //bool $underline = false,
			false, //bool $linethrough = false,
			false, //bool $overline = false,
			false, // bool $clip = false,
			true, // bool $drawcell = true,
			'', // string $forcedir = '',
			null, // ?array $shadow = null,
		);

        $lastPage = $pdf->page->getPageID();
        $noteEnd1 = $pdf->getTextLine(
            'page id:' . $lastPage,
            10,
            15,
		);
		$pdf->page->addContent($noteEnd1);

        $lastPage2 = $pdf->page->getPage();
        $noteEnd2 = $pdf->getTextLine(
            'page id:' . $lastPage2['pid'],
            10,
            20,
		);
		$pdf->page->addContent($noteEnd2);

        $plastPage3 = $pdf->page->setCurrentPage();
        $noteEnd3 = $pdf->getTextLine(
            'page id:' . $plastPage3['pid'],
            10,
            25,
		);
		$pdf->page->addContent($noteEnd3);


        $noteEnd4 = $pdf->getTextLine(
            'Manual added line (should be added at the end of document',
            10,
            30,
		);
		$pdf->page->addContent($noteEnd4);

        $lastPage5 = $pdf->page->getPage();
        $noteEnd5 = $pdf->getTextLine(
            'page id:' . $lastPage5['pid'],
            10,
            35,
		);
		$pdf->page->addContent($noteEnd5);

        $nwStr = 'Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts. Separated they live in Bookmarksgrove right at the coast of the Semantics, a large language ocean. A small river named Duden flows by their place and supplies it with the necessary regelialia. It is a paradisematic country, in which roasted parts of sentences fly into your mouth. Even the all-powerful Pointing has no control about the blind texts it is an almost unorthographic life One day however a small line of blind text by the name of Lorem Ipsum decided to leave for the far World of Grammar. The Big Oxmox advised her not to do so, because there were thousands of bad Commas, wild Question Marks and devious Semikoli, but the Little Blind Text didn’t listen. She packed her seven versalia, put her initial into the belt and made herself on the way. When she reached the first hills of the Italic Mountains, she had a last view back on the skyline of her hometown Bookmarksgrove, the headline of Alphabet Village and the subline of her own road, the Line Lane. Pityful a rethoric question ran over her cheek, then .';
        
        $pdf->addTextCell(
			$str,// string $txt,
			-1, // int $pid = -1,
			0, // float $posx = 0,
			0, // float $posy = 0,
			0, // float $width = 0,
			0, // float $height = 0,
			0, // float $offset = 0,
			0, // float $linespace = 0,
			'T', // string $valign = 'T',
			'L', // string $halign = '',
			null, // ?array $cell = null,
			$LINE_STYLE_DEFAULT, // array $styles = [],
			0, // float $strokewidth = 0,
			0, // float $wordspacing = 0,
			0, // float $leading = 0,
			0, // float $rise = 0,
			true, // bool $jlast = true,
			true, // bool $fill = true,
			false, // bool $stroke = false,
			false, //bool $underline = false,
			false, //bool $linethrough = false,
			false, //bool $overline = false,
			false, // bool $clip = false,
			true, // bool $drawcell = true,
			'', // string $forcedir = '',
			null, // ?array $shadow = null,
		);


        $rawpdf = $pdf->getOutPDFString();

        $pdf->renderPDF(rawpdf: $rawpdf);
    }
}

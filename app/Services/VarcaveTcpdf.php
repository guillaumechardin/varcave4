<?php

namespace App\Services;

use App\Models\CoordinateSystemHandler;
use App\Models\Setting;
use Com\Tecnick\Pdf\Import\PageTemplateInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use proj4php\Point;
use proj4php\Proj4php;
use proj4php\Proj;
use LogicException;
use RuntimeException;

define('K_PATH_FONTS', realpath('../storage\app\private\pdf\fonts'));

class VarcaveTcpdf extends \Com\Tecnick\Pdf\Tcpdf
{

	/**
	 * path to the header image file
	 */	
	protected string $logoHeader = 'pdf/pdf_header_logo.png';
	
	/**
	 * enable/disable default header on top of page
	 */
	public bool $showHeader = true;
	
	/**
	 * Show footer on bottom of page
	 */
	public bool $ShowFooter = true;
 
    /**
	 * Handle page numbering on cave 1st page.
	 * If false a global pdf page number is used. Can be set by setpagegroup().
	 */
	protected bool $pagegroups = true;
	
    /**
     * Main cave data resources.
     */
    private ?array $cave = null;

    /**
     * cavedata is a shorthand to $cave[attributes] and a point in time copy
     */
    private ?array $cavedata = null;

    /**
     * Different font size for quick picking
     */
    public const int sizeXS  = 6;
	public const int sizeS   = 8;
	public const int sizeM   = 10;
	public const int sizeL   = 12;
	public const int sizeXL  = 16;
	public const int sizeXXL = 24;

	public const int sizeTitle1 = self::sizeXL;
	public const int sizeTitle2 = self::sizeL;
	public const int sizeSubtitle1 = self::sizeM;
	public const int sizeSubtitle2 = self::sizeXS;
	
	/**
	 * Default font family
	 */
	protected string $defaultFont = 'casualmemories';

	/**
	 * Default font color
	 */
	protected string $defaultFontColor = '#000000'; 

	/**
	 * default line style
	 */
	protected const  array LINE_STYLE_DEFAULT = [
		'lineWidth' => 0.4,
		'lineCap' => 'butt',
		'lineJoin' => 'miter',
		'dashArray' => [],
		'dashPhase' => 0,
		'lineColor' => '#000000',
		'fillColor' => '',
	];

	/**
	 * current Y position along document must be set with setY()
	 */
	private float $currentY = 0;

    /**
	 * current X position along sections must be set setX()
	 */
	private float $currentX = 0;

    /**
     * Margins definition, use setMargins(array $arr) to override
     */
    protected array $margins = [
		'top' => 7,
		'right' => 7,
		'bottom' => 7,
		'left'  => 7,
        'header_bottom' => 15,
        'footer_top' => 20,
	];

    /**
     * defaultTmplContent 
     */
    private ?PageTemplateInterface $defaultTmplContentP = null;
    private ?PageTemplateInterface $defaultTmplContentL = null;

    /**
     * Build a new pdf file for designed cave
     */
    public function build(array $cavedata){
        Log::debug('Start PDF initialization');
        Log::debug('Tcpdf will look for fonts into subfolders: ' . K_PATH_FONTS);
        $this->cavedata = $cavedata['attributes']['data'];
		$this->cave = $cavedata;

        $this->setCreator('tc-lib-pdf/varcave4');
		$this->setAuthor(Setting::get('pdf_author'));
		$this->setKeywords(Setting::get('keywords')); // *** must be from db
		
		//specific cave details
		$this->setSubject('cavité $cave'); // *** to be fixed
		$this->setTitle('$cave');// *** to be fixed
		
		

		//set filename from cave name	
		$this->setPDFFilename(Str::slug($this->cave['raw']['name'] . '.pdf') );

		// Insert font before addPage() so page context has a valid current font.
		$this->font->insert($this->pon, $this->getDefaultFont(), '', 9);
			

		//$this->buildTemplate();
        //$this->enableDefaultPageContent(true);
		
		$page01 = $this->addPage([
			'orientation' => 'P',
			'format' => 'A4',
			'margin' => [
				'PT' => $this->margins['top'],
				'PR' => $this->margins['right'],
				'PB' => $this->margins['bottom'],
				'PL' => 0,//$this->margins['left'],
				'HB' => 0,//$this->margins['header_bottom'],
				'FT' => 0,//$this->margins['footer_top'],
			],
		]);
		
        $page = $this->page->getPage();
        //dd($page);
		//$this->addCaveTitle(); //to first page
		//$this->addMainSection();
		$this->addBibliography();
		//$this->addAccess();
		
		//dd($this->cave);
		/*foreach($this->cave['caveFiles']['cave_maps'] as $file){
			$f = $file['file_path'];
			$this->addCaveMap($f, 12, 12);
		}*/
		

		/**
		 * Load data into PDF
		 */
		if(env('APP_DEBUG', true))
		{
			//$this->makeGrid();
		}
    }

	/**
	* build default page template. Call disable enableDefaultPageContent before : with enableDefaultPageContent(false) 
	*/
	private function buildTemplate()
	{
		$tmpl = new VarcaveTcpdf();
		$srcFont = $tmpl->font->insert($tmpl->pon, $this->getDefaultFont(), '', 14);

		$tmpl->addPage();
		$tmpl->page->addContent($srcFont['out']);
		$this->addHeader($tmpl);

		$this->setDefaultPageContent($tmpl->getOutPDFString()); //portrait

		$tmplL = new VarcaveTcpdf();
		$srcFont = $tmplL->font->insert($tmplL->pon, $this->getDefaultFont(), '', 14);

		$tmplL->addPage([
			'orientation' => 'L',
			'format' => 'A4',
		]);
		$tmplL->page->addContent($srcFont['out']);
		$this->addHeader($tmplL);

		$this->setDefaultPageContent($tmplL->getOutPDFString(), 'L'); //landscape
	}

	private function addHeader(VarcaveTcpdf $pdf)
	{
		/**
		 * Add logo header
		 */
		$logoHeader = Storage::disk('local')->path($this->logoHeader); 
		$img_00 = $pdf->image->add($logoHeader);
	
		//resize image to size
		$width_mm  = 17;
		$height_mm = 15;

		$page = $pdf->page->getPage();

		$img_00_out = $pdf->image->getSetImage($img_00, 4, 4, $width_mm, $height_mm, $page['height']);
		$pdf->page->addContent($img_00_out);


		//add pdf header title
		$pdf->setFont(size: $pdf::sizeXXL);
		$page = $pdf->page->getPage();

		$txt = $pdf->getTextLine(
			Str::upper(Setting::get('pdf_header_title')),
			22,
			11,
			0, //justification off
		);
		$pdf->page->addContent($txt);

		//small outline
		$lineStyle = $pdf->getLineStyle([
			'lineWidth' => 0.2,
			'lineColor' => '#acacac',
		]);
		$margins = $pdf->getMargins();
		$xEnd = floor($page['width'] - $margins['right']);
		$xStart = 20.5;
		$y = 12.5;

		$line = $pdf->graph->getLine($xStart, $y, $xEnd , $y, $lineStyle);
		$pdf->page->addContent($line);

		
	}

	private function addCaveTitle()
	{
		//$this->setColor('');
		$this->setDefaultCellPadding(1,1, 1,1);

		$cellStyle = [
			'all' => self::LINE_STYLE_DEFAULT,
		];	
		$this->setFont(
            size: self::sizeXXL,
            style: 'I',
        );
		$string = $this->cave['raw']['name'];
		
		$this->currentY = 13;

		$this->page->addContent($this->getTextCell($string, 25, $this->currentY, 0, 0, halign: 'L',  drawcell: false, styles: $cellStyle));

		$cellMetrics = $this->getLastCellBBox();

		$this->currentY += $cellMetrics['h'] ;
	}
		
	private function addMainSection(): void
	{
		$this->setFont(size: self::sizeTitle1);
		$this->setColor();

		$font = $this->font->getCurrentFont();
		$this->currentY += 7; //7mm margin bellow cave title
		
		$this->page->addContent(
            $this->getTextLine(
				__('varcave.pdf.speleometry'),
				8,
				$this->currentY,
				0, //justify text if this text width set. 0 = no justify
		    ),
            -1
        );

		//draw small outline
		$lineStyle = $this->getLineStyle([
			'lineWidth' => 0.4,
			'lineColor' => '#acacac',
		]);

		$page = $this->page->getPage();
		$margins = $this->getMargins();
		$xPageEnd = floor($page['width'] - $margins['right']);
		$xPageStart = 7;
		$this->currentY = $this->currentY + 0.5;

		$line = $this->graph->getLine($xPageStart, $this->currentY , $xPageEnd , $this->currentY , $lineStyle);
		$this->page->addContent($line);


		//3 column sheet
		$maxCol = 3;
		$itemPerCol = round(count($this->cavedata) / $maxCol, 0, PHP_ROUND_HALF_UP);
		
		$xColPadding = 2;
		$colDef = [];
		/**
		 * Compute column xStart/xEnd
		 */
		$colDef['usableWidth'] = $page['width'] - ($margins['right'] + $margins['left']);
		$colDef['colWidth'] = $colDef['usableWidth'] / $maxCol;

		$firstloop = true;
		$currentEnd = 0;
		for($i=0 ; $i < $maxCol; $i++){
			if($firstloop){
				$firstloop = false;

				$colDef['col'][] = [
					'xColStart' => $xPageStart,
					'xColEnd' => $xPageStart + $colDef['colWidth'],
					'xStart' => $xPageStart + $xColPadding,
					'xEnd' => $xPageStart + $colDef['colWidth'] - $xColPadding,
				];
				$currentEnd += $xPageStart + $colDef['colWidth'];
			}else{
				$colDef['col'][] = [
					'xColStart' => $currentEnd,
					'xColEnd' => $currentEnd + $colDef['colWidth'],
					'xStart' => $currentEnd + $xColPadding,
					'xEnd' =>$currentEnd + $colDef['colWidth'] - $xColPadding,
				];
				$currentEnd = $currentEnd + $colDef['colWidth'];
			}
		}

		$this->setFont(size: self::sizeM);
		$font = $this->font->getCurrentFont();
		$this->currentY += 5; //5mm margin bellow title
		
		$col  = $yOffset = 0;
		//process cave data horizontally
		//and change col numbering on each iteration
		foreach($this->cavedata as $key => $data){
			if ($col >= $maxCol){
				$col = 0;

				
				//check if multilined data do not overflow over next title 
				if ($this->currentY < ($this->currentY + $yOffset)){
					$this->currentY += $yOffset + ($font['ascent'] * 0.5);
				}
				else{
					$this->currentY += $font['ascent'];
				}
				$yOffset = 0;
			}

			//add item title
			$this->setFont(size: self::sizeM);
			$this->setColor('blue');
			$str = $key . ':';
			$keyItemSize = $this->measureText($str);
			$keyTxt = $this->getTextLine(
				Str::upper($str),
				$colDef['col'][$col]['xStart'],
				$this->currentY,
				0, //justification off
			);
			$this->page->addContent($keyTxt);
			
			//add item data
			$this->setFont(size: 9);
			$this->setColor('pink');;
			$dataTxt = $this->getTextCell(
				$data . "(x: ".$colDef['col'][$col]['xStart'] -1 . 'y:'.$this->currentY,
				$colDef['col'][$col]['xStart'] -1,
				$this->currentY,
				$colDef['colWidth'] * 0.9,
				halign: 'L',
				drawcell : true,
				styles: ['all'=> $this->getLineStyle(['fillColor' => '#0ffeee'])],
			);
			$this->page->addContent($dataTxt);
			$cellMetrics = $this->getLastCellBBox();
			$yOffset = max($yOffset, $cellMetrics['h']);

			$col++;
		}
	}

	private function addAccess(): void
	{
		$this->currentY += 12; //offset
		$this->setFont(size: self::sizeTitle1);
		$this->setColor();
		
		$description = $this->getTextLine(
				__('varcave.pdf.access'),
				8,
				$this->currentY,
		);
		$this->page->addContent($description);


		$lineStyle = $this->getLineStyle([
			'lineWidth' => 0.4,
			'lineColor' => '#acacac',
		]);

		$page = $this->page->getPage();
		$margins = $this->getMargins();
		$xPageEnd = floor($page['width'] - $margins['right']);
		$xPageStart = 7;
		$this->currentY += 1;

		$line = $this->graph->getLine($xPageStart, $this->currentY, $xPageEnd , $this->currentY, $lineStyle);
		$this->page->addContent($line);

		//add minimap
		$caveMap = new StaticMapService($this->cave);
		$caveMap->setTileSource('opentopomap');
		$mapfile = $caveMap->getmap();
		
		$this->currentY += 2;
		$page = $this->page->getPage();
		$mini_map = $this->image->add($mapfile);
		$mini_map_out = $this->image->getSetImage($mini_map, 12, $this->currentY, 60, 45, $page['height']);
		$this->page->addContent($mini_map_out);

		//add coordinates
		$this->setFont(size: self::sizeL);
		$font = $this->font->getCurrentFont();
		$x = 76;
		$this->currentY = 160 +5;

		$coord = $this->getTextLine(
				__('varcave.pdf.coordinates'). ': ',
				$x,
				$this->currentY,
			);
		$this->page->addContent($coord);
		
		$this->currentY += $font['descent'] * -2.5; //descent is neg


		$coordSystemPref = Setting::get('pdf_coords_system');
		$coordSystem = CoordinateSystemHandler::findOrFail($coordSystemPref);

		$proj4 = new Proj4php();
		//default projection as points stored in db
		$projWGS84  = new Proj('EPSG:4326', $proj4);
		
		$this->setFont(size: self::sizeM);
		$font = $this->font->getCurrentFont();
		//Add new CSR definition
		Log::debug('load epsg :' .$coordSystem['epsg_code']);
		//use a custom script if proj4_string is not set, transormation require some specific transforms
		if(!empty($coordSystem['proj4_string'])){
			$defName = 'EPSG:' . $coordSystem['epsg_code'];
			$proj4->addDef($defName, $coordSystem['proj4_string']);
			$dstCSR = new Proj($defName, $proj4);
			$useCustomFunc = false;
		}else{
			Log::debug('Use custom php handler for coordinate transform');
			$phpHandler = Storage::disk('local')->path($coordSystem['php_handler']); 
			if(!file_exists($phpHandler)){
				throw new \RuntimeException( 'PHP handler file not found: '. $phpHandler);
			}
			Log::debug('(' . $phpHandler . ')');
			require_once "$phpHandler";
			$useCustomFunc = $coordSystem['js_handler_fn'];
		}

		foreach($this->cave['coordinates']['entrance'] as $coords)
		{
			if(function_exists($useCustomFunc)){
				Log::debug('run custom handler:' . $useCustomFunc .'()');
				$pointDest = $useCustomFunc($coords['x'], $coords['y'], $proj4);
				if(!empty($pointDest['prefix']['name'])) $coordTxt = $pointDest['prefix']['name'] . ': ' . $pointDest['prefix']['value'];
				$coordTxt .= ' X: ' . floor($pointDest['x']) . '   Y: ' . floor($pointDest['y']);
				if(!empty($pointDest['suffix']['name'])) $coordTxt .= ' ' . $pointDest['suffix']['name'] . ': ' . $pointDest['suffix']['value'];
			}else{
				$pointSrc = new Point($coords['x'], $coords['y'], $projWGS84);
				$pointDest = $proj4->transform($dstCSR, $pointSrc);
				$coordTxt =  ' X:' . $pointDest->x . ' ' . ' Y:' . $pointDest->y;
			}
			
			$coord = $this->getTextLine(
				$coordTxt,
				$x,
				$this->currentY,
			);
			$this->page->addContent($coord);
			
			$this->currentY += $font['descent'] * -2.2; //descent is neg
		}
		
		//add text access informations
		$accessTxt = $this->getTextCell(
			$this->cave['accessTxt'],
			$x,
			$this->currentY,
			120,
			halign: 'L',
			drawcell : true,
			styles: ['all'=> $this->getLineStyle(['fillColor' => '#ffbb00'])],
		);
		$this->page->addContent($accessTxt);
		$cellMetrics = $this->getLastCellBBox();
		$this->currentY += $cellMetrics['h'];

		$lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
		// block of text between two page regions
		$this->addTextCell(
			$lorem . "\n" . $lorem . "\n" . $lorem . "\n" . $lorem. "\n" . $lorem,// string $txt,
			-1, // int $pid = -1,
			20, // float $posx = 0,
			$this->currentY +5, // float $posy = 0,
			170, // float $width = 0,
			0, // float $height = 0,
			15, // float $offset = 0,
			1, // float $linespace = 0,
			'T', // string $valign = 'T',
			'J', // string $halign = '',
			null, // ?array $cell = null,
			$this->getLineStyle(), // array $styles = [],
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
	}

	/**
	 * Add cave map on current page at defined position, Landscape or Portrait orientation on page is automatic
	 * @arg $x X position
	 * @arg $y X position
	 * @arg $caveMap Path to jpg or png file
	 * 
	 */
	private function addCaveMap(string $caveMap, float $x, float $y, int $pid = -1): void
	{
		$caveMap = Storage::disk('public')->path($caveMap); 
		if(! file_exists($caveMap)){
			throw new RuntimeException('Cave map file does not exists [' . $caveMap . ']');
		}

		[$width, $height] = getimagesize($caveMap);
		if ($height > $width) {
			$orientation = 'P';
		} else {
			$orientation = 'L';
		}

		
		$img = $this->image->add($caveMap);

		//try to do the best to fit image on page
		$page = $this->page->getPage();
		$isImagePortait = true;

		$margins = $this->getMargins();
	
		$this->addPage([
			'orientation' => 'L',//$orientation,
			'format' => 'A4',
			'margin' => [
				'PT' => $margins['top'],
				'PR' => $margins['right'],
				'PB' => $margins['bottom'],
				'PL' => $margins['left'],
				'HB' => $margins['header_bottom'],
				'FT' => $margins['footer_top'],
			],
		]);
	/*///////////
			$logoHeader = Storage::disk('local')->path($this->logoHeader); 
			$img_00 = $this->image->add($logoHeader);
		
			//resize image to size
			$width_mm  = 17;
			$height_mm = 15;

			$page = $this->page->getPage();

			$img_00_out = $this->image->getSetImage($img_00, 4, 4, $width_mm, $height_mm, $page['height']);
			$this->page->addContent($img_00_out);
	////////*/
		$page = $this->page->getPage($pid);
		$margins = $this->getMargins();

		$image_width_mm = $this->pxToMm($width);
		$image_height_mm = $this->pxToMm($height);

		$max_width = $page['width'] - $margins['right'] - $margins['left'];
		$max_height = $page['height'] - $margins['top'] - $margins['bottom'];

		/**
		 * Resize image if needed
		 */
		$image_width_mm  = $this->pxToMm($width);
		$image_height_mm = $this->pxToMm($height);

		// Default, no scaling
		$final_width_mm  = $image_width_mm;
		$final_height_mm = $image_height_mm;

		// Scale image if either side is more that total usable space
		if ($image_width_mm > $max_width || $image_height_mm > $max_height) {

			// facteur de réduction selon largeur et hauteur
			$ratio_width  = $max_width  / $image_width_mm;
			$ratio_height = $max_height / $image_height_mm;

			// on prend le plus petit ratio (le plus contraignant)
			$scale = min($ratio_width, $ratio_height);

			// application du scale (homothétie)
			$final_width_mm  = $image_width_mm * $scale;
			$final_height_mm = $image_height_mm * $scale;
		}


		

		$caveMap_out = $this->image->getSetImage($img, $x, $y, $final_width_mm, $final_height_mm, $page['height']);
		$this->page->addContent($caveMap_out);
	}

	private function addBibliography():void
	{
		$this->page->addContent($this->getTextLine(
				'This text is located at X:50, Y:100',
				30,
				30,
				0, //justify text if this text width set. 0 = no justify
		));


        $this->addTextCell(
			'This cell string is at x:150.5 y:100'  ,// string $txt,
			-1, // int $pid = -1,
			30, // float $posx = 0,
			30, // float $posy = 0,
			40, // float $width = 0,
			90, // float $height = 0,
			0, // float $offset = 0,
			0, // float $linespace = 0,
			'T', // string $valign = 'T',
			'L', // string $halign = '',
			null, // ?array $cell = null,
			['all'=> $this->getLineStyle(['fillColor' => '#e20ffe'])], // array $styles = [],
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
		return ;
		// no bib
		if(empty($this->cave['bibliography']['data']['bibliography']) ){
			return;
		}

		$margins = $this->getMargins();

		/** add section title **/
		$this->setFont(size: self::sizeTitle1);
		$this->setColor();
		$font = $this->font->getCurrentFont();
		$this->currentY += $font['ascent'];
		
		$sectionTitle = $this->getTextLine(
				$this->cave['bibliography']['model']['bibliography']['i18n_label'],
				$margins['left'],
				$this->currentY,
				0, //justify text if this text width set. 0 = no justify
		);
		$this->page->addContent($sectionTitle, -1);
		//draw small outline
		$lineStyle = $this->getLineStyle([
			'lineWidth' => 0.4,
			'lineColor' => '#acacac',
		]);

		$page = $this->page->getPage();
		$xPageStart = $margins['left'];
        $xPageEnd = floor($page['width'] - $margins['right']);
		$this->currentY += 0.5; //small padding for outline

		$line = $this->graph->getLine($xPageStart, $this->currentY , $xPageEnd , $this->currentY , $lineStyle);
		$this->page->addContent($line);

		
		/** add bib content **/
		$this->setFont(size: self::sizeM);
		$this->setColor();
		$font = $this->font->getCurrentFont();
		$this->currentY += $font['ascent'] *0.7;

		$this->page->addContent($this->getTextLine(
				$this->currentY,
				$margins['left'],
				$this->currentY,
				0, //justify text if this text width set. 0 = no justify
		));


		$str = implode("\r\n", $this->cave['bibliography']['data']['bibliography']);
		$this->addTextCell(
			'X:'.$margins['left'].'Y:'.$this->currentY . "\n" . trim($str)  ,// string $txt,
			-1, // int $pid = -1,
			$margins['left'], // float $posx = 0,
			$this->currentY, // float $posy = 0,
			halign: 'L',
            drawcell : true,
            styles: ['all'=> $this->getLineStyle(['fillColor' => '#0ffeee'])],
		);
	}

	function pxToMm(int $px, int $dpi = 96): float
	{
		return ($px * 25.4) / $dpi;
	}

	/**
	 * Force pdf download 
	 */
    public function render($filename = null)
    {
		$rawpdf = $this->getOutPDFString();
        if(!$filename)
        {
			//force pdf download with specidif filename
            $this->renderPDF($rawpdf);;
        }
        else
        {
            $this->downloadPDF($rawpdf);
        }
    }

	

	/**
	 * Insert a grid on pages to help debug on pdf creation page units 
     * must be set to mm to be a consistent grid
	 */
	public function makeGrid(): void
    {
		$pages = $this->page->getPages();
        Log::debug('Build grid for (' . count($pages) .' ) pages');
	
        foreach($pages as $page)
        {
            Log::debug('NEW PAGE: ' . $page['orientation']);
            //set font and lines styles
            $gridStyle = $this->getLineStyle([
                'lineWidth' => 0.15,
                'lineColor' => '#B5B5B5',//'gray',
            ]);
            $gridCell = 10;

            $xRight = $page['width'] - $page['margin']['PR'];
            $yBottom = $page['height'] - $page['margin']['PB'];
            

            $this->setFont(size: self::sizeS, pid: $page['pid']);	
            $this->setColor('red', $page['pid']);
        
            /*
            * H lines
            */
            for($y = $gridCell ; $y <= $page['height'] ; $y = $y + $gridCell)
            {
                //if($page['orientation'] == 'L'){dd([$this->margins['left'],$y]);}
                $line = $this->graph->getLine($this->margins['left'], $y, $xRight , $y, $gridStyle);
                $this->page->addContent($line, $page['pid']);
                Log::debug('draw H grid x/y: '. $y . '/' . $this->margins['left']);
                $lineNbr = $this->getTextLine(
                    $y,
                    $this->margins['left'],
                    $y,
                    0,
                );
                $this->page->addContent($lineNbr, $page['pid']);
            }

            /*
            * V lines
            */
            for($x = $gridCell ; $x <= $page['width'] ; $x += $gridCell)
            {
                Log::debug('draw V grid x/y: '. $x . '/' . $this->margins['top']);
                $line = $this->graph->getLine($x, $this->margins['top'], $x, $yBottom, $gridStyle);
                $this->page->addContent($line, $page['pid']);
                
                $lineNbr = $this->getTextLine(
                    $x,
                    $x,
                    $this->margins['top'] + 3, //3mm V offset
                    0,
                );
                $this->page->addContent($lineNbr, $page['pid']);
            }

        }
		
    }

	/**
     * Insert a pagination block on all pages.
     * Adds a page number label at the specified coordinates using the
     * configured page text prefix.
     *
     * Example output:
     *  - "Page : 1/5"
     *  - "Sheet : 2/12"
     *  - "Annex : 3/5"
     *
     * @param string $pageNumberPrefix Optional text displayed before the page number.
     *                        Default: "Page".
     * @param float  $x       Horizontal position of the pagination block.
     * @param float  $y       Vertical position of the pagination block.
     *
     * @return void
     */
	public function pagination(string $pageNumberPrefix = 'Page', float $x = 180.0, float $y = 180.0): void
	{
		
		$this->setColor('default');
		$this->setFont(size: self::sizeM);	

		$pages = $this->page->getPages();
		foreach ($pages as $pid => $page) {
			$totalPages = count($pages);
			$pageNumber = $pid + 1; // $pid index starts at 0

			$pageNbrTxt = $this->getTextLine(
				$pageNumberPrefix . ' : ' . $pageNumber . ' / '. $totalPages,
				$x,
			    $y,
				0, //justify text if this text width set. 0 = no justify
			);
			$this->page->addContent($pageNbrTxt, $pid);
		}
	}

	/**
	 *  Set current font
	 * exemple setFont('helvetica', 'B', 20);
	 */ 
	public function setFont( 
		?string $family = null, // font name
		int $size = self::sizeM,
		string $style = '',
		?float $spacing = null,
		?float $stretching = null,
        int $pid = -1): array
	{
		$family = $family ?? $this->defaultFont;

		$font = $this->font->insert($this->pon, $family, $style, $size, $spacing, $stretching);
		$this->page->addContent($font['out'], $pid);
		return $font;
	}


	/**
	 * Set font color to use. If empty string, use defaultFontColor()
	 * @arg $color 'black', grey, etc. or in hex format #aaaaaa
     * @arg $pid page id, default to -1
	 */
	public function setColor(string $color = '', int $pid = -1):void
	{
		//force default color if not set by user
		if( empty($color) || $color == 'default' ){
			$color = $this->defaultFontColor;
		}
		$pdfColor = $this->color->getPdfColor($color);
		$this->page->addContent($pdfColor, $pid);
	}


	protected function drawStyledCell(
		string $label,
		float $x,
		float $y,
		float $w,
		float $h,
		array $styles,
		int $borderPos,
        string $halign = 'L',
        string $valign = 'C',
        bool $drawcell = true,): void 
	{
		$this->setDefaultCellBorderPos($borderPos);

		$this->page->addContent($this->getTextCell(' ', $x, $y, $w, $h, styles: $styles, drawcell: $drawcell));

		$this->page->addContent($this->color->getPdfColor('black'));
		$this->page->addContent($this->getTextCell($label, $x, $y, $w, $h, valign: $valign , halign: $halign,  drawcell: false));
	}

	public function measureText(string $string): float
	{
		$ordarr = $this->uniconv->strToOrdArr($string);
		$widthPoints = $this->font->getOrdArrWidth($ordarr);
		$widthUserUnits = $this->toUnit($widthPoints);
		return $widthUserUnits;
	}

	public  function drawBoxedLine (
		array $style,
		string $text,
		float $x,
		float $y,
		float $w,
		float $h,
		float $spacing,
		float $stretching
	): void 
	{
		$this->page->addContent($this->graph->getRect($x, $y, $w, $h, 'D', $style));
        $font = $this->font->getCurrentFont();
		$this->page->addContent($this->getTextLine($text, $x, $y));
	}

	public function setDefaultFont(string $fontname)
	{
		$this->defaultFont = $fontname;
	}

    public function getDefaultFont()
	{
		return $this->defaultFont;
	}

	/**
	 * get default line style and return an array with overriden params if specified
	 * @var array{
	 *     lineWidth: float,
	 *     lineCap: 'butt'|'round'|'square',
	 *     lineJoin: 'miter'|'round'|'bevel',
	 *     dashArray: array<int, float>,
	 *     dashPhase: float,
	 *     lineColor: string,
	 *     fillColor: string
	 * }
	 */
	public function getLineStyle(array $override = []): array
	{
		return array_merge(self::LINE_STYLE_DEFAULT, $override);
	}

    /**
     * Sets the page common content like Header and Footer.
     * You must use setDefaultPageContent($sourceTemplate) before calling this method.
     */
    public function defaultPageContent(int $pid = -1): string
    {
        dd('you should not see me');
        if ($this->defaultTmplContentP === null || $this->defaultTmplContentL === null) {
            throw new LogicException('Default page template content is set use setDefaultPageContent()');
        }
        
        if ($pid < 0) {
            $pid = $this->page->getPageId();
        }

        $page = $this->page->getPage($pid);
        Log::debug('page orientation:'. $page['orientation']);

        $options = [
            //'keepAspectRatio' => true,
            //'align' => 'CC',
            //'clip' => true,
        ];
    
        if($page['orientation'] == 'P'){
            $template = $this->defaultTmplContentP;
        }else{
            $template = $this->defaultTmplContentL;
        }
        // Fill the whole destination page with the imported source page.
        $this->useImportedPage(
                $template,
                0.0,
                0.0,
                (float) $page['width'],
                (float) $page['height'],
                $options,
            );
        

        return '';
    }

    /**
     * Register source data and pick the page template to be used as default content.
     *
     * @param string $sourcePdfData Raw source PDF bytes.
     */
    public function setDefaultPageContent(string $sourcePdfData, string $orientation = 'P', int $sourcePageNum = 1): void
    {
        $sourceId = $this->setImportSourceData($sourcePdfData);
        $pageCount = $this->getSourcePageCount($sourceId);

        if (($sourcePageNum < 1) || ($sourcePageNum > $pageCount)) {
            throw new \InvalidArgumentException(
                'Requested source page is out of range. Available pages: ' . $pageCount
            );
        }

        if($orientation == 'P'){
            $this->defaultTmplContentP = $this->importPage($sourceId, $sourcePageNum, [
                'box' => 'CropBox',
                'cache' => true,
            ]);
        }else{
            $this->defaultTmplContentL = $this->importPage($sourceId, $sourcePageNum, [
                'box' => 'CropBox',
                'cache' => true,
            ]);
        }

    }



    /**
     * Set current page margins.
     *
     * This method must be called before invoking addPage().
     *
     * The given associative array may contain the following keys:
     *  - 'top'
     *  - 'right'
     *  - 'bottom'
     *  - 'left'
     *
     * Omitted keys keep their current values.
     *
     * @param array $newMargins Associative array of margin values.
     *
     * @return void
     */
    public function setMargins(array $newMargins): void
    {
        $arr = array_merge($this->margins, $newMargins);
        $this->margins = $arr;
    }

    public function getMargins(): array
    {
        return $this->margins ;
    }
}

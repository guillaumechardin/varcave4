<?php
namespace App\Services;

use App\Models\CoordinateSystemHandler;
use App\Models\Setting;
use App\Services\VarcaveTcpdf;
use Com\Tecnick\Pdf\Tcpdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use proj4php\Point;
use proj4php\Proj4php;
use proj4php\Proj;
use RuntimeException;

class CavePdfService
{
	/**
	 * root $pdf value object
	 */
    private VarcaveTcpdf $pdf;

    /**
     * Cave data
     */
    private array $cavedata; //shorthand of cave model
	private array $cave; //full cave model representation

	/**
	 * Font dir relative to storage dir
	 */
	private string $fontDir = 'app/private/pdf/fonts';

	/**
	 * path to the header image file
	 */	
	protected string $logoHeader = 'pdf/pdf_header_logo.png';

	/**
	 * current Y position along sections
	 */
	private float $currentY = 0;
	
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
	


    public function __construct(array $cavedata)
    {
        Log::debug('Start build PDF');
		
		$fontPath = storage_path($this->fontDir);
		\define('K_PATH_FONTS', realpath($fontPath));

		//A4 default format
        $this->pdf = new VarcaveTcpdf(
			'mm',
			true, //is unicode
		);
		$this->pdf->setCreator('tc-lib-pdf');
		$this->pdf->setAuthor(Setting::get('pdf_author'));
		$this->pdf->setKeywords(Setting::get('keywords')); // *** must be from db
		
		//specific cave details
		$this->pdf->setSubject('cavité $cave'); // *** to be fixed
		$this->pdf->setTitle('$cave');// *** to be fixed
		
		$this->cavedata = $cavedata['attributes']['data'];
		$this->cave = $cavedata;

		//set filename from cave name	
		$this->pdf->setPDFFilename(Str::slug($this->cave['raw']['name'] . '.pdf') );

		// Insert one neutral font before addPage() so page context has a valid current font.
		Log::debug('Tcpdf will look for fonts into subfolders: ' . K_PATH_FONTS);
		$this->pdf->font->insert($this->pdf->pon, $this->pdf->getdefaultFont(), '', 10, 0.0, 1.0);		

		$this->buildTemplate();

		$margins = $this->pdf->getMargins();
		$this->pdf->enableDefaultPageContent(true);
		$page01 = $this->pdf->addPage([
			'' => "",
			'margin' => [
				'PT' => $margins['top'],
				'PR' => $margins['right'],
				'PB' => $margins['bottom'],
				'PL' => $margins['left'],
				'HB' => $margins['header_bottom'],
				'FT' => $margins['footer_top'],
			],
		]);
		$this->pdf->setFont(size: $this->pdf::sizeXL);

		$this->addCaveTitle(); //to first page
		$this->addMainSection();
		$this->addBibliography();
		$this->addAccess();
		

		
		//dd($this->cave);
		foreach($this->cave['caveFiles']['cave_maps'] as $file){
			$f = $file['file_path'];
			$this->addCaveMap($f, 12, 12);
		}
		
		
		/* Add test pages
		for($i=0;$i<3;$i++)
		{
			$this->currentY = 35;
			$this->pdf->addPage([
			'margin' => [
				'PT' => $margins['top'],
				'PR' => $margins['right'],
				'PB' => $margins['bottom'],
				'PL' => $margins['left'],
				'HB' => $margins['header_bottom'],
				'FT' => $margins['footer_top'],
			],
		]);
			$this->addMainSection();
		}
			*/

		/**
		 * Load data into PDF
		 */
		if(env('APP_DEBUG', true))
		{
			$this->pdf->makeGrid();
		}
    }

	/**
	* build default page template. Call disable enableDefaultPageContent before : with enableDefaultPageContent(false) 
	*/
	private function buildTemplate()
	{
		$tmpl = new VarcaveTcpdf();
		$srcFont = $tmpl->font->insert($tmpl->pon, $this->pdf->getDefaultFont(), '', 14);

		$tmpl->addPage();
		$tmpl->page->addContent($srcFont['out']);
		$this->addHeader($tmpl);

		$this->pdf->setDefaultPageContent($tmpl->getOutPDFString()); //portrait

		$tmplL = new VarcaveTcpdf();
		$srcFont = $tmplL->font->insert($tmplL->pon, $this->pdf->getDefaultFont(), '', 14);

		$tmplL->addPage([
			'orientation' => 'L',
			'format' => 'A4',
		]);
		$tmplL->page->addContent($srcFont['out']);
		$this->addHeader($tmplL);

		$this->pdf->setDefaultPageContent($tmplL->getOutPDFString(), 'L'); //landscape
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
		$this->pdf->setDefaultCellPadding(1,1, 1,1);

		$cellStyle = [
			'all' => [
				'lineWidth' => 0.2,
				'lineCap' => 'butt',
				'lineJoin' => 'miter',
				'dashArray' => [],
				'dashPhase' => 0,
				'lineColor' => 'black',
				'fillColor' => '',
			],
		];	
		$this->pdf->setFont(style: 'I', size: 20 );
		$string = $this->cave['raw']['name'];
		
		$this->currentY = 13;

		$this->pdf->page->addContent($this->pdf->getTextCell($string, 25, $this->currentY, 0, 0, halign: 'L',  drawcell: false, styles: $cellStyle));

		$cellMetrics = $this->pdf->getLastCellBBox();

		$this->currentY += $cellMetrics['h'] ;
	}
		
	private function addMainSection(): void
	{
		$this->pdf->setFont(size: $this->pdf::sizeTitle1);
		$this->pdf->setColor();

		$font = $this->pdf->font->getCurrentFont();
		$this->currentY += 7; //3mm margin
		
		$description = $this->pdf->getTextLine(
				__('varcave.pdf.speleometry'),
				8,
				$this->currentY,
				0, //justify text if this text width set. 0 = no justify
		);
		$this->pdf->page->addContent($description, -1);

		//draw small outline
		$lineStyle = $this->pdf->getLineStyle([
			'lineWidth' => 0.4,
			'lineColor' => '#acacac',
		]);

		$page = $this->pdf->page->getPage();
		$margins = $this->pdf->getMargins();
		$xPageEnd = floor($page['width'] - $margins['right']);
		$xPageStart = 7;
		$this->currentY = $this->currentY + 0.5;

		$line = $this->pdf->graph->getLine($xPageStart, $this->currentY , $xPageEnd , $this->currentY , $lineStyle);
		$this->pdf->page->addContent($line);


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

		$this->pdf->setFont(size: $this->pdf::sizeM);
		$font = $this->pdf->font->getCurrentFont();
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
			$this->pdf->setFont(size: $this->pdf::sizeM);
			$this->pdf->setColor('blue');
			$str = $key . ':';
			$keyItemSize = $this->pdf->measureText($str);
			$keyTxt = $this->pdf->getTextLine(
				Str::upper($str),
				$colDef['col'][$col]['xStart'],
				$this->currentY,
				0, //justification off
			);
			$this->pdf->page->addContent($keyTxt);
			
			//add item data
			$this->pdf->setFont(size: 9);
			$this->pdf->setColor('pink');;
			$dataItemSize = $this->pdf->measureText($data);
			$dataTxt = $this->pdf->getTextCell(
				$data,
				$colDef['col'][$col]['xStart'] -1,
				$this->currentY,
				$colDef['colWidth'] * 0.9,
				halign: 'L',
				drawcell : true,
				styles: ['all'=> $this->pdf->getLineStyle(['fillColor' => '#0ffeee'])],
			);
			$this->pdf->page->addContent($dataTxt);
			$cellMetrics = $this->pdf->getLastCellBBox();
			$yOffset = max($yOffset, $cellMetrics['h']);

			$col++;
		}
	}

	private function addAccess(): void
	{
		$this->currentY += 12; //offset
		$this->pdf->setFont(size: $this->pdf::sizeTitle1);
		$this->pdf->setColor();
		
		$description = $this->pdf->getTextLine(
				__('varcave.pdf.access'),
				8,
				$this->currentY,
		);
		$this->pdf->page->addContent($description);


		$lineStyle = $this->pdf->getLineStyle([
			'lineWidth' => 0.4,
			'lineColor' => '#acacac',
		]);

		$page = $this->pdf->page->getPage();
		$margins = $this->pdf->getMargins();
		$xPageEnd = floor($page['width'] - $margins['right']);
		$xPageStart = 7;
		$this->currentY += 1;

		$line = $this->pdf->graph->getLine($xPageStart, $this->currentY, $xPageEnd , $this->currentY, $lineStyle);
		$this->pdf->page->addContent($line);

		//add minimap
		$caveMap = new StaticMapService($this->cave);
		$caveMap->setTileSource('opentopomap');
		$mapfile = $caveMap->getmap();
		
		$this->currentY += 2;
		$page = $this->pdf->page->getPage();
		$mini_map = $this->pdf->image->add($mapfile);
		$mini_map_out = $this->pdf->image->getSetImage($mini_map, 12, $this->currentY, 60, 45, $page['height']);
		$this->pdf->page->addContent($mini_map_out);

		//add coordinates
		$this->pdf->setFont(size: $this->pdf::sizeL);
		$font = $this->pdf->font->getCurrentFont();
		$x = 76;
		$this->currentY = 160 +5;

		$coord = $this->pdf->getTextLine(
				__('varcave.pdf.coordinates'). ': ',
				$x,
				$this->currentY,
			);
		$this->pdf->page->addContent($coord);
		
		$this->currentY += $font['descent'] * -2.5; //descent is neg


		$coordSystemPref = Setting::get('pdf_coords_system');
		$coordSystem = CoordinateSystemHandler::findOrFail($coordSystemPref);

		$proj4 = new Proj4php();
		//default projection as points stored in db
		$projWGS84  = new Proj('EPSG:4326', $proj4);
		
		$this->pdf->setFont(size: $this->pdf::sizeM);
		$font = $this->pdf->font->getCurrentFont();
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
			
			$coord = $this->pdf->getTextLine(
				$coordTxt,
				$x,
				$this->currentY,
			);
			$this->pdf->page->addContent($coord);
			
			$this->currentY += $font['descent'] * -2.2; //descent is neg
		}
		
		//add text access informations
		$accessTxt = $this->pdf->getTextCell(
			$this->cave['accessTxt'],
			$x,
			$this->currentY,
			120,
			halign: 'L',
			drawcell : true,
			styles: ['all'=> $this->pdf->getLineStyle(['fillColor' => '#ffbb00'])],
		);
		$this->pdf->page->addContent($accessTxt);
		$cellMetrics = $this->pdf->getLastCellBBox();
		$this->currentY += $cellMetrics['h'];

		$lorem = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
		// block of text between two page regions
		$this->pdf->addTextCell(
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
			$this->pdf->getLineStyle(), // array $styles = [],
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

		
		$img = $this->pdf->image->add($caveMap);

		//try to do the best to fit image on page
		$page = $this->pdf->page->getPage();
		$isImagePortait = true;

		$margins = $this->pdf->getMargins();
	
		$this->pdf->addPage([
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
			$img_00 = $this->pdf->image->add($logoHeader);
		
			//resize image to size
			$width_mm  = 17;
			$height_mm = 15;

			$page = $this->pdf->page->getPage();

			$img_00_out = $this->pdf->image->getSetImage($img_00, 4, 4, $width_mm, $height_mm, $page['height']);
			$this->pdf->page->addContent($img_00_out);
	////////*/
		$page = $this->pdf->page->getPage($pid);
		$margins = $this->pdf->getMargins();

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


		

		$caveMap_out = $this->pdf->image->getSetImage($img, $x, $y, $final_width_mm, $final_height_mm, $page['height']);
		$this->pdf->page->addContent($caveMap_out);
	}

	private function addBibliography():void
	{
		//$this->pdf->addPage();

		
		// no bib
		if(empty($this->cave['bibliography']['data']['bibliography']) ){
			return;
		}

		$margins = $this->pdf->getMargins();

		/** add section title **/
		$this->pdf->setFont(size: $this->pdf::sizeTitle1);
		$this->pdf->setColor();
		$font = $this->pdf->font->getCurrentFont();
		$this->currentY += $font['ascent'];
		
		$sectionTitle = $this->pdf->getTextLine(
				$this->cave['bibliography']['model']['bibliography']['i18n_label'],
				$margins['left'],
				$this->currentY,
				0, //justify text if this text width set. 0 = no justify
		);
		$this->pdf->page->addContent($sectionTitle, -1);
		//draw small outline
		$lineStyle = $this->pdf->getLineStyle([
			'lineWidth' => 0.4,
			'lineColor' => '#acacac',
		]);

		$page = $this->pdf->page->getPage();
		$xPageEnd = floor($page['width'] - $margins['right']);
		$xPageStart = $margins['left'];
		$this->currentY += 0.5; //small padding for outline

		$line = $this->pdf->graph->getLine($xPageStart, $this->currentY , $xPageEnd , $this->currentY , $lineStyle);
		$this->pdf->page->addContent($line);

		
		/** add bib content **/
		$this->pdf->setFont(size: $this->pdf::sizeM);
		$this->pdf->setColor();
		$font = $this->pdf->font->getCurrentFont();
		$this->currentY += $font['ascent'] *0.7;

		$this->pdf->page->addContent($this->pdf->getTextLine(
				$this->currentY,
				$margins['left'],
				$this->currentY,
				0, //justify text if this text width set. 0 = no justify
		));


		$str = implode("\r\n", $this->cave['bibliography']['data']['bibliography']);
		$this->pdf->addTextCell(
			$this->currentY . "\n" . trim($str)  ,// string $txt,
			-1, // int $pid = -1,
			$margins['left'], // float $posx = 0,
			$this->currentY, // float $posy = 0,
			0, // float $width = 0,
			0, // float $height = 0,
			0, // float $offset = 0,
			0, // float $linespace = 0,
			'T', // string $valign = 'T',
			'L', // string $halign = '',
			null, // ?array $cell = null,
			['all'=> $this->pdf->getLineStyle(['fillColor' => '#e20ffe'])], // array $styles = [],
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

	function pxToMm(int $px, int $dpi = 96): float
	{
		return ($px * 25.4) / $dpi;
	}

	/**
	 * Force pdf download 
	 */
    public function render($filename = null)
    {
		$rawpdf = $this->pdf->getOutPDFString();
        if(!$filename)
        {
			//force pdf download with specidif filename
            $this->pdf->renderPDF($rawpdf);;
        }
        else
        {
            $this->pdf->downloadPDF($rawpdf);
        }
    }
	
}

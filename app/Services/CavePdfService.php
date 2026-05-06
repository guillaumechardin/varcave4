<?php
namespace App\Services;

use App\Models\Setting;
use App\Models\Cave;
use Com\Tecnick\Pdf\Tcpdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CavePdfService
{
	/**
	 * root $pdf value object
	 */
    private Tcpdf $pdf;

    /**
     * Cave data
     */
    private array $cavedata; //shorthand of cave model
	private array $cavemodel; 

	/**
	 * Font dir realtive to stoarage dir
	 */
	private string $fontDir = 'app/private/pdf/fonts';

    /**
     * Different font size for quick picking
     */
    protected const int sizeXS  = 6;
	protected const int sizeS   = 8;
	protected const int sizeM   = 10;
	protected const int sizeL   = 12;
	protected const int sizeXL  = 16;
	protected const int sizeXXL = 24;

	protected const int sizeTitle1 = self::sizeXL;
	protected const int sizeTitle2 = self::sizeL;
	protected const int sizeSubtitle1 = self::sizeM;
	protected const int sizeSubtitle2 = self::sizeXS;
	
	/**
	 * Default font family
	 */
	protected string $defaultFont = 'casualmemories';

	/**
	 * Default font color
	 */
	protected string $defaultFontColor = '#000000'; //or 'black'

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
	
	/**
	 * Document margins in mm
	 */
	protected array $margins = [
		'top' => 7,
		'right' => 7,
		'bottom' => 7,
		'left'  => 7,
	];
	
	// Constants to process mm to px convertion
	// 1px = 0.264583333 mm
	// 1mm = 3.779527559 px
	const float PXTOMM = 0.264583333;
	const float MMTOPX = 3.779527559;


    public function __construct(array $cavedata)
    {
        Log::debug('Start build PDF');
		
		$fontPath = storage_path($this->fontDir);
		\define('K_PATH_FONTS', realpath($fontPath));

		//A4 default format
        $this->pdf = new Tcpdf(
			'mm',
			true, //is unicode

		);
		$this->pdf->setCreator('tc-lib-pdf');
		$this->pdf->setAuthor(Setting::get('pdf_author'));
		$this->pdf->setKeywords(Setting::get('keywords'));
		
		//specific cave details
		$this->pdf->setSubject('cavité $cave');
		$this->pdf->setTitle('$cave');
		
		$this->cavedata = $cavedata['attributes']['data'];
		$this->cavemodel = $cavedata;

		//set filename from cave name		
		$this->pdf->setPDFFilename(Str::slug($this->cavedata['name'] . '.pdf') );

		

		//A4 max available space
		$contentWidth = 210.0 - $this->margins['left'] - $this->margins['right'];
		$contentHeight = 297.0 - $this->margins['top'] - $this->margins['bottom'];

		// Insert one neutral font before addPage() so page context has a valid current font.
		Log::debug('Tcpdf will look for fonts into subfolders: ' . K_PATH_FONTS);
		$this->pdf->font->insert($this->pdf->pon, $this->defaultFont, '', 10, 0.0, 1.0);

		

		$page01 = $this->pdf->addPage([
			'margin' => [
				'PL' => $this->margins['left'],
				'PR' => $this->margins['right'],
				'CT' => $this->margins['top'],
				'CB' => $this->margins['bottom'],
			],
		]);

		/**
		 * Load data into PDF
		 */
		if(env('APP_DEBUG', true))
		{
			$this->makeGrid();
		}
		$this->setColor();

		$this->addHeader();
		
		$this->addCaveTitle(); //to first page

		$this->addMainSection(); //main cave details

		$this->addAccess(); //access informations and map
		
		//add pagination 
		$this->pagination();
		
			
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

	/**
	 * Insert a grid on page to help debug on pdf creation
	 */
	private function makeGrid($pid = -1)
    {
		$page = $this->pdf->page->getPage($pid);
	
		//$this->pdf->page->setPageWidth($page['width']);
		//$this->pdf->graph->setPageHeight($page['height']);

		$gridStyle = [
			'lineWidth' => 0.15,
			'lineCap' => 'butt',
			'lineJoin' => 'miter',
			'dashArray' => [],
			'dashPhase' => 0,
			'lineColor' => '#B5B5B5',//'gray',
			'fillColor' => '',
		];

		$gridCell = 10;
		$xRight = $page['width'] - $this->margins['right'];
		$yBottom = $page['height'] - $this->margins['bottom'];

		$this->setFont(size: self::sizeS);	
		$this->setColor('red');

		/*
		 * H lines
		 */
		for($y =  $gridCell ; $y <= $page['height'] ; $y = $y + $gridCell)
		{
			$line = $this->pdf->graph->getLine($this->margins['left'], $y, $xRight , $y, $gridStyle);
			$var[] = ''. $this->margins['left'] .', '. $y .', '. $xRight .', '. $y .', style';
			$this->pdf->page->addContent($line, $pid);
			
			$lineNbr = $this->pdf->getTextLine(
				$y,
				$this->margins['left'],
				$y,
				0,
			);
			$this->pdf->page->addContent($lineNbr, $pid);
		}

		/*
		 * V lines
		 */
		for($x =  $gridCell ; $x <= $page['width'] ; $x += $gridCell)
		{
			$line = $this->pdf->graph->getLine($x, $this->margins['top'], $x, $yBottom, $gridStyle);
			$var[] = ''. $x . ', ' . $this->margins['top'] . ', ' . $x . ', ' . $yBottom . ', style' ;
			$this->pdf->page->addContent($line, $pid);
			
			$lineNbr = $this->pdf->getTextLine(
				$x,
				$x,
				$this->margins['top'] + 3, //3mm V offset
				0,
			);
			$this->pdf->page->addContent($lineNbr, $pid);
		}
    }

	private function addHeader(int $pid = -1)
	{
		/**
		 * Add logo header
		 */
		$logoHeader = Storage::disk('local')->path($this->logoHeader); 
		$img_00 = $this->pdf->image->add($logoHeader);
	
		//resize image to size
		$width_mm  = 17;
		$height_mm = 15;

		$page = $this->pdf->page->getPage();

		$img_00_out = $this->pdf->image->getSetImage($img_00, 4, 4, $width_mm, $height_mm, $page['height']);
		$this->pdf->page->addContent($img_00_out, $pid);


		//add pdf header title
		$this->setFont(size: self::sizeXXL);
		$page = $this->pdf->page->getPage($pid);

		$txt = $this->pdf->getTextLine(
			Str::upper(Setting::get('pdf_header_title')),
			22,
			11,
			0, //justification off
		);
		$this->pdf->page->addContent($txt, $pid);

		//small outline
		$lineStyle = $this->getLineStyle([
			'lineWidth' => 0.2,
			'lineColor' => '#acacac',
		]);
		$xEnd = floor($page['width'] - $this->margins['right']);
		$xStart = 20.5;
		$y = 12.5;

		$line = $this->pdf->graph->getLine($xStart, $y, $xEnd , $y, $lineStyle);
		$this->pdf->page->addContent($line, $pid);

		
	}

	/**
	 * Insert pagination block on all pages
	 */
	private function pagination()
	{
		
		$this->setColor('default');
		$this->setFont(size: self::sizeM);	

		$pages = $this->pdf->page->getPages();
		foreach ($pages as $pid => $page) {
			$totalPages = count($pages);
			$pageNumber = $pid + 1; // $pid index starts at 0

				

			$pageNbrTxt = $this->pdf->getTextLine(
				Str::upper('Page') . ' : ' . $pageNumber . ' / '. $totalPages,
				180,
				10,
				0, //justify text if this text width set. 0 = no justify
			);
			$this->pdf->page->addContent($pageNbrTxt, $pid);

			$caveRefTxt = $this->pdf->getTextLine(
				Str::upper($this->cavedata['cave_ref']),
				150,
				10 ,
				0, //justify text if this text width set. 0 = no justify
			);
		}
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
		$this->setFont(style: 'I', size: 20 );
		$string = $this->cavedata['name'];
		
		$this->pdf->page->addContent($this->pdf->getTextCell($string, 25, 13, 0, 0, halign: 'L',  drawcell: false, styles: $cellStyle));
	}

	public function test2()
	{

		$style1 = [
			'lineWidth' => 0.3,
			'lineCap' => 'butt',
			'lineJoin' => 'miter',
			'dashArray' => [],
			'dashPhase' => 0,
			'lineColor' => 'grey',
			'fillColor' => 'powderblue',
		];
		$style = [
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
		//$line1 = $this->pdf->graph->getLine(21, 21, 121, 21, $style);
		//$this->pdf->page->addContent($line1);

		$sizes = ['XS', 'S', 'M', 'L', 'XL' , 'XXL' ];
		foreach ($sizes as $key => $size){
			$this->setFont(size: constant(self::class . '::size' . $size) );
			$string = 'This is a small string' . " $size";
			$textWidthUserUnits = round($this->measureText($string), 2);
			$string = $string . " ($textWidthUserUnits mm)";
			$this->pdf->page->addContent($this->pdf->getTextCell($string, 60, 20 +($key*10), 0, 0, halign: 'L',  drawcell: false));
		}		

	}


	public function test()
	{
		$fillStyle = [
			'lineWidth' => 0,
			'lineCap' => 'butt',
			'lineJoin' => 'miter',
			'dashArray' => [],
			'dashPhase' => 0,
			'lineColor' => '#ffffff',
			'fillColor' => '#ffffff',
		];        


		$styles = [
			'all' => $fillStyle,
			0 => [
				'lineWidth' => 0.508,
				'lineCap' => 'square',
				'lineJoin' => 'miter',
				'dashArray' => [],
				'dashPhase' => 0,
				'lineColor' => '#ff0000',
				'fillColor' => '#ff0000',
				
    		],
			1 => [
				'lineWidth' => 0,
				'lineCap' => 'square',
				'lineJoin' => 'miter',
				'dashArray' => [],
				'dashPhase' => 0,
				'lineColor' => '',
				'fillColor' => '',
				
    		],

			2 => [
				'lineWidth' => 0,
				'lineCap' => 'square',
				'lineJoin' => 'miter',
				'dashArray' => [],
				'dashPhase' => 0,
				//'lineColor' => '#f700ff',
				
    		],
			
			3 => [
				'lineWidth' => 0,
				'lineCap' => 'square',
				'lineJoin' => 'miter',
				'dashArray' => [],
				'dashPhase' => 0,
				//'lineColor' => '#000000',
				
    		],

		];


		$this->drawStyledCell(
			$this->pdf,
			$this->cavedata['name'] .   ' ' .  round($this->pdf->getStringWidth($this->cavedata['name']), 2),
			23,
			18,
			95,
			15,
			$styles,
			$this->pdf::BORDERPOS_DEFAULT
		);
	}
		
	private function addMainSection(): void
	{
		$this->setFont(size: self::sizeTitle1);
		$this->setColor();
		
		$description = $this->pdf->getTextLine(
				__('varcave.pdf.speleometry'),
				8,
				28,
				0, //justify text if this text width set. 0 = no justify
		);
		$this->pdf->page->addContent($description, -1);

		//draw small outline
		$lineStyle = $this->getLineStyle([
			'lineWidth' => 0.4,
			'lineColor' => '#acacac',
		]);

		$page = $this->pdf->page->getPage();
		$xPageEnd = floor($page['width'] - $this->margins['right']);
		$xPageStart = 7;
		$y = 28.5;

		$line = $this->pdf->graph->getLine($xPageStart, $y, $xPageEnd , $y, $lineStyle);
		$this->pdf->page->addContent($line);


		//3 column sheet
		$maxCol = 3;
		$itemPerCol = round(count($this->cavedata) / $maxCol, 0, PHP_ROUND_HALF_UP);
		
		$xColPadding = 2;
		$colDef = [];
		/**
		 * Compute column xStart/xEnd
		 */
		$colDef['usableWidth'] = $page['width'] - ($this->margins['right'] + $this->margins['left']);
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
		$font = $this->pdf->font->getCurrentFont();
		//dd($font);
		
		$col = 0;
		$currentY = 33;
		//process cave data horizontally
		foreach($this->cavedata as $key => $data){
			if ($col >= $maxCol){
				$col = 0;
				$currentY = $currentY + $font['ascent'];
			}

			//add item title
			$this->setFont(size: self::sizeM);
			$this->setColor('blue');
			$str = $key . ':';
			$keyItemSize = $this->measureText($str);
			$keyTxt = $this->pdf->getTextLine(
				Str::upper($str),
				$colDef['col'][$col]['xStart'],
				$currentY,
				0, //justification off
			);
			$this->pdf->page->addContent($keyTxt);
			
			//add item data
			$this->setFont(size: 9);
			$this->setColor('pink');;
			$dataItemSize = $this->measureText($data);
			$dataTxt = $this->pdf->getTextCell(
				$data,
				$colDef['col'][$col]['xStart'] -1,
				$currentY,
				$colDef['colWidth'] * 0.9,
				halign: 'L',
			);
			$this->pdf->page->addContent($dataTxt);

			$col++;
		}
		//set Y internals counters
		$this->currentY = $currentY;
		

		/**
		 * Unneeded part
		 *
			$style = [
				'lineWidth' => 2,
				'lineCap' => 'butt',
				'lineJoin' => 'miter',
				'dashArray' => [],//[5, 2, 1, 2],
				'dashPhase' => 0,
				'lineColor' => 'blue',
				'fillColor' => 'blue',
			];

			$style5 = [
				'lineWidth' => 0.25,
				'lineCap' => 'butt',
				'lineJoin' => 'miter',
				'dashArray' => [],
				'dashPhase' => 0,
				'lineColor' => 'gray',
				'fillColor' => '',
			];
			$line1 = $this->pdf->graph->getLine(10, 10, 200 , 10, $style5);
			$line2 = $this->pdf->graph->getLine(10, 40, 170 , 130, $style);
			$this->pdf->page->addContent($line1);
			$this->pdf->page->addContent($line2);
		*/

	}

	private function addAccess(): void
	{
		$this->currentY += 10; //offset
		$this->setFont(size: self::sizeTitle1);
		$this->setColor();
		
		$description = $this->pdf->getTextLine(
				__('varcave.pdf.access'),
				8,
				$this->currentY,
		);
		$this->pdf->page->addContent($description);


		$lineStyle = $this->getLineStyle([
			'lineWidth' => 0.4,
			'lineColor' => '#acacac',
		]);

		$page = $this->pdf->page->getPage();
		$xPageEnd = floor($page['width'] - $this->margins['right']);
		$xPageStart = 7;
		$this->currentY += 1;

		$line = $this->pdf->graph->getLine($xPageStart, $this->currentY, $xPageEnd , $this->currentY, $lineStyle);
		$this->pdf->page->addContent($line);

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
		?float $stretching = null): array
	{
		$pdf = $this->pdf;
		$family = $family ?? $this->defaultFont;

		$font = $pdf->font->insert($pdf->pon, $family, $style, $size, $spacing, $stretching);
		$pdf->page->addContent($font['out']);
		return $font;
	}


	/**
	 * set font color in format
	 * @arg $color 'black', grey, etc. or in hex format #aaaaaa
	 */
	public function setColor(string $color = ''):void
	{
		//force default color if not set by user
		if( empty($color) || $color == 'default' ){
			$color = $this->defaultFontColor;
		}
		$pdf = $this->pdf;
		$pdfColor = $pdf->color->getPdfColor($color);
		$pdf->page->addContent($pdfColor);
	}


	public function drawStyledCell(
		Tcpdf $pdf,
		string $label,
		float $x,
		float $y,
		float $w,
		float $h,
		array $styles,
		int $borderPos): void 
	{
		$pdf->setDefaultCellBorderPos($borderPos);

		$pdf->page->addContent($pdf->getTextCell(' ', $x, $y, $w, $h, styles: $styles, drawcell: true));

		$pdf->page->addContent($pdf->color->getPdfColor('black'));
		$pdf->page->addContent($pdf->getTextCell($label, $x, $y, $w, $h, valign: 'C', halign: 'L',  drawcell: false));
	}

	public  function measureText(string $string): float
	{
		$pdf = $this->pdf;
		$ordarr = $pdf->uniconv->strToOrdArr($string);
		$widthPoints = $pdf->font->getOrdArrWidth($ordarr);
		$widthUserUnits = $pdf->toUnit($widthPoints);
		return $widthUserUnits;
	}

	public  function drawBoxedLine (
		Tcpdf $pdf,
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
		$pdf->page->addContent($pdf->graph->getRect($x, $y, $w, $h, 'D', $style));
		$baseline = $y + 1.5 + $pdf->toUnit($font['ascent']);
		$pdf->page->addContent($pdf->getTextLine($text, $x + 1.5, $baseline));
	}

	public function setDefaultFont(string $fontname)
	{
		$this->defaultFont = $fontname;
	}

	public function measureCellHeight($text, $width)
	{
		$pdf = $this->pdf;
		$ordarr = $pdf->uniconv->strToOrdArr($text);


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

}

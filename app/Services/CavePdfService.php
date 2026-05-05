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
    private $pdf;

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
    private const int sizeXS  = 6;
	private const int sizeS   = 8;
	private const int sizeM   = 10;
	private const int sizeL   = 12;
	private const int sizeXL  = 16;
	private const int sizeXXL = 24;

	private const int sizeTitle1 = self::sizeXL;
	private const int sizeTitle2 = self::sizeL;
	private const int sizeSubtitle1 = self::sizeM;
	private const int sizeSubtitle2 = self::sizeXS;
	
	/**
	 * Default font
	 */
	protected string $defaultFont = 'dejavusans';
	
	/**
	 * path to the header image file
	 */	
	protected string $headerImg = null;
	
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
	 * document margins in mm
	 */
	protected array $margins = [
		'top' => 10,
		'right' => 10,
		'bottom' => 10,
		'left'  => 10,
	];
	
	// Constants to process mm to px convertion
	// 1px = 0.264583333 mm
	// 1mm = 3.779527559 px
	const float PXTOMM = 0.264583333;
	const float MMTOPX = 3.779527559;


    public function __construct(array $cavedata)
    {
        Log::debug('Start build PDF');
		//require_once ('/usr/share/php/Com/Tecnick/Barcode/autoload.php');
		
		$fontPath = storage_path($this->fontDir);
		define('K_PATH_FONTS', $fontPath);

        $this->pdf = new Tcpdf(
			'mm',
			true, //is unicode

		);
		$this->pdf->setCreator('tc-lib-pdf');
		$this->pdf->setAuthor('Comité Départemental de Spéléologie du Var');
		$this->pdf->setSubject('cavité $cave');
		$this->pdf->setTitle('$cave');
		$this->pdf->setKeywords('CDS83');
		$this->pdf->setPDFFilename('mycave.pdf');

		$this->cavedata = $cavedata['attributes']['data'];
		$this->cavemodel = $cavedata;

		$this->fontTitle1 = $this->pdf->font->insert($this->pdf->pon, 'casualmemories', 'B', 22);
		$this->fontTitle2 = $this->pdf->font->insert($this->pdf->pon, 'dejavusans', 'B', 22);
		$this->fontTextNormal = $this->pdf->font->insert($this->pdf->pon, 'casualmemories', '', CavePdfService::sizeM);
		$this->fontTextSmall = $this->pdf->font->insert($this->pdf->pon, 'dejavusans', '', CavePdfService::sizeXS);

		//A4 max available space
		$contentWidth = 210.0 - $this->margins['$left'] - $this->margins['$right'];
		$contentHeight = 297.0 - $this->margins['$top'] - $this->margins['$bottom'];

		$page01 = $this->pdf->addPage([
			'margin' => [
				'PL' => $this->margins['$left'],
				'PR' => $this->margins['$right'],
				'CT' => $this->margins['$top'],
				'CB' => $this->margins['bottom'],
			],
		]);
		$this->addHeader();
		$this->test2();
		



		//add pagination 
		$this->setPagination();
		
		
		
		/* test image
		$this->pdf->setBookmark('Images', '', 0, -1, 0, 0, 'B', 'blue');

		$headerPng = Storage::disk('local')->path('pdf/pdfheader.png');
		
		$iid00 = $this->pdf->image->add($headerPng);
	
		$width_mm  = 200;
		$height_mm = 9 ;

		$iid00_out = $this->pdf->image->getSetImage($iid00, 4, 4, $width_mm, $height_mm, $page01['height']);
		$this->pdf->page->addContent($iid00_out);
		*/

		if(env('APP_DEBUG', true))
		{
			$this->makeGrid();
		}
	
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
		$this->pdf->page->addContent($this->fontTitle1['out'], $pid);
		$page = $this->pdf->page->getPage($pid);
	
		$this->pdf->graph->setPageWidth($page['width']);
		$this->pdf->graph->setPageHeight($page['height']);

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

		//set font property
		$this->pdf->page->addContent($this->fontTextSmall['out'], $pid);	
		$color = $this->pdf->color->getPdfColor('red');
		$this->pdf->page->addContent($color, $pid);

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
				$this->margins['top'],
				0,
			);
			$this->pdf->page->addContent($lineNbr, $pid);
		}
    }

	private function addHeader(int $pid = -1)
	{
		//set font style
		$this->setFont($this->pdf, 'casual')
		$page = $this->pdf->page->getPage($pid);

		$txt = $this->pdf->getTextLine(
			Str::upper('fichier des cavités du Var'), // ******* TO BE FIXED FROM DATABASE ********
			20,
			11,
			0, //justification off
		);
		$this->pdf->page->addContent($txt, $pid);

		/**
		 * Add image header
		 */
		$headerPng = Storage::disk('local')->path('img/logo_cds83.png'); // ******* TO BE FIXED FROM DATABASE ********
		
		$img_00 = $this->pdf->image->add($headerPng);
	
		$width_mm  = 17;// 200;
		$height_mm = 15; // ;

		$img_00_out = $this->pdf->image->getSetImage($img_00, 4, 4, $width_mm, $height_mm, $page['height']);
		$this->pdf->page->addContent($img_00_out, $pid);
	}

	/**
	 * Insert pagination block on all pages
	 */
	private function setPagination()
	{
		$pages = $this->pdf->page->getPages();
		
		foreach ($pages as $pid => $page) {
			$totalPages = count($pages);
			$pageNumber = $pid + 1; // $pid index start at 0

			$this->pdf->page->addContent($this->fontTextNormal['out'], $pid);
			
			$color = $this->pdf->color->getPdfColor('black');
			$this->pdf->page->addContent($color, $pid);

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
					10 + self::sizeXS ,
					0, //justify text if this text width set. 0 = no justify
			);
		}
	}

	private function addCaveTitle()
	{
		$col = $this->pdf->color->getPdfColor('black');
		$this->pdf->page->addContent($col);
		$this->pdf->setDefaultCellPadding(2,2,2,2);

		/*$titleRect = $this->pdf->graph->getRoundedRect(15, 25, 40, 30, 6.50, 6.50, '1000', 'b');
		$this->pdf->page->addContent($titleRect);
		*/
		$cellStyle = [
			'all' => [
				'lineWidth' => 1,
				'lineCap' => 'round',
				'lineJoin' => 'round',
				'miterLimit' => 1,
				'dashArray' => [],
				'dashPhase' => 0,
				'lineColor' => 'black',
				'fillColor' => 'yellow',
			],
		];
		$this->pdf->setDefaultCellBorderPos(1);
		$cellTitle = $this->pdf->getTextCell(
			$this->cavedata['name'], // string $txt,
			25, // float $posx = 0,
			15, // float $posy = 0,
			0, // float $width = 0,
			0, // float $height = 0,
			0, // float $offset = 0,
			0, // float $linespace = 0,
			'C', // string $valign = 'C',
			'C', // string $halign = 'C',
			null, // ?array $cell = null,
			$cellStyle, // array $styles = [],
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
			null // ?array $shadow = null,
		);
		$this->pdf->page->addContent($cellTitle);

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
			$this->setFont($this->pdf, 'casualmemories', size: constant(self::class . '::size' . $size) );
			$string = 'This is a small string' . " $size";
			$textWidthUserUnits = round($this->measureText($this->pdf, $string), 2);
			$string = $string . " ($textWidthUserUnits mm)";
			$this->pdf->page->addContent($this->pdf->getTextCell($string, 60, 20 +($key*10), 0, 0, halign: 'L',  drawcell: false));
		}

		//$this->pdf->page->addContent($this->pdf->color->getPdfColor('black'));
		

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
		
	private function addMain()
	{
		/**
		 * Add right header part page number and cave ref
		 */
		//we start with a 3 column sheet
		$maxCol = 3;

		$this->pdf->page->addContent($this->fontTextNormal['out']);	
		$color = $this->pdf->color->getPdfColor('black');
		$this->pdf->page->addContent($color);
		
		$itemPerCol = round(count($this->cavedata) / $maxCol, 0, PHP_ROUND_HALF_UP);

		$col = 1;
		$itemCount = 1 ;

		foreach($this->cavedata as $data){
			if ($itemCount >= $itemPerCol)
			{
				$col++;
				$itemCount = 1;
			}

			//insert col title

			//insert caveData value
			$itemCount++;
		}

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

		
		$page03 = $this->pdf->addPage();
		

		
		//$this->pdf->page->addContent($txt);

	}

	/**
	 *  Set current font
	 * exemple setFont($pdf, 'helvetica', 'B', 20);
	 */
	public function setFont(
		Tcpdf $pdf,
		string $family, //font name
		string $style = '',
		int $size = self::sizeM,
		float $spacing = null, 
		float $stretching = null
	): array
	{
		$font = $pdf->font->insert($pdf->pon, $family, $style, $size, $spacing, $stretching);
		$pdf->page->addContent($font['out']);
		return $font;
	}


	public function drawStyledCell(
		Tcpdf $pdf,
		string $label,
		float $x,
		float $y,
		float $w,
		float $h,
		array $styles,
		int $borderPos
	): void 
	{
		$pdf->setDefaultCellBorderPos($borderPos);

		$pdf->page->addContent($pdf->getTextCell(' ', $x, $y, $w, $h, styles: $styles, drawcell: true));

		$pdf->page->addContent($pdf->color->getPdfColor('black'));
		$pdf->page->addContent($pdf->getTextCell($label, $x, $y, $w, $h, valign: 'C', halign: 'L',  drawcell: false));
	}

	public  function measureText(Tcpdf $pdf, string $string): array|float
	{
		$ordarr = $pdf->uniconv->strToOrdArr($string);
		$widthPoints = $pdf->font->getOrdArrWidth($ordarr);

		$widthUserUnits = $pdf->toUnit($widthPoints);
		
		return $widthUserUnits;
		return [
			$ordarr, 
			$width
		];
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

}

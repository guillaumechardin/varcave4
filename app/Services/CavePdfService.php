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
     * Cave data for main section
     */
    private $cavedata;

    /**
     * Different font size for quick picking
     */
    const sizeXS = 6;
	const sizeS = 8;
	const sizeM = 10;
	const sizeL = 12;
	const sizeXL = 14;
	const sizeXXL = 18;
	
	/**
	 * Default font
	 */
	protected $defaultFont = 'dejavusans';
	
	/**
	 * Font used in document. Can be change by setFont()
	 */
	protected $font = '';
	
	/**
	 * path to the header image file
	 */	
	protected $headerImg = null;
	
	/**
	 * enable/disable default header on top of page
	 */
	public $noheader = false;
	
	/**
	 * Show footer on bottom of page
	 */
	public $nofooter = true;
 
    
	/**
	 * Handle page numbering on cave 1st page.
	 * If false a global pdf page number is used. Can be set by setpagegroup().
	 */
	protected $pagegroups = true;
	
	
	/**
	 * document margins in mm
	 */
	protected $margins = [
		'top' => 10,
		'left'  => 10,
		'right' => 10,
		'bottom' => 10,
	];
	
	//some constants to process mm to px convertion
	// 1px = 0.264583333 mm
	// 1mm = 3.779527559 px
	const PXTOMM = 0.264583333;
	const MMTOPX = 3.779527559;
    

	private $fontTitle1;
	private $fontTitle2;
	private $fontTextNormal;
	private $fontTextSmall;


    public function __construct(array $cavedata)
    {
        Log::debug('Start build PDF');
		//require_once ('/usr/share/php/Com/Tecnick/Barcode/autoload.php');
		
		\define('K_PATH_FONTS', \realpath(__DIR__ . '/../../vendor/tecnickcom/tc-lib-pdf-font/target/fonts'));

        $this->pdf = new Tcpdf();
		$this->pdf->setCreator('tc-lib-pdf');
		$this->pdf->setAuthor('Comité Départemental de Spéléologie du Var');
		$this->pdf->setSubject('cavité $cave');
		$this->pdf->setTitle('$cave');
		$this->pdf->setKeywords('CDS83');
		$this->pdf->setPDFFilename('mycave.pdf');

		$this->cavedata = $cavedata;

		$this->fontTitle1 = $this->pdf->font->insert($this->pdf->pon, 'casualmemories', '', 22);
		$this->fontTitle2 = $this->pdf->font->insert($this->pdf->pon, 'dejavusans', '', 22);
		$this->fontTextNormal = $this->pdf->font->insert($this->pdf->pon, 'casualmemories', '', CavePdfService::sizeM);
		$this->fontTextSmall = $this->pdf->font->insert($this->pdf->pon, 'dejavusans', '', CavePdfService::sizeXS);


		$page01 = $this->pdf->addPage();
		$this->addHeader();
		$this->makeGrid();




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
			'lineColor' => 'gray',
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
		$this->pdf->page->addContent($this->fontTitle1['out'], $pid);
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
			
			$pageNumber = $pid + 1; // index commence à 0

			$this->pdf->page->addContent($this->fontTextNormal['out'], $pid);
			
			$color = $this->pdf->color->getPdfColor('orange');
			$this->pdf->page->addContent($color, $pid);

			$pageNbrTxt = $this->pdf->getTextLine(
			Str::upper('Page') . ' : ' . $pageNumber,
				150,
				10,
				0, //justify text if this text width set. 0 = no justify
			);
			$this->pdf->page->addContent($pageNbrTxt, $pid);

			
			$caveRefTxt = $this->pdf->getTextLine(
			Str::upper('cavité numéro') . ':: ' . 'XYZ123',
				150,
				10 + self::sizeXS ,
				0, //justify text if this text width set. 0 = no justify
			);
		}
	}
		
	private function addMain($cavedata)
	{
		/**
		 * Add right header part page number and cave ref
		 */
		//we start with a 3 column sheet
		$maxCol = 3;

		$this->pdf->page->addContent($this->fontTextNormal['out']);	
		$color = $this->pdf->color->getPdfColor('black');
		$this->pdf->page->addContent($color);
		
		$itemPerCol = round(count($cavedata) / $maxCol, 0, PHP_ROUND_HALF_UP);

		$col = 1;
		$itemCount = 1 ;

		foreach($cavedata as $data){
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


}

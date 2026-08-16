<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use ZipArchive;

class SpatialFileService
{
    /**
     * Pairs of file extensions and MIME types.
     */
    private const  PERMITTED_MIME_TYPES  = [
        'kml' => 'text/xml',//'application/vnd.google-earth.kml+xml,text/xml',
        'kmz' => 'application/zip',
        'geojson' => 'application/json',
    ];
    
    /**
     * Maximum spatial file size in KB.
     * Warning, this value will be stored in user session variable
     * to cache last spatial search request. Do not set it to a `large` value
     * see: SPATIAL_SEARCH_SESSION_TTL and middleware ClearExpiredSpatialSearch
     */
    public const MAX_SPATIAL_FILE_SIZE  = 350; 

    /**
     * Spatial search session lifetime in seconds before deletion
     */
    public const SPATIAL_SEARCH_SESSION_TTL = 300;
    
    
    /**
     * Permitted file extensions.
     */
    public array $filesExtensions = [];

    /**
     * Permitted file extensions.
     */
    public array $filesMimetypes = [];

    /**
     * File that will be used to build WKT string
     */
    private string $filePath = '';

    private string $originalExtension = '';
    private string $originalFileName= '';

    public function __construct()
    {
        Log::debug(__METHOD__ . ' called.');
        $this->filesExtensions = array_keys(self::PERMITTED_MIME_TYPES);
        $this->filesMimetypes = array_values(self::PERMITTED_MIME_TYPES);
    }

    public static function getPermitedMimeTypes()
    {
        return self::PERMITTED_MIME_TYPES;
    }

    public function buildWktFromFile(UploadedFile $file): string
    {
        Log::debug(__METHOD__ . ' called.');

        //Collect file details
        $this->filePath = $file->getRealPath(); //['realPath'];

        $this->originalExtension = $file->getClientOriginalExtension();
        $this->originalFileName = $file->getClientoriginalName();
        
        switch($this->originalExtension)
        {
            case 'kml':
            case 'kmz':
                Log::info('Process KML file');
                return $this->processFileKml();
            break;
            
            case 'geojson':
                Log::info('Process geojson file');
                return $this->processFileGeojson();
            break;

            default:
                Log::error('Unable to process file');
                throw new RuntimeException('Unknown file type: ' . $this->originalExtension);
        }
    }

    /**
     * 
     */
    public function processFileKml(): string
    {
        Log::debug(__METHOD__ . ' called.');
        if(mime_content_type($this->filePath) == self::PERMITTED_MIME_TYPES['kmz']){
            //kml file with Zip compression
            Log::info('Use zipped KMZ file');            
            $zip = new ZipArchive();

            if ($zip->open($this->filePath) === true) {
                $kmlContent = null;

                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $name = $zip->getNameIndex($i);

                    if (strtolower(pathinfo($name, PATHINFO_EXTENSION)) == 'kml') {
                        $kmlContent = $zip->getFromIndex($i);
                        break;
                    }
                }
                $zip->close();

                if ($kmlContent === null) {
                    throw new RuntimeException('No KML file found in the KMZ archive.');
                }
            }
            else{
                throw new RuntimeException('KMZ file corrupt');
            }
            
        }else {
            //simple kml file with no compression
            Log::info('Use simple kml file');            
            $kmlContent = file_get_contents($this->filePath);
        }

        /**
         * Check if file is MultiGeometry
         * Else we consider that is a simple polygon
         */
        $xml = simplexml_load_string($kmlContent); 
        
        //check if folder present keep only first one
        $data = null;
        if(isset($xml->Document->Folder)){
            Log::info('KML data is inside folder. processings folder and any subs');  
            $data = $this->recursiveKmlFolders($xml->Document);
        }
        else{
            Log::info('KML do not user folder');
            $data = $xml->Document; //no folder
        }
        

        //Merge Polygons into one array
        $polygonsGeometry = [];
        foreach($data as $key => $d){
            if(isset($d->MultiGeometry) ){
                Log::info('Use multiple geometry');
                foreach($d->MultiGeometry->Polygon as $p){
                    $polygonsGeometry[] = $p;
                }
            }elseif( isset($d->Polygon) ){
                Log::info('Use single geometry');
                foreach($d->Polygon as $p){
                    $polygonsGeometry[] = $p;
                }
            }
            else{
                throw new RuntimeException(__('varcave.spatial_search.no_polygon_found'));                
            }
        }         
        

        //loop over collected polygons to collect outer and inner (holes)
        $polygons =  [];
        Log::info('Found: ' . count($polygonsGeometry) . ' polygons in file');

        foreach($polygonsGeometry as $pkey => $p){
            $outer =  [];
            $outerPoints = [];
            $innerPoints = [];
            //outer ring bounderies
            $outerRing = trim((string)$p->outerBoundaryIs->LinearRing->coordinates); //only one outerBoundary

            //convert to of  `long lat` arrays
            foreach(preg_split('/\s+/', $outerRing) as $coordinate) { //only index 0 available
                [$longitude, $latitude] = explode(",", $coordinate);
                $outer[] = "{$longitude} {$latitude}";
            }
            
            //inner  ring bounderie
            if(isset($p->innerBoundaryIs[0]->LinearRing->coordinates) ){ //can be many  innerBoundaryIs check only the first
                $i=0;
                foreach($p->innerBoundaryIs as $ring){

                    $ringCoords = trim((string)$ring->LinearRing->coordinates); 
                    
                    //convert to `long lat` arrays
                    $inner = [];
                    foreach(preg_split('/\s+/', $ringCoords) as $coordinate) {
                        [$longitude, $latitude] = explode(",", $coordinate);
                        $inner[] = "{$longitude} {$latitude}";
                    }
                    $innerPoints[$i] = $inner;
                    $i++;
                }
            }
            $polygons[$pkey] = [
                'outer' => $outer,
                'inners' => $innerPoints,
            ];   
        }
        

        //convert to WKT
        $wktPolygons = [];
        foreach ($polygons as $polyId => $polygon) {
            $rings = [];

            // Outer ring
            $rings[] = $this->polygonsCoordinatesToWkt($polygon['outer']); //first ring is outer

            // Inner rings
            foreach ($polygon['inners'] as $inner) {
                $rings[] = $this->polygonsCoordinatesToWkt($inner);   // 2nd and later represents holes in polygon
                
            }
            
            //wktPolygons results are similar to
            /*
                0 => "(5.9121901390197 43.190147991026,5.9121910240984 43.19012059975)", //outer
                1 => "(5.8810700034542 43.161425669995,5.8811426549785 43.161383637121)" //subsequent holes
            */
            $wktPolygons[$polyId] = $rings;   
        }
            
        
        //dd($wktPolygons);

        if(count($wktPolygons) > 1){
            /**
             * MultiPolygon(
             *      (
             *          (0 0,0 3,3 3,3 0,0 0),
             *          (1 1,1 2,2 2,2 1,1 1)
             *      )
             * )';
             * 
             * 
             * 
                multipolygon syntax is MULTIPOLYGON( 
                                            ( (outerpoly1), (inner1)),
                                            ( (outerpoly2), (inner2)),
                                            [...]
                                    )
            */
            $wktString = "MULTIPOLYGON (\n";
            $pol = [];
            foreach($wktPolygons as $polygon){
                $pol[] =  '(' . implode(',', $polygon) . ')';
            }
            $wktString .= implode(",\n", $pol);
            $wktString .= ")"; //close multipolygon
        }else{
           /**
                polygon syntax is POLYGON( 
                                             (outerpoly1), (inner1),
                                    )
            */

            $wktString = "POLYGON (";
            foreach($wktPolygons as $polygon){
                $wktString .=  implode(",", $polygon);
            }
            $wktString .= ")  "; //close polygon
        }
        
        return $wktString;
    }

    private function polygonsCoordinatesToWkt(array $coordinates): string
    {
        return '(' . implode(',', $coordinates) . ')';
    }

    /**
     * Recursively collect all Placemark elements from a KML Folder tree.
     *
     * @param \SimpleXMLElement $element
     * @return array
     */
    private function recursiveKmlFolders(\SimpleXMLElement $element): array
    {
        $data = [];

        // Collect Placemarks directly contained in this element.
        foreach ($element->Placemark as $placemark) {
            $data[] = $placemark;
        }

        // Recursively process all child folders.
        foreach ($element->Folder as $folder) {
            $data = array_merge(
                $data,
                $this->recursiveKmlFolders($folder)
            );
        }

        return $data;
    }

    public function processFileGeojson(): string
    {
        Log::debug(__METHOD__ . ' called.');
        return '';
    }

}

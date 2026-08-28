<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use SimpleXMLElement;
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
    public const MAX_SPATIAL_FILE_SIZE  = 5000; 

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

    public function buildWktFromFile(UploadedFile $file): array
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
    public function processFileKml(): array
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
        Log::info('Collect all polygons data');
        $allPolygons = [];
        $this->kmlFindPolygons($xml, $allPolygons);

        if( count($allPolygons) == 0){
            throw new RuntimeException(__('varcave.spatial_search.no_polygon_found'));            
        }         

        //loop over collected polygons to collect outer and inner (holes)
        $_polygons =  [];
        Log::info('Found: ' . count($allPolygons) . ' polygons in file');

        /**
         * Convert long0,lat0,x0 long1,lat1,x1 ...
         *   to array
         * [long0, lat0], [long1, lat1], ...
         */
        Log::debug('Build polygons from kml 1st step');
        foreach($allPolygons as $pkey => $p){
            $outerPoints =  [];
            $innerPoints = [];
            
            //outer ring bounderies
            $outerRing = trim((string)$p->outerBoundaryIs->LinearRing->coordinates); //only one outerBoundary

            //max bounding box for this polygon
            $bbox = [
                'minLon' => INF,
                'maxLon' => -INF,
                'minLat' => INF,
                'maxLat' => -INF,
            ];

            //convert to of  `long lat` arrays
            foreach(preg_split('/\s+/', $outerRing) as $coordinate) { //only index 0 available
                //kml store coords like :
                //5.783855717638864,43.17411316578551,0 5.785481385503788,43.11241103046611,0
                [$longitude, $latitude] = explode(",", $coordinate);
                
                $outerPoints[] = "{$longitude} {$latitude}";
                
                //process bbox calculation
                $longitude = (float) $longitude;
                $latitude = (float) $latitude;
                $bbox['minLon'] = min($bbox['minLon'], $longitude);
                $bbox['maxLon'] = max($bbox['maxLon'], $longitude);

                $bbox['minLat'] = min($bbox['minLat'], $latitude);
                $bbox['maxLat'] = max($bbox['maxLat'], $latitude);
            }
            
            //inner  ring bounderie
            if(isset($p->innerBoundaryIs[0]->LinearRing->coordinates) ){ //can be many  innerBoundaryIs check only the first
                $i=0;
                foreach($p->innerBoundaryIs as $ring){

                    $ringCoords = trim((string)$ring->LinearRing->coordinates); 
                    
                    //convert to `long lat` arrays
                    $inner = [];
                    foreach(preg_split('/\s+/', $ringCoords) as $coordinate) {
                        [$longitude, $latitude] = explode(",", $coordinate); //6.25895512514388,43.261592645669
                        $inner[] = "{$longitude} {$latitude}";
                    }
                    $innerPoints[$i] = $inner;
                    $i++;
                }
            }
            $_polygons[$pkey] = [
                'outer'  => $outerPoints,
                'inners' => $innerPoints,
                'bbox'  => $bbox,
            ];   
        }
        Log::debug('Done building polygons from kml');
        
        //convert to WKT
        $wktPolygons = [];
        /**
         * Convert long/lat arrays [long0, lat0], [long1, lat1], ...
         *   to wkt arrays
         *  `(long0 lat0,  long1 lat1)`
         */
        Log::debug('Consolidate WKT from inner/outer');
        $polygons = [];
        foreach ($_polygons as $polyId => $polygon) {
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
                1 => "(5.8810700034542 43.161425669995,5.8811426549785 43.161383637121)" //subsequent inner holes
            */
            //dd($rings);
            //$wktPolygons[$polyId]['rings'] = $;
            $wktString = "POLYGON (";
            $wktString .=  implode(",", $rings);
            
            $wktString .= ")  "; //close polygon
            $polygons[$polyId]['wktstring'] = $wktString;
            $polygons[$polyId]['bbox'] = $polygon['bbox'];
        }
        Log::debug('Ended consolidate rings');

        return $polygons;
    }

    private function kmlFindPolygons(SimpleXMLElement $node, array &$polygons = []): void
    {
        foreach ($node->children() as $child) {

            if ($child->getName() === "Polygon") {
                $polygons[] = $child;
            }

            $this->kmlFindPolygons($child, $polygons);
        }
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

    public function processFileGeojson(): array
    {
        Log::debug(__METHOD__ . ' called.');
        return [];
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName ;
    }

}

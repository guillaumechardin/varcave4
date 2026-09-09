<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use SimpleXMLElement;
use Symfony\Component\Serializer\Encoder\JsonEncode;
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
     * Process a KML or KMZ file and convert its polygons to WKT.
     *
     * This does not validate file mime type or contents. 
     * 
     * The method extracts the KML content from either a plain KML file or
     * a compressed KMZ archive, recursively collects all Polygon elements,
     * extracts their outer and inner rings, computes their bounding boxes,
     * and converts each polygon to a WKT POLYGON string.
     *
     * @return array<int, array{
     *   wktstring: string,
     *   bbox: array{
     *       minLon: float,
     *       maxLon: float,
     *       minLat: float,
     *       maxLat: float
     *   }
     * }>
     *
     * @throws RuntimeException If the KMZ archive is corrupt.
     * @throws RuntimeException If no KML file is found in the KMZ archive.
     * @throws RuntimeException If no polygon is found in the KML file.
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
    
    /**
     * must receive an array based on this format :
     * 0 => "5.920095881019258 43.15653794235881"
     * 1 => "5.91148503448599 43.14129451816049"
     * 2 => "5.95947718770428 43.12948167833671"
     * ... ]
     */
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

    /**
     * Process geojson file
     */
    public function processFileGeojson(): array
    {
        Log::debug(__METHOD__ . ' called.');        
        
        /**
         * Check if file is MultiGeometry
         * Else we consider that is a simple polygon
         */
        Log::info('Collect all polygons data');
        $geoJsonRaw = file_get_contents($this->filePath);
        $jsonData = json_decode($geoJsonRaw);
        
        //quick data validation
        $validatedJson = true;
        //$validatedJson = $this->validateGeoJson($jsonData);
        if( $validatedJson == false){
            throw new RuntimeException(__('varcave.spatial_search.fail_validate_geojson_data'));            
        }

        $polygons = [];
        $count = count($jsonData->features);
        Log::info('File contains: ' . $count . ' multipolygons features to process');

        foreach ($jsonData->features as $feature) {
            if (
                !isset($feature->geometry) ||
                !isset($feature->geometry->type) ||
                !isset($feature->geometry->coordinates)
            ) {
                Log::warning('Current feature does not respect minimum structure, skiping');
                continue;
            }
            
            $geometry = $feature->geometry;
            $type = strtolower($geometry->type);
            
            if ($type === 'polygon') {
                $polygonsDataList = [$geometry->coordinates];
            } elseif ($type === 'multipolygon') {
                $polygonsDataList = $geometry->coordinates;
            } else {
                Log::error('unconsistent geometry type');
                continue;
            }

                // loop trhougth rings 1st ring is exterior ring, subsequents are holes
                foreach ($polygonsDataList as $polygonsData) {
                    // convert outer array from geojson representation to simple ["x0 y0", "x1 y1"] array
                    // outer is at polygonsData[0] index
                    $outerCoordinates = array_map(
                        fn($point) => $point[0] . " " . $point[1],
                        $polygonsData[0]
                    );
                    $outer = [];
                    $outer = $this->polygonsCoordinatesToWkt($outerCoordinates);

                    //Calculation of max bounding box for $polygonsData[0]
                    $bbox = [
                        'minLon' => INF,
                        'maxLon' => -INF,
                        'minLat' => INF,
                        'maxLat' => -INF,
                    ];

                    foreach( $polygonsData[0] as $coordinate) { //index 0 is outer
                    /**
                     * geojson store data as :
                     *    0 => array:n [
                     *       0 => array:2 [
                     *         0 => 5.8758436026805
                     *         1 => 43.167166180319
                     *       ]
                     *       1 => array:2 [
                     *         0 => 5.8765467626798
                     *         1 => 43.157506603543
                     *       ]
                     *  ...
                     **/
                        $longitude = $coordinate[0];
                        $latitude = $coordinate[1];
                        
                        //process bbox calculation
                        $longitude = (float) $longitude;
                        $latitude = (float) $latitude;
                        $bbox['minLon'] = min($bbox['minLon'], $longitude);
                        $bbox['maxLon'] = max($bbox['maxLon'], $longitude);

                        $bbox['minLat'] = min($bbox['minLat'], $latitude);
                        $bbox['maxLat'] = max($bbox['maxLat'], $latitude);
                    }

                    // collect inners rings if any, remove the first array (outer)
                    $innersPols = array_slice($polygonsData, 1);
                    $inners = [];
                    
                    foreach($innersPols as $innerPol){
                        $innerCoordinates = array_map(
                            fn($point) => $point[0] . " " . $point[1],
                            $innerPol
                        );
                        $inners[] = $this->polygonsCoordinatesToWkt($innerCoordinates);
                    }

                    $rings = array_merge([$outer], $inners);
                    $wktString = "POLYGON (";
                    $wktString .=  implode(",", $rings);
                    
                    $wktString .= ")  "; //close polygon
                    $polygons[] = [
                        "wktstring" => $wktString,
                        "bbox" => $bbox,
                    ];
                }
            }
        
        return $polygons;
        
    }

    public function getOriginalFileName(): string
    {
        return $this->originalFileName ;
    }

    /**
     * Quick validate the basic structure of a GeoJSON FeatureCollection.
     * This does not perform afull validation of geojson structure and data 
     *
     * @param object $geoJson
     * @return bool
     */
    private function validateGeoJson(object $geoJson): bool
    {
        Log::debug(__METHOD__ . ' called.');  
        // GeoJSON must be a FeatureCollection
        if (($geoJson->type ?? null) !== "FeatureCollection") {
            Log::error('No featureCollection');
            return false;
        }

        // Features must be present and must be an array
        if (!isset($geoJson->features) || !is_array($geoJson->features)) {
            Log::error('Bad features in featureCollection');
            return false;
        }

        // A FeatureCollection must contain at least one feature
        if (count($geoJson->features) === 0) {
            Log::error('No features found');
            return false;
        }

        foreach ($geoJson->features as $feature) {
            // Each feature must have a geometry
            if (!is_object($feature) || !isset($feature->geometry)) {
                Log::error('No geometry found');
                continue;
            }

            $geometry = $feature->geometry;

            // Look for at least one Polygon or MultiPolygon geometry
            if (
                isset($geometry->type) &&
                in_array($geometry->type, ["Polygon", "MultiPolygon"], true)
            ) {
                return true;
            }
        }
        Log::error('No Polygon or multipolygon found');
        return false;
    }

}

<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use App\Services\CaveService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use phpDocumentor\Reflection\Types\ArrayKey;

/**
 * Service to generate maps image from openstreet map data or equivalent
 * 
 */
class StaticMapService
{
    /**
     * This is greatly inspired from : staticMapLite
     * https://github.com/dfacts/staticmaplite
     * 
     * * Copyright 2009 Gerhard Koch
    *
    * Licensed under the Apache License, Version 2.0 (the "License");
    * you may not use this file except in compliance with the License.
    * You may obtain a copy of the License at
    *
    *     http://www.apache.org/licenses/LICENSE-2.0
    *
    * Unless required by applicable law or agreed to in writing, software
    * distributed under the License is distributed on an "AS IS" BASIS,
    * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    * See the License for the specific language governing permissions and
    * limitations under the License.
    *
    * @author Gerhard Koch <gerhard.koch AT ymail.com>
    *
    * USAGE:
    *
    *  staticmap.php?center=40.714728,-73.998672&zoom=14&size=512x512&maptype=mapnik&markers=40.702147,-74.015794,blues|40.711614,-74.012318,greeng|40.718217,-73.998284,redc
    *
    */

    protected int $maxWidth = 1024;
    protected int $maxHeight = 1024;

    protected int $tileSize = 256;
    
    /**
     * $tileSrcUrl list of  maps tiles url.
     * For now only accept {Z}/{X}/{Y} type API url
     */
    protected array $tileSrcUrl = [
		'mapnik' => 'http://tile.openstreetmap.org/{Z}/{X}/{Y}.png',
        'osmarenderer' => 'http://otile1.mqcdn.com/tiles/1.0.0/osm/{Z}/{X}/{Y}.png',
        'cycle' => 'http://a.tile.opencyclemap.org/cycle/{Z}/{X}/{Y}.png',
		'opentopomap' => 'https://b.tile.opentopomap.org/{Z}/{X}/{Y}.png',
		'outdoor' => 'https://tile.thunderforest.com/outdoor/{Z}/{X}/{Y}.png?apikey=<myapikey>',        
    ];

    /**
     * $tileDefaultSrc the default map to use must exists in $tileSrcUrl
     */
    protected string $tileDefaultSrc = 'mapnik';
    protected string $markerBaseDir = ''; 
    protected string $osmLogo = 'marker/osm_logo.png';

    protected array $markerPrototypes = array(
        // found at http://www.mapito.net/map-marker-icons.html
        'lighblue' => array('regex' => '/^lightblue([0-9]+)$/',
            'extension' => '.png',
            'shadow' => false,
            'offsetImage' => '0,-19',
            'offsetShadow' => false
        ),
        // openlayers std markers
        'ol-marker' => array('regex' => '/^ol-marker(|-blue|-gold|-green|1|2|3|4|5)+$/',
            'extension' => '.png',
            'shadow' => 'marker_shadow.png',
            'offsetImage' => '-10,-25',
            'offsetShadow' => '-1,-13'
        ),
        // taken from http://www.visual-case.it/cgi-bin/vc/GMapsIcons.pl
        'ylw' => array('regex' => '/^(pink|purple|red|ltblu|ylw)-pushpin$/',
            'extension' => '.png',
            'shadow' => 'marker_shadow.png',
            'offsetImage' => '-10,-32',
            'offsetShadow' => '-1,-13'
        ),
        // http://svn.openstreetmap.org/sites/other/StaticMap/symbols/0.png
        'ojw' => array('regex' => '/^bullseye$/',
            'extension' => '.png',
            'shadow' => false,
            'offsetImage' => '-20,-20',
            'offsetShadow' => false
        )
    );

    protected string $defaultMarkerType = '';
    protected string $mapsDir = '';

    protected string $staticMapBaseDir = '';

    protected bool $useTileCache = true;
    protected string $tileCacheBaseDir = '';

    protected bool $useMapCache = false;
    protected string $mapCacheBaseDir = '';
    protected string $mapCacheID = '';
    protected string $mapCacheFile = '';
    protected string $mapCacheExtension = 'png';
    protected int $mapMaxAgeHours = 0;

    protected $image;
    protected int $zoom, $width, $height;
    protected string $maptype;
    protected array $markers;
    protected float $centerX, $centerY, $offsetX, $offsetY, $lat, $lon;

    protected array $cavedata;

    public function __construct(array $cave, array $option = [])
    {
        Log::debug(__METHOD__ . ' called.');
        $this->zoom = Setting::get('pdf_map_zoom');
        $this->lat = 0;
        $this->lon = 0;
        $this->width = 500;
        $this->height = 350;
        $this->markers = array();
        $this->defaultMarkerType = 'ol-marker4';
        $this->maptype = $this->tileDefaultSrc;
        $this->mapMaxAgeHours = Setting::get('pdf_map_cache_delay');

        $this->staticMapBaseDir = Storage::disk('local')->path('staticmap');
        $this->markerBaseDir = $this->staticMapBaseDir . '/markers';
        $this->tileCacheBaseDir = $this->staticMapBaseDir . '/cache/tiles';
        $this->mapCacheBaseDir = $this->staticMapBaseDir . '/cache/maps';
        $this->mapsDir = $this->staticMapBaseDir . '/maps';
        $this->cavedata = $cave;
    }

    protected function parseCaveCoords()
    {
        if(count($this->cavedata['coordinates']['entrance']) < 1){
            Log::error(' no coordinates available');
            return null;
        }
        foreach($this->cavedata['coordinates']['entrance'] as $coord){
            $this->markers[] = array(
                'lat' => floatval($coord['lat']), 
                'lon' => floatval($coord['lon']), 
                'type' => $this->defaultMarkerType,
            );
        }

        $this->lat = $this->markers[0]['lat'];
        $this->lon = $this->markers[0]['lon'];
    }

    
    protected function lonToTile($long, $zoom)
    {
        return (($long + 180) / 360) * pow(2, $zoom);
    }

    protected function latToTile($lat, $zoom)
    {
        return (1 - log(tan($lat * pi() / 180) + 1 / cos($lat * pi() / 180)) / pi()) / 2 * pow(2, $zoom);
    }

    protected function initCoords()
    {
        $this->centerX = $this->lonToTile($this->lon, $this->zoom);
        $this->centerY = $this->latToTile($this->lat, $this->zoom);
        $this->offsetX = floor((floor($this->centerX) - $this->centerX) * $this->tileSize);
        $this->offsetY = floor((floor($this->centerY) - $this->centerY) * $this->tileSize);
    }

    protected function createBaseMap()
    {
        $this->image = imagecreatetruecolor($this->width, $this->height);
        $startX = floor($this->centerX - ($this->width / $this->tileSize) / 2);
        $startY = floor($this->centerY - ($this->height / $this->tileSize) / 2);
        $endX = ceil($this->centerX + ($this->width / $this->tileSize) / 2);
        $endY = ceil($this->centerY + ($this->height / $this->tileSize) / 2);
        $this->offsetX = -floor(($this->centerX - floor($this->centerX)) * $this->tileSize);
        $this->offsetY = -floor(($this->centerY - floor($this->centerY)) * $this->tileSize);
        $this->offsetX += floor($this->width / 2);
        $this->offsetY += floor($this->height / 2);
        $this->offsetX += floor($startX - floor($this->centerX)) * $this->tileSize;
        $this->offsetY += floor($startY - floor($this->centerY)) * $this->tileSize;

		error_log('-- start dl tile --');
        for ($x = $startX; $x <= $endX; $x++) {
            for ($y = $startY; $y <= $endY; $y++) {
                $url = str_replace(array('{Z}', '{X}', '{Y}'), array($this->zoom, $x, $y), $this->tileSrcUrl[$this->maptype]);
                //dd([array('{Z}', '{X}', '{Y}'), array($this->zoom, $x, $y), $this->tileSrcUrl[$this->maptype]], $url);
                $tileData = $this->fetchTile($url);
				error_log($url);
                if ($tileData) {
                    $tileImage = imagecreatefromstring($tileData);
                } else {
                    $tileImage = imagecreate($this->tileSize, $this->tileSize);
                    $color = imagecolorallocate($tileImage, 255, 255, 255);
                    @imagestring($tileImage, 1, 127, 127, 'err', $color);
                }
                $destX = ($x - $startX) * $this->tileSize + $this->offsetX;
                $destY = ($y - $startY) * $this->tileSize + $this->offsetY;
                imagecopy($this->image, $tileImage, $destX, $destY, 0, 0, $this->tileSize, $this->tileSize);
            }
        }
		error_log('-- end  tile DL--');
    }


    protected function placeMarkers()
    {
        // loop thru marker array
        foreach ($this->markers as $marker) {
            // set some local variables
            $markerLat = $marker['lat'];
            $markerLon = $marker['lon'];
            $markerType = $marker['type'];
            // clear variables from previous loops
            $markerFilename = '';
            $markerShadow = '';
            $matches = false;
            // check for marker type, get settings from markerPrototypes
            if ($markerType) {
                foreach ($this->markerPrototypes as $markerPrototype) {
                    if (preg_match($markerPrototype['regex'], $markerType, $matches)) {
                        $markerFilename = $matches[0] . $markerPrototype['extension'];
                        if ($markerPrototype['offsetImage']) {
                            list($markerImageOffsetX, $markerImageOffsetY) = explode(",", $markerPrototype['offsetImage']);
                        }
                        $markerShadow = $markerPrototype['shadow'];
                        if ($markerShadow) {
                            list($markerShadowOffsetX, $markerShadowOffsetY) = explode(",", $markerPrototype['offsetShadow']);
                        }
                    }

                }
            }

            // check required files or set default
            $markerIndex = 0;
            if ($markerFilename == '' || !file_exists($this->markerBaseDir . '/' . $markerFilename)) {
                $markerIndex++;
                $markerFilename = 'lightblue' . $markerIndex . '.png';
                $markerImageOffsetX = 0;
                $markerImageOffsetY = -19;
            }

            // create img resource
            if (file_exists($this->markerBaseDir . '/' . $markerFilename)) {
                $markerImg = imagecreatefrompng($this->markerBaseDir . '/' . $markerFilename);
            } else {
                $markerImg = imagecreatefrompng($this->markerBaseDir . '/lightblue1.png');
            }

            // check for shadow + create shadow recource
            if ($markerShadow && file_exists($this->markerBaseDir . '/' . $markerShadow)) {
                $markerShadowImg = imagecreatefrompng($this->markerBaseDir . '/' . $markerShadow);
            }

            // calc position
            $destX = floor(($this->width / 2) - $this->tileSize * ($this->centerX - $this->lonToTile($markerLon, $this->zoom)));
            $destY = floor(($this->height / 2) - $this->tileSize * ($this->centerY - $this->latToTile($markerLat, $this->zoom)));

            // copy shadow on basemap
            if ($markerShadow && $markerShadowImg) {
                imagecopy($this->image, $markerShadowImg, $destX + intval($markerShadowOffsetX), $destY + intval($markerShadowOffsetY),
                    0, 0, imagesx($markerShadowImg), imagesy($markerShadowImg));
            }

            // copy marker on basemap above shadow
            imagecopy($this->image, $markerImg, $destX + intval($markerImageOffsetX), $destY + intval($markerImageOffsetY),
                0, 0, imagesx($markerImg), imagesy($markerImg));

        };
    }


    protected function tileUrlToFilename($url)
    {
        $parsed_url = parse_url($url);
        return $this->tileCacheBaseDir . "/" . $parsed_url['host'] . $parsed_url['path'];
    }

    protected function checkTileCache($url)
    {
        $filename = $this->tileUrlToFilename($url);
        if (file_exists($filename)) {
            return file_get_contents($filename);
        }
    }

    protected function checkMapCache()
    {
        $this->mapCacheID = md5($this->serializeParams());
        $filename = $this->mapCacheIDToFilename();
        if (file_exists($filename)) return true;
    }

    protected function serializeParams()
    {
        return join("&", array($this->zoom, $this->lat, $this->lon, $this->width, $this->height, serialize($this->markers), $this->maptype));
    }

    protected function mapCacheIDToFilename()
    {
        if (!$this->mapCacheFile) {
            $this->mapCacheFile = $this->mapCacheBaseDir . "/" . $this->maptype . "/" . $this->zoom . "/cache_" . substr($this->mapCacheID, 0, 2) . "/" . substr($this->mapCacheID, 2, 2) . "/" . substr($this->mapCacheID, 4);
        }
        return $this->mapCacheFile . "." . $this->mapCacheExtension;
    }


    protected function mkdir_recursive($pathname, $mode)
    {
        is_dir(dirname($pathname)) || $this->mkdir_recursive(dirname($pathname), $mode);
        return is_dir($pathname) || @mkdir($pathname, $mode);
    }

    protected function writeTileToCache($url, $data)
    {
        $filename = $this->tileUrlToFilename($url);
        $this->mkdir_recursive(dirname($filename), 0777);
        file_put_contents($filename, $data);
    }

    protected function fetchTile($url)
    {
        if ($this->useTileCache && ($cached = $this->checkTileCache($url))) return $cached;
        //disable curl some website mishandle the request for png files
        
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, "varcave/4.0 (Windows NT 10.0; Win64; x64; rv:91.0) varcave-Agent/1.0");
            
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HEADER, false);
            
            curl_setopt($ch, CURLOPT_URL, $url);
            $tile = curl_exec($ch);
        
        //$tile = file_get_contents($url);
        if ($tile && $this->useTileCache) {
            $this->writeTileToCache($url, $tile);
        }
        return $tile;

    }

    protected function copyrightNotice()
    {
        $logoImg = imagecreatefrompng($this->osmLogo);
        imagecopy($this->image, $logoImg, imagesx($this->image) - imagesx($logoImg), imagesy($this->image) - imagesy($logoImg), 0, 0, imagesx($logoImg), imagesy($logoImg));
    }

    protected function makeMap()
    {
        $this->initCoords();
        $this->createBaseMap();
        if (count($this->markers)) $this->placeMarkers();
        //if ($this->osmLogo) $this->copyrightNotice();
    }

    public function getmap()
    {
        $this->parseCaveCoords();
        if ($this->useMapCache) {
            // use map cache, so check cache for map
            if (!$this->checkMapCache()) {
                // map is not in cache, needs to be build
                $this->makeMap();
                $this->mkdir_recursive(dirname($this->mapCacheIDToFilename()), 0777);
                imagepng($this->image, $this->mapCacheIDToFilename(), 9);
            }
        } else {
            // no cache
            $path = $this->mapsDir . '/'. $this->cavedata['attributes']['data']['uuid'] . '.png';
            $isOlder = false;
            if(file_exists($path)){
                $fileTime = filemtime($path);
                //check if file older than limit
                $strTime = '-' . $this->mapMaxAgeHours . ' second';
                $isOlder = $fileTime < strtotime($strTime);
            }

            if( !file_exists($path) || $isOlder ){
                Log::debug(' non existant map or expired:');
                //maps was never built
                $this->makeMap();
                imagepng($this->image, $path);               
            }
            return $path;
        }
    }

    public function setTileSource(string $tileSrcName){
        if(array_key_exists($tileSrcName, $this->tileSrcUrl)){
                $this->maptype = $tileSrcName;
        } else {
            $msg = 'Argument $tileSrc must be one of:' . implode(', ', array_keys($this->tileSrcUrl));
            throw new InvalidArgumentException($msg);
        }
    }
}
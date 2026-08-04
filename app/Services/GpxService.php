<?php

namespace App\Services;

use phpGPX\phpGPX;
use phpGPX\Models\Link;
use phpGPX\Models\Point;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Metadata;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Models\Cave;

/**
 * Service to generate GPX files from Cave or Coordinates data
 * 
 * This service uses the phpGPX library to create GPX files.
 * It can handle a single Cave or multiple Caves.
 */ 
class GpxService
{
    protected $config;

    public function __construct()
    {
        // Example config, adjust as needed
        $this->config = [
            //'httpdomain'   => config('app.url'),
            //'httpwebroot'  => 'caves',
            'include_GPX_details' => true,
        ];
    }

    /**
     * Generate GPX data from one or more caves.
     *
     * @param Collection Collection CaveService->renderForPage()
     * @param bool $useCaveRefAsPointName Use cave reference instead of name for waypoints
     * @return string GPX XML content
     */
    public function createGPX(array $caves,/* string $caveName = 'TO BE REMOVED',*/ bool $useCaveRefAsPointName = false ): string
    {
        Log::debug(__METHOD__ . ' start GPX creation process');
        $gpxFile = new GpxFile();

        foreach ($caves as $cave) {
            $caveData = $cave['attributes']['data'];
            $coordinates = $cave['coordinates']['entrance'];
            if (!$caveData) {
                Log::error("Empty Cave element, skiping");
                continue;
            }
            
            if (empty($coordinates))
            {
                Log::error('No coordinates found for cave' . $cave['attributes']['data']['name']);
                continue;
            }

            // Create metadata for the GPX file
            $gpxFile->metadata = new Metadata();
            $gpxFile->metadata->description = env('APP_NAME', '');

            $link = new Link();
           // $link->href = $this->config['httpdomain'] . '/' . $this->config['httpwebroot'];
            $link->href = route('varcave.homepage');
            $gpxFile->metadata->links[] = $link;

            // Waypoint naming
            $namePrefix = $useCaveRefAsPointName ? $caveData['caveRef'] : $cave['attributes']['data']['name'];
            $multipleCoords = count($coordinates) > 1;

            $i = 1;
            foreach ($coordinates as $coord) {
                $lat  = $coord['lat'];
                $long = $coord['lon'];
                $elev = $coord['z'] ?? 0;

                Log::debug("Adding point: lat {$lat}, long {$long}, elev {$elev}");

                $point = new Point(Point::WAYPOINT);
                $point->name = $namePrefix . ($multipleCoords ? "_{$i}" : '');
                $point->latitude  = $lat;
                $point->longitude = $long;
                $point->elevation = $elev;

                // Description and link
                $url = route('varcave.caves.show', ['uuid' => $caveData['uuid'] ]);
                $description = '';

                if (UserPreferenceService::get('include_GPX_details'))
                {
                    $description = "Infos:\n";
                    $description .= "\nLength: " . $caveData['length'];
                    $description .= "\nMax Depth: " . $caveData['max_depth'];
                    //$hasPhotos = $cave->hasPhotos() ? 'Yes' : 'No';

                    $cave = Cave::getByUuid($caveData['uuid']);
                    if ($cave->hasFileType('photos'))
                    {
                        $description .= "\nPhotos: ".  Str::upper(__('varcave.general.yes'));
                    }else{
                        $description .= "\nPhotos: ".  Str::upper(__('varcave.general.no'));
                    }
                    
                }
                $point->description = $description;

                $link = new Link();
                $link->href = $url;
                $point->links[] = $link;

                $gpxFile->waypoints[] = $point;
                $i++;
            }
        }

        Log::debug('GPX content generated');

        return $gpxFile->toXML()->saveXML();
    }
}

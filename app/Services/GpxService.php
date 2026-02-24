<?php

namespace App\Services;

use phpGPX\phpGPX;
use phpGPX\Models\Link;
use phpGPX\Models\Point;
use phpGPX\Models\GpxFile;
use phpGPX\Models\Metadata;
use InvalidArgumentException;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
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
            'httpdomain'   => config('app.url'),
            'httpwebroot'  => 'caves',
            'include_GPX_details' => true,
        ];
    }

    /**
     * Generate GPX data from one or more caves.
     *
     * @param Collection $caveGuids A single GUID or an array of Cave GUIDs
     * @param bool $useCaveRefAsPointName Use cave reference instead of name for waypoints
     * @return string GPX XML content
     */
    public function createGPX(Cave|Collection $caves, bool $useCaveRefAsPointName = false): string
    {
        Log::debug(__METHOD__ . ' start GPX creation process');

        //Prepare source data for GPX creation
        $_caves = collect();
        if ($caves instanceof Cave) {
            $_caves->push($caves);
        }
        elseif($caves instanceOf Collection && $caves->first() instanceof Cave){
            $caves->values();
        }
        else{
            $msg = 'Undefined cave type';
            Log::error($msg);
            throw new  \InvalidArgumentException($msg);
        }





        $gpxFile = new GpxFile();

        foreach ($_caves as $cave) {
            //$cave = Cave::where('guid', $guid)->first();

            if (!$cave) {
                Log::warning("Empty Cave element, skiping");
                continue;
            }

            $coordsList = json_decode($cave->json_coords)->features ?? [];

            if (empty($coordsList)) {
                Log::debug("No coordinates found for cave {$cave->name}");
                continue;
            }

            // Create metadata for the GPX file
            $gpxFile->metadata = new Metadata();
            $gpxFile->metadata->description = $this->config['httpdomain'] . '/' . $this->config['httpwebroot'];

            $link = new Link();
            $link->href = $this->config['httpdomain'] . '/' . $this->config['httpwebroot'];
            $gpxFile->metadata->links[] = $link;

            // Waypoint naming
            $namePrefix = $useCaveRefAsPointName ? $cave->caveRef : $cave->name;
            $multipleCoords = count($coordsList) > 1;

            $i = 1;
            foreach ($coordsList as $coord) {
                $lat  = $coord->geometry->coordinates[0];
                $long = $coord->geometry->coordinates[1];
                $elev = $coord->geometry->coordinates[2] ?? null;

                Log::debug("Adding point: lat {$lat}, long {$long}, elev {$elev}");

                $point = new Point(Point::WAYPOINT);
                $point->name = $namePrefix . ($multipleCoords ? "_{$i}" : '');
                $point->latitude  = $lat;
                $point->longitude = $long;
                $point->elevation = $elev;

                // Description with link
                $url = $this->config['httpdomain'] . '/' . $this->config['httpwebroot'] . '/display.php?guid=' . $cave->guid;
                $description = $url;

                if ($this->config['include_GPX_details']) {
                    $description .= "\nLength: {$cave->length}";
                    $description .= "\nMax Depth: {$cave->maxDepth}";
                    $hasPhotos = $cave->hasPhotos() ? 'Yes' : 'No';
                    $description .= "\nPhotos: {$hasPhotos}";
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

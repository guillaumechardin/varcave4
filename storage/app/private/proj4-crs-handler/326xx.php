<?php

use proj4php\Point;
use proj4php\Proj4PHP;
use proj4php\Proj;
/**
 * Convert geographic coordinates (WGS84 - EPSG:4326)
 * to UTM coordinates (WGS84 datum) with automatic zone detection.
 *
 * @param {number} lat Latitude in decimal degrees (WGS84)
 * @param {number} lon Longitude in decimal degrees (WGS84)
 * @returns {Array<number>} [Easting, Northing] in meters
 */
function utmTransform(float $x, float $y, Proj4PHP $proj4) {
    //dd('Compute UTM from: '.lat.', '.lon);
    $projWGS84  = new Proj('EPSG:4326', $proj4);
    $zone = floor(($x + 180) / 6) + 1;
    $epsg = 'EPSG:326'.$zone;
    //Log::debug('Computed zone:'.zone);

    // Define projection only if not defined
    if (!$proj4->hasDef($epsg)) {
        $proj4->addDef($epsg,'+proj=utm +zone='.$zone.' +datum=WGS84 +units=m +no_defs');
    }
    $dstProj = new Proj($epsg, $proj4);
    $pointSrc = new Point($x, $y, $projWGS84);
    

    $pointDest = $proj4->transform($dstProj, $pointSrc);
    return [
        'x' => $pointDest->x,
        'y' => $pointDest->y,
        'prefix' => [
            'name' => 'Zone',
            'value' => $zone,
        ],
        'suffix' => [
            'name' => '',
            'value' => '',
        ],
    ];
}
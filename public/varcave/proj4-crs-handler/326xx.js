import proj4 from 'proj4';

/**
 * Convert geographic coordinates (WGS84 - EPSG:4326)
 * to UTM coordinates (WGS84 datum) with automatic zone detection.
 *
 * @param {number} lat Latitude in decimal degrees (WGS84)
 * @param {number} lon Longitude in decimal degrees (WGS84)
 * @returns {Array<number>} [Easting, Northing] in meters
 */
function convertToUtm(lat, lon) {

    const zone = Math.floor((lon + 180) / 6) + 1;
    const epsg = `EPSG:326${zone}`;

    // Define projection only if not already defined
    if (!proj4.defs(epsg)) {
        proj4.defs(
            epsg,
            `+proj=utm +zone=${zone} +datum=WGS84 +units=m +no_defs`
        );
    }

    return proj4("EPSG:4326", epsg, [lon, lat]);
}
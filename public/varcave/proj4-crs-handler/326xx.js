/**
 * Convert geographic coordinates (WGS84 - EPSG:4326)
 * to UTM coordinates (WGS84 datum) with automatic zone detection.
 *
 * @param {number} lat Latitude in decimal degrees (WGS84)
 * @param {number} lon Longitude in decimal degrees (WGS84)
 * @returns {Array<number>} [Easting, Northing] in meters
 */
function utmTransform(lat, lon) {
    Logger.debug('Compute UTM from: '+lat+', '+lon);
    const zone = Math.floor((lon + 180) / 6) + 1;
    const epsg = `EPSG:326${zone}`;
    Logger.debug('Computed zone:'+zone);

    // Define projection only if not already defined
    if (!proj4.defs(epsg)) {
        proj4.defs(
            epsg,
            `+proj=utm +zone=${zone} +datum=WGS84 +units=m +no_defs`
        );
    }

    const coordUTM = proj4("EPSG:4326", epsg, [lon, lat]);
    
    return {
        x: coordUTM[0],
        y: coordUTM[1],
        prefix: {
            name: 'Zone',
            value: zone,
        },
        suffix: {
            name: null,
            value: null,
        },
    }
}
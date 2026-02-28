@props([
    'caveData',
    'caveAccess',
    'caveCoords',
    'crs',
])

<div class="box">
    <div class="columns">
        <div class="column is-two-fifths">
            <script src="/lib/openlayers/10.7.0/dist/ol.js"></script>
            <link rel="stylesheet" href="/lib/openlayers/10.7.0/ol.css" type="text/css">
            <script src="/lib/ol-layerswitcher/4.1.2/dist/ol-layerswitcher.js"></script>
            <link rel="stylesheet" href="/lib/ol-layerswitcher/4.1.2/dist/ol-layerswitcher.css" type="text/css">
            <style>
                #map {
                    width: 100%;
                    height: 50vh; /* par défaut */
                }
            </style>
            <div id="map-wrapper" class="is-skeleton">
                <div id="map" ></div>
            </div>
            <script>
                const crsSettings2 = @json($crs, JSON_PRETTY_PRINT); //to be removed
                $(document).ready(function (){
                    map.on('rendercomplete', function(evt) {
                        Logger.debug('Map rendering complete');
                        $('#map-wrapper ').removeClass('is-skeleton');
                    });
                    
                    const crsSettings = @json($crs, JSON_PRETTY_PRINT);
                    
                    var coordsFrame = null;
                    var forceClear = false;
                    $('#display-crs').on('change', function(e){
                        if(coordsFrame === null){
                           coordsFrame = $('#coord-list').html();
                        }
                        Logger.debug('Change CRS');
                        var crsValue = $(this).val();
                        const epsgCode = $(this).find('option:selected').data('epsg-code');
                        Logger.debug('epsg code found: ' + epsgCode);
                        const selectedEPSG = crsSettings.find(el => el.epsg_code == epsgCode);
                        if(forceClear || epsgCode == 4326){
                            Logger.debug('Restore CRS');
                            forceClear = false;
                            $('#coord-list').html(coordsFrame);
                            return true;
                        }

                        if(selectedEPSG.proj4_string != null){
                            const epsgCodeStr = 'EPSG:'+selectedEPSG.epsg_code;
                            proj4.defs(epsgCodeStr, selectedEPSG.proj4_string);
                            
                            
                            caveCoordinates.forEach(function(coord, index) {
                                Logger.info('Transform:' + selectedEPSG.epsg_name+'('+ epsgCodeStr +')');
                                
                                const X = caveCoordinates[index].lon; 
                                const Y = caveCoordinates[index].lat;
                                Logger.debug(epsgCodeStr);
                                var newCoords = proj4('EPSG:4326', epsgCodeStr, [X, Y]);
                                Logger.info(newCoords);
                                //replace by new coords
                                
                                const $xWrapper = $('#coord-wrapper-' + index + '-x');
                                const $yWrapper = $('#coord-wrapper-' + index + '-y');

                                $xWrapper.find('.x-name').text("X");
                                $xWrapper.find('.x-value').text(Math.floor(newCoords[0]));

                                $yWrapper.find('.y-name').text("Y");
                                $yWrapper.find('.y-value').text(Math.floor(newCoords[1]));

                            });
                        }
                        else if(selectedEPSG.js_handler != null){   
                            Logger.debug('transform module start');
                            forceClear = true;

                            // Create a dynamic <script> for .js load
                            var script = document.createElement('script');
                            script.src = selectedEPSG.js_handler;

                            script.onload = function() {
                                Logger.debug('Handler script loaded: ' + selectedEPSG.js_handler);

                                // well know fn name
                                var fnName = selectedEPSG.epsg_code + '_transform';

                                // check fn present/js file loaded
                                if(typeof window[fnName] === 'function') {
                                    var result = window[fnName](coords);
                                    for(var key in result) {
                                        $('#coord-wrapper-' + index + '-' + key + ' .' + key + '-value').text(result[key]);
                                    }
                                } else {
                                    Logger.error('Transform function not found: ' + fnName);
                                }
                            };

                            script.onerror = function() {
                                Logger.error('Failed to load JS handler: ' + selectedEPSG.js_handler);
                            };

                            document.head.appendChild(script);
                        }
                        else{
                            Logger.error('bad crs handler');
                            return false;
                        }

                        return true;
                        $('#coord-wrapper-0').empty();
                        $('#coord-wrapper-1').empty();
                        proj4.defs("EPSG:9794","+proj=lcc +lat_0=46.5 +lon_0=3 +lat_1=49 +lat_2=44 +x_0=700000 +y_0=6600000 +ellps=GRS80 +towgs84=0,0,0,0,0,0,0 +units=m +no_defs +type=crs");
                        proj4.defs("EPSG:27563","+proj=lcc +lat_1=44.1 +lat_0=44.1 +lon_0=0 +k_0=0.999877499 +x_0=600000 +y_0=200000 +ellps=clrk80ign +pm=paris +towgs84=-168,-60,320,0,0,0,0 +units=m +no_defs +type=crs");
                        //var newvar = proj4(firstProjection,secondProjection,[-122.305887, 58.9465872]);
                        //OK UTM 31 var newvar = proj4('EPSG:4326','EPSG:32631',[5.9677369, 43.2015802]);
                        var newvar = proj4('EPSG:4326','EPSG:27563',[5.9677369, 43.2015802]);
                        // [-2690575.447893817, 36622916.8071244564]
                        Logger.debug('new coord1:');
                        Logger.debug(newvar);
                        //Logger.debug('new coord2:');
                        //Logger.debug(newvar2);
                        //Logger.debug(caveCoordinates);

                    });
                })
            </script>
            <script>
                var cave = @json($caveData['data'], JSON_PRETTY_PRINT);
                
                var nearCavesData = @json($caveCoords['near_caves'], JSON_PRETTY_PRINT);
                var caveCoordinates = @json($caveCoords['entrance'], JSON_PRETTY_PRINT);
                const baseCaveRouteURL = "{{ route('varcave.caves.show', ['_uuid_']) }}";

                // Reusable style
                var caveStyle = new ol.style.Style({
                    image: new ol.style.Icon({
                        src: "/img/marker_green_64.png",
                        anchor: [0.5, 1],
                        scale: 0.5
                    }),
                    text: new ol.style.Text({
                        text: '', // dynamic text if needed
                        scale: 1.5,
                        fill: new ol.style.Fill({
                            color: "#111"
                        }),
                        stroke: new ol.style.Stroke({
                            color: "0",
                            width: 0.5
                        }),
                        backgroundFill: new ol.style.Fill({
                            color: "#22B14C"
                        }),
                        padding: [3, 3, 3, 3],
                        offsetY: -24,
                    })
                });

                // Features caves entrance
                var caveFeatures_a = [];
                for (const caveCoord of caveCoordinates) {
                    var caveFeature = new ol.Feature({
                        geometry: new ol.geom.Point(ol.proj.fromLonLat([caveCoord.lon, caveCoord.lat]))
                    });
                    caveFeature.set("name", cave.name.value);
                    caveFeature.set("main", "main");
                    caveFeature.set("url", 'none');
                    caveFeature.setStyle(caveStyle.clone());
                    caveFeature.getStyle().getText().setText(cave.name.value);
                    caveFeatures_a.push(caveFeature);
                }
                Logger.debug('Current cave features detail:');
                Logger.debug(caveFeatures_a);

                
                // Features near caves
                var nearCaveFeatures_a = [];
                for (const nearCaveData of nearCavesData) {
                    var nearCaveFeature = new ol.Feature({
                        geometry: new ol.geom.Point(ol.proj.fromLonLat([nearCaveData.lon, nearCaveData.lat]))
                    });
                    nearCaveFeature.set("name", nearCaveData.name);
                    nearCaveFeature.set("url", baseCaveRouteURL.replace('_uuid_', nearCaveData.uuid));
                    
                    nearCaveFeature.setStyle(caveStyle.clone());
                    nearCaveFeature.getStyle().getText().setText(nearCaveData.name);
                    nearCaveFeature.getStyle().getImage().setSrc("/img/marker_blue_64.png");
                    nearCaveFeature.getStyle().getText().getBackgroundFill().setColor("#4258ff");  // a essayer #66d1ff
                    nearCaveFeatures_a.push(nearCaveFeature);
                }
                Logger.debug('Closest caves features detail:');
                Logger.debug(nearCaveFeatures_a);
                
                //init layers
                var caveLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({ features: caveFeatures_a }),
                    title: "{{ __('varcave.caveshow.mainEntrance') }}"
                });

                var nearCavesLayer = new ol.layer.Vector({
                    source: new ol.source.Vector({ features: nearCaveFeatures_a }),
                    title: "{{ __('varcave.caveshow.nearCaves') }}"
                });


                //map rendering
                var map = new ol.Map({
                    target: 'map',
                    layers: [
                        new ol.layer.Tile({ 
                            source: new ol.source.OSM(),
                            title: 'Open Street Map',
                        }),
                        caveLayer,
                        nearCavesLayer
                    ],
                    view: new ol.View({
                        center: ol.proj.fromLonLat([{{ $caveCoords['entrance'][0]['lon'] . ',' . $caveCoords['entrance'][0]['lat'] }} ]),
                        zoom: 16
                    })
                });

                // LayerSwitcher
                var lsControl = new ol.control.LayerSwitcher();
                map.addControl(lsControl);


                //click on features
                map.on("click", function(evt){
                    var feature = map.forEachFeatureAtPixel(evt.pixel, function(f){
                        return f;
                    });
                    if(feature && feature.get("main") != "main"){  //skip main entrance
                        var url = feature.get("url");
                        if(url) window.open(url, "_blank");
                    }
                });

                //change pointer style on POI
                map.on("pointermove", function(evt) {
                    var feat = "";
                    var hit = this.forEachFeatureAtPixel(evt.pixel, function(feature, layer) {
                        feat = feature;
                        return true;
                    });
                    if (hit && feat.get("main") != "main") { //skip main entrance
                        this.getTargetElement().style.cursor = "pointer";
                    } else {
                        this.getTargetElement().style.cursor = "";
                    }
                });





            </script>
        </div>
        <div class="column">
            <p class="title is-5"> {{ Str::ucfirst('coordonnées') }} : </p>
            <script src="/lib/proj4js/2.20.2/proj4.js"></script>
            <div class="select is-primary">
                <form>
                    <select name="display-crs" id="display-crs">
                        @foreach($crs as $li )
                            <option 
                                value="{{ $li['list_value_value'] }}"
                                @if ($loop->first)
                                    selected
                                @endif
                                data-epsg-code="{{ $li['epsg_code'] }}"
                            >
                                {{ Str::upper(__($li['list_value_i18n_key'])) }}    
                            </option>    
                        @endforeach
                    </select>
                </form>
            </div>
                <ul id="coord-list">
                    @foreach($caveCoords['entrance'] as $coord)
                            <span>{{ __('varcave.caveshow.cave-entrance', ["nbr"=> $loop->index+1]) }}:</span>
                            <div id="coord-wrapper-{{ $loop->index }}">
                                <div class="field is-grouped is-grouped-multiline">
                                    <div  class="control">
                                        <div id="coord-wrapper-{{ $loop->index }}-x" class="tags has-addons">
                                            <span class="tag is-dark x-name">Lon</span>
                                            <span  class="tag is-info x-value">{{ $coord['lon'] }}</span>
                                        </div>
                                    </div>

                                    <div class="control">
                                        <div id="coord-wrapper-{{ $loop->index }}-y" class="tags has-addons">
                                            <span class="tag is-dark y-name">Lat</span>
                                            <span  class="tag is-info y-value">{{ $coord['lat'] }}</span>
                                        </div>
                                    </div>

                                    <div class="control">
                                        <div id="coord-wrapper-{{ $loop->index }}-z" class="tags has-addons">
                                            <span class="tag is-dark z-name">Elev</span>
                                            <span class="tag is-info z-value">{{ $coord['z'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>                        
                    @endforeach
                </ul>
            <hr>
            
            <p class="title is-5"> {{ Str::ucfirst($caveAccess['model']['access_text']['i18n_label']) }} : </p>
            <p class="content" style="white-space: pre-line;">
                {{ Str::ucfirst($caveAccess['data']['access_text']) }}
            </p>
        </div>
    </div>
</div>
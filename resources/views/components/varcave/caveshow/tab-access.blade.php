@props([
    'caveData',
    'caveName',
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
                $(document).ready(function (){
                    map.on('rendercomplete', function(evt) {
                        Logger.debug('Map rendering complete');
                        $('#map-wrapper ').removeClass('is-skeleton');
                    });
                    
                    const crsSettings = @json($crs, JSON_PRETTY_PRINT);
                    
                    //change coordinates display 
                    const coordsFrame = $('#coord-list').html(); //backup origin div/
                    var forceClear = false;
                    $('#display-crs').on('change', function(e){
                        Logger.debug('Change CRS');
                        
                        var crsValue = $(this).val();
                        const epsgCode = $(this).find('option:selected').data('epsg-code');
                        Logger.debug('epsg code found: ' + epsgCode);
                        
                        if(epsgCode == 4326){
                            Logger.debug('Restore CRS');
                            forceClear = false;
                            $('#coord-list').html(coordsFrame);
                            return true;
                        }

                        const selectedEPSG = crsSettings.find(el => el.epsg_code == epsgCode);
                        if(selectedEPSG.proj4_string != null){
                             if(forceClear){
                                Logger.debug('Restore CRS');
                                forceClear = false;
                                $('#coord-list').html(coordsFrame);
                                ;
                            }
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
                                setXYTags(index, newCoords[0], newCoords[1]);
                            });
                        }
                        else if(selectedEPSG.js_handler_path != null){   
                            Logger.debug('transform module start');
                            forceClear = true;
                            // well know fn name
                            const fnName = selectedEPSG.js_handler_fn;
                      
                            // check fn present/js file loaded
                            if(typeof window[fnName] === 'function') {
                                caveCoordinates.forEach(function(coord, index) {
                                    const lon = caveCoordinates[index].lon; 
                                    const lat = caveCoordinates[index].lat;
                                    var result = window[fnName](lat, lon); //callback function from js transform handler

                                    setXYTags(index, result.x, result.y, result.prefix, result.suffix);
                                });                      

                            } else {
                                Logger.error('Transform function not found: ' + fnName);
                                return false;
                            }
                        }
                        else{
                            Logger.error('bad crs handler');
                            return false;
                        }
                    });
                })

                //define X Y tags from given data
                function setXYTags(index, X, Y, prefix = null, suffix = null){
                    const $xWrapper = $('#coord-wrapper-' + index + '-x');
                    const $yWrapper = $('#coord-wrapper-' + index + '-y');

                    $xWrapper.find('.x-name').text("X");
                    $xWrapper.find('.x-value').text(Math.floor(X));

                    $yWrapper.find('.y-name').text("Y");
                    $yWrapper.find('.y-value').text(Math.floor(Y)); 

                    if(prefix != null && prefix.name){
                        const wrapper = $('#coord-wrapper-'+index+'-prefix');
                        wrapper.find('.prefix-name').text(prefix.name);
                        wrapper.find('.prefix-value').text(prefix.value);

                        const control = wrapper.closest('.control').removeClass('is-hidden');
                    }

                    if(suffix != null && suffix.name){
                        const wrapper = $('#coord-wrapper-'+index+'-suffix');
                        wrapper.find('.suffix-name').text(suffix.name);
                        wrapper.find('.suffix-value').text(suffix.value);

                        const control = wrapper.closest('.control').removeClass('is-hidden');
                    }
                }
                                

            </script>
            <script>
                
                var caveName = "{!! $caveName !!}";
                var nearCavesData = @json($caveCoords['near_caves'], JSON_PRETTY_PRINT);
                var caveCoordinates = @json($caveCoords['entrance'], JSON_PRETTY_PRINT);
                const baseCaveRouteURL = "{{ route('varcave.caves.show', ['_uuid_']) }}";

                // Reusable style
                var caveStyle = new ol.style.Style({
                    image: new ol.style.Icon({
                        src: "/img/marker_03fc84_64.png",
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
                            color: "#03fc84"
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
                    caveFeature.set("name", caveName);
                    caveFeature.set("main", "main");
                    caveFeature.set("url", 'none');
                    caveFeature.setStyle(caveStyle.clone());
                    caveFeature.getStyle().getText().setText(caveName);
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
                    nearCaveFeature.getStyle().getImage().setSrc("/img/marker_is-info_66d1ff_64.png");
                    nearCaveFeature.getStyle().getText().getBackgroundFill().setColor("#66d1ff");  // a essayer #66d1ff
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
                        zoom: {{ $UserPreference::get('ol_zoom_map_lvl') }}
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
                
                //copy correspondant coords to clipboard
                preventDuplicate = false;
                $(document).on('click', '.copy-coord-clipboard', function(e){
                    e.preventDefault();
                    Logger.debug('copy start');

                    let lat = $(this).closest('.field').find(' .x-value').text().trim();
                    let lon = $(this).closest('.field').find(' .y-value').text().trim();

                    navigator.clipboard.writeText(lat + ', ' + lon)
                    .then(() => {
                        const icon = $(this);
                        icon
                        .removeClass('bi-clipboard')
                        .addClass('bi-clipboard-check has-text-success');
                        
                        if(!icon.data('locked')){
                            $(this).parent().after('<span class="copied-label  has-text-success">{{ Str::ucfirst( __('varcave.caveshow.coord_copied')) }}</span>');
                            icon.data('locked', true);
                        }
                        
                        setTimeout(function () {
                            icon
                            .removeClass('bi-clipboard-check has-text-success')
                            .addClass('bi-clipboard');
                            $('.copied-label').remove();
                            icon.data('locked', false);
                        }, 2500);

                    })
                    .catch(err => {
                        Logger.error('clipbaord send failed');
                    });
                });

            </script>
        </div>
        <div class="column">
            <p class="title is-5"> {{ Str::ucfirst(__('varcave.caveshow.coordinates')) }} : </p>
            {{-- Load crs custom functions --}}
            <script src="/lib/proj4js/2.20.2/proj4.js"></script>
            @foreach($crs as $script)
                @if(!empty($script['js_handler_path']))
                    <script src="{{ $script['js_handler_path'] }}"></script>
                @endif
            @endforeach
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
                                <div  class="control is-hidden">
                                    <div id="coord-wrapper-{{ $loop->index }}-prefix" class="tags has-addons">
                                        <span class="tag is-dark prefix-name"></span>
                                        <span  class="tag is-info prefix-value"></span>
                                    </div>
                                </div>

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

                                <div  class="control is-hidden">
                                    <div id="coord-wrapper-{{ $loop->index }}-suffix" class="tags has-addons">
                                        <span class="tag is-dark suffix-name"></span>
                                        <span  class="tag is-info suffix-value"></span>
                                    </div>
                                </div>
                                <div class="icon is-icon-wrapper bi-sm mr-0" >
                                    <a class="bi bi-clipboard copy-coord-clipboard" data-locked="false"></a>
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
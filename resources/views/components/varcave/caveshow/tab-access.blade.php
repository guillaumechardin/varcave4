@props([
    'caveData',
    'caveAccess',
    'caveCoords',
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
                    nearCaveFeature.getStyle().getText().getBackgroundFill().setColor("#1053F1");
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
            <p class="content" style="white-space: pre-line;"></p>
                <ul>
                    @foreach($caveCoords['entrance'] as $coord)
                        <li>{{ __('varcave.caveshow.cave-entrance') }} lat:{{ $coord['lat'] }} elev:{{ $coord['z'] }}</li>
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
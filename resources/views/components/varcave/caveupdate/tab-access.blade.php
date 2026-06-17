@props([
    'caveAccess',
])

<div class="box">
    <div class="columns">
        <div class="column">
            <p class="title is-5"> {{ Str::ucfirst('FR coordonnées') }} : </p>
            <span class="icon is-icon-wrapper bi-md" >
                <a id="add-coord" class="bi bi-plus-square "></a>
            </span>                
            <ul id="coord-list" class="my-2">
                @foreach($caveAccess['coordinates']['entrance'] as $coord)
                    
                    <div class="coord-wrapper">
                        <span>{{ __('varcave.caveshow.cave-entrance', ["nbr"=> $loop->index+1]) }}:</span>
                        <div class="field is-grouped is-grouped-multiline">
                            <div class="field has-addons has-addons-left">
                                <p class="control">
                                    <button class="button is-static has-background-primary has-text-primary-invert">
                                    Longitude
                                    </button>
                                </p>
                                <p class="control">
                                    <input class="input coord-long" type="text" placeholder="3.255445" value="{{ $coord['lon'] }}">
                                </p> 
                            </div>
                            
                            <div class="field has-addons has-addons-left">
                                <p class="control">
                                    <button class="button is-static has-background-primary has-text-primary-invert">
                                    Latitude
                                    </button>
                                </p>
                                <p class="control">
                                    <input class="input coord-lat" type="text" placeholder="43.559845" value="{{ $coord['lat'] }}">
                                </p> 
                            </div>

                            <div class="field has-addons has-addons-left">
                                <p class="control">
                                    <button class="button is-static has-background-primary has-text-primary-invert">
                                    Elevation
                                    </button>
                                </p>
                                <p class="control">
                                    <input class="input coord-lat" type="text" placeholder="258.5" value="{{ $coord['z'] }}">
                                </p> 
                            </div>

                            <div class="field has-addons">
                                <p class="control">
                                    <span class="icon is-icon-wrapper bi-md">
                                        <a class="del-coord-set bi bi-trash has-text-warning" data-coord-id="{{ $coord['id'] }}" ></a>
                                    </span> 
                                </p> 
                            </div>
                        </div>
                    </div>                      
                @endforeach
            </ul>
            <hr>
            <div>
                <p class="title is-5"> {{ Str::ucfirst($caveAccess['attributes']['model']['access_text']['i18n_label']) }} : </p>
                <textarea class="cave-setting p-2" data-fieldname="access_text" style="width:80%;height:15em" >{{ trim($caveAccess['attributes']['data']['access_text']) }}</textarea>
            </div>
        </div>
    </div>
</div>
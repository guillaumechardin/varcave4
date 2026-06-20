@props([
    'caveAccess',
])

<div class="box">
    <div class="columns">
        <div class="column">
            <p class="title is-5"> {{ Str::ucfirst('FR coordonnées') }} : </p>
            <span>
                <label class="checkbox">
                    <input id="permit-coord-set-delete" type="checkbox" />
                        Autoriser la suppression des coordonnées
                </label>
            </span>
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
                                    <button class="button is-static has-background-primary has-text-primary-invert" tabindex="-1">
                                    Longitude
                                    </button>
                                </p>
                                <p class="control">
                                    <input class="input coord-long" type="text" placeholder="3.255445" value="{{ $coord['lon'] }}" tabindex="{{ $coord['id'] +1 }}">
                                </p> 
                            </div>
                            
                            <div class="field has-addons has-addons-left">
                                <p class="control">
                                    <button class="button is-static has-background-primary has-text-primary-invert" tabindex="-1">
                                    Latitude
                                    </button>
                                </p>
                                <p class="control">
                                    <input class="input coord-lat" type="text" placeholder="43.559845" value="{{ $coord['lat'] }}" tabindex="{{ $coord['id'] +2 }}">
                                </p> 
                            </div>

                            <div class="field has-addons has-addons-left">
                                <p class="control">
                                    <button class="button is-static has-background-primary has-text-primary-invert" tabindex="-1">
                                    Elevation
                                    </button>
                                </p>
                                <p class="control">
                                    <input class="input coord-z" type="text" placeholder="258.5" value="{{ $coord['z'] }}"  tabindex="{{ $coord['id'] +3 }}">
                                </p> 
                            </div>

                            <div class="field has-addons">
                                <p class="control">
                                    <span class="icon is-icon-wrapper bi-md ">
                                        <i class="save-coord-set bi bi-floppy has-text-primary" data-coord-id="{{ $coord['id'] }}" ></i>
                                    </span>
                                </p> 
                            </div>

                            <div class="field has-addons">
                                <p class="control">
                                    <span class="icon is-icon-wrapper bi-md ">
                                        <i class="del-coord-set bi bi-trash has-text-warning is-icon-disabled" data-coord-id="{{ $coord['id'] }}" ></i>
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
{{-- This template is used in tab-access AJAX responses to render a new coordinate set --}}
<div class="coord-wrapper" data-coord-id="{{ $coord['id'] }}">
    <span>{{ __('varcave.caveupdate.new_entrance') }}:</span>

    <div class="field is-grouped is-grouped-multiline">
        <div class="field has-addons has-addons-left">
            <p class="control">
                <button class="button is-static has-background-primary has-text-primary-invert">
                Longitude
                </button>
            </p>
            <p class="control">
                <input class="input coord-long" type="text" placeholder="3.255445" value="{{ $coord['lon'] }}" tabindex="{{ $coord['id'] +1 }}">
            </p> 
        </div>
        
        <div class="field has-addons has-addons-left">
            <p class="control">
                <button class="button is-static has-background-primary has-text-primary-invert">
                Latitude
                </button>
            </p>
            <p class="control">
                <input class="input coord-lat" type="text" placeholder="43.559845" value="{{ $coord['lat'] }}" tabindex="{{ $coord['id'] +2 }}">
            </p> 
        </div>

        <div class="field has-addons has-addons-left">
            <p class="control">
                <button class="button is-static has-background-primary has-text-primary-invert">
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
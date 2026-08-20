@props([
    '',
])
<div id="search-coordinates-form-wrapper" class="box">
    <form id="search-coordinates-form" action="{{ route('varcave.caves.searchByCoords') }}">
        <div class="field">
            <input type="submit" class="button is-link container" value="{{ Str::ucfirst(__('varcave.navbar.search')) }}">
        </div>
            <div class="field">
                <label class="label">{{ str::upper(__('varcave.searchPage.select_title')) }} </label>
                <div class="control">
                    <div class="select" >
                    <select id="select-coord-searchtype" name="select-coord-searchtype">
                        <option value="" disabled selected>{{ Str::ucfirst(__('varcave.general.select_choose_opt')) }}</option>
                        <option value="single">{{ __('varcave.searchPage.around_poi') }}</option>
                    </select>
                    </div>
                </div>
            </div>
            
            <div class="field is-hidden" id="wrapper-coords-single">
                <div class="field">
                    <label class="label">{{ __('varcave.searchPage.longitude') }}</label>
                    <div class="control">
                        <input id="search-type-long" name="search-type-long" class="input" type="number" placeholder="5.8581521" step="any">
                    </div>
                </div>
                <div class="field">
                    <label class="label">{{ __('varcave.searchPage.latitude') }}</label>
                    <div class="control">
                        <input id="search-type-lat" name="search-type-lat" class="input" type="number" placeholder="43.2269819" step="any">
                    </div>
                </div>
                <div class="field">
                    <label class="label">{{ __('varcave.searchPage.radius') }} (m)</label>
                    <div class="control">
                        <input id="search-max-radius" name="search-max-radius" class="input" type="number" placeholder="700" placeholder="1500" step="any">
                    </div>
                </div>
            </div>
        <div class="field">
            <input type="submit" class="button is-link container" value="{{ Str::ucfirst(__('varcave.navbar.search')) }}">
        </div>
    </form>
</div>
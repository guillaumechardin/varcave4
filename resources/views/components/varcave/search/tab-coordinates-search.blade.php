@props([
    '',
])
<div id="search-coordinates-form-wrapper" class="box">
    <form id="search-coordinates-form" action="{{ route('varcave.caves.searchByCoords') }}">
        <div class="field">
            <input type="submit" class="button is-link container" value="{{ Str::ucfirst(__('varcave.navbar.search')) }}">
        </div>
            <div class="field">
                <label class="label">FR CHOIX TYPE RECHERCHE</label>
                <div class="control">
                    <div class="select" >
                    <select id="select-coord-searchtype" name="select-coord-searchtype">
                        <option value="" disabled >FR Choisir une option</option>
                        <option value="polygon">FR par zone</option>
                        <option value="single" selected>FR autour d-un point</option>
                    </select>
                    </div>
                </div>
            </div>

            <div class="field is-hidden" id="wrapper-coords-polygon">
                <label class="label"></label>
                    <textarea id="search-type-polygon" name="textarea-coords-search" class="textarea" placeholder="FR entrer coords[[4.4355,43.5588],[4.4758,43.6698]]"></textarea>
            </div>
            
            <div class="field is-hidden" id="wrapper-coords-single">
                <div class="field">
                    <label class="label">FR Long</label>
                    <div class="control">
                        <input id="search-type-long" name="search-type-long" class="input" type="number" value="5.8581521" step="any">
                    </div>
                </div>
                <div class="field">
                    <label class="label">FR lat</label>
                    <div class="control">
                        <input id="search-type-lat" name="search-type-lat" class="input" type="number" value="43.2269819" step="any">
                    </div>
                </div>
                <div class="field">
                    <label class="label">FR rayon de recherche (m)</label>
                    <div class="control">
                        <input id="search-max-radius" name="search-max-radius" class="input" type="number" value="700" placeholder="1500" step="any">
                    </div>
                </div>
            </div>
        <div class="field">
            <input type="submit" class="button is-link container" value="{{ Str::ucfirst(__('varcave.navbar.search')) }}">
        </div>
    </form>
</div>
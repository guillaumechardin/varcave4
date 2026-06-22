@props([
    'caveAccess',
])

<div class="box">
    <div class="columns">
        <div class="column">
            <p class="title is-5"> {{ Str::ucfirst(__('varcave.caveshow.coordinates')) }} : </p>
            <span class="icon is-icon-wrapper bi-md block" >
                <a id="add-coord" class="bi bi-plus-square "></a>
            </span>
            <div class="block">
                <label class="checkbox">
                    <input id="permit-coord-set-delete" type="checkbox" />
                        {{ __('varcave.cave_update.unlock_del_coords') }}
                </label>
            </div>
            <ul id="coord-list" class="my-2">
                @foreach($caveAccess['coordinates']['entrance'] as $coord)

                    @include('varcave.template.caveupdate.coord-wrapper', ['coord' => $coord, 'loopNbr' => $loop->iteration])
                                        
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
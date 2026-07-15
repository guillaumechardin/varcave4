@props([
    'bibliography',
])

<div class="box">
    <p class="title is-5">
        {{ Str::ucfirst( $bibliography['attributes']['model']['bibliography']['i18n_label']) }}&nbsp;:
    </p>
    <div class="field has-addons">
        <div class="control">
            <input id="input-add-bibliography" class="input" type="text" size="30" placeholder="{{ __('varcave.cave_update.add_bibliography') }}">
        </div>
        <div class="control">
            <button class="button is-info" id="add-bibliography-item">
            {{ Str::ucfirst(__('varcave.general.add')) }}
            </button>
        </div>
    </div>
    <div class="field">
        <div class="control">
            <label class="checkbox">
                <input id="input-enable-biblio-delete" type="checkbox"  />
                    {{ __('varcave.cave_update.enable-biblio-delete') }}
            </label>
        </div>
    </div>
    <hr >
    <div id="biblio-tags" class="field is-grouped is-grouped-multiline">
        @foreach($bibliography['attributes']['data']['bibliography'] as $item)
            <x-varcave.caveupdate.tab-bibliography-tag-item :item="$item" />
        @endforeach
    </div>
</div>
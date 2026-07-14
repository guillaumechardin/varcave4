@props([
    'changelog',
])

<div class="box">
    <p class="title is-5">
        {{ Str::ucfirst( __('varcave.caveshow.change_history')) }}&nbsp;:
    </p>
    <div class="field has-addons">
        <div class="control">
                <input id='input-modification-note' class="input" type="text" size="30"  placeholder="{{ __('varcave.cave_update.add_change_log') }}">
            </div>
        <div class="control">
            <button class="button is-info" id="add-change-history">
            {{ Str::ucfirst(__('varcave.general.add')) }}
            </button>
        </div>
    </div>
    <div class="field" id="checkbox-is-homepage-visible-field">
        <div class="control">
            <label class="checkbox">
                <input type="checkbox"  id="input-is-homepage-visible"/>
                    {{ __('varcave.cave_update.changelog_is_visible') }}
            </label>
        </div>
    </div>
    <hr >
    <div id="changelog-items" class="block">
        @foreach($changelog as $change)
            <x-varcave.caveupdate.tab-changehistory-chglog-item :change="$change" />
        @endforeach
    </div>
</div>

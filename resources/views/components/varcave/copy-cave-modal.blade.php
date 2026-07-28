<form id="copy-cave-form" name="copy-cave-form" method='post' action="#">
    <div class="field">
        <label class="label">{{ __('varcave.caveshow.new_name') }}</label>
        <div class="control">
            <input class="input" type="text" name="new-name" value="{{ old('new-name') }}" placeholder="{{ __('varcave.caveshow.copy_name_hint')}}">
        </div>
    </div>
    <div class="field">
        <label class="label">{{ __('varcave.caveshow.new_ref') }}</label>
        <div class="control">
            <input class="input" type="text" name="new-ref" value="{{ old('new-ref') }}" placeholder="{{ __('varcave.caveshow.copy_ref_hint')}}">
        </div>
    </div>
    
    <div class="field is-grouped">
        <div class="control">
            <button class="button is-link">{{ Str::ucfirst(__('varcave.general.save')) }}</button>
        </div>
    </div>
    <input type="hidden" name="src-cave" value="">
</form>
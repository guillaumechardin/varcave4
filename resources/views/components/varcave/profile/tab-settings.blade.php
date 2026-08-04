@props([
    'var',
    'userSettings',
    'availableSettings',
    'listsDetails',
])

<div class="columns m-1">
    <div class="column">
        <div class="settings-wrapper">
            <h3 class="title is-3">{{ Str::ucfirst(__('varcave.profile.settings') ) }}</h3>
            <div id="reset-prefs" class="field">
                <input id="settingname-reset" type="hidden" value="true"/>
                <button  class="button is-warning save-button" data-target-setting="reset">
                    {{ __('varcave.profile.reset_prefs') }}
                </button>
            </div>
            @foreach($availableSettings as $category => $settingsCategory )
                <div class="box">
                    <h5 class="title is-5">
                        {{ Str::ucfirst( __('varcave.settings.category_name.'. $category)) }}
                    </h5>
                    @foreach($settingsCategory as $setting)
                        <div data-name="{{ $setting->name }}" class="field">
                            <label class="label">
                                {{ __('varcave.settings.'. $setting->name .'_dsp') }}
                                <i class="ml-4 bi bi-info-circle" title="{{ __('varcave.settings.'. $setting->name .'_hlp') }}">
                                </i>
                            </label>
                            @switch( $setting->type) 
                                @case('bool')
                                @case('boolean')
                                <div class="field has-addons">
                                    <div class="select">
                                        <select id="settingname-{{ $setting->name }}">
                                            <option value="1" @selected( $setting->value == true) >{{__('varcave.general.yes')}}</option>
                                            <option value="0" @selected( $setting->value == false) >{{__('varcave.general.no')}}</option>
                                        </select>
                                    </div>
                                    <div class="control">
                                        <button class="button is-primary save-button" data-target-setting="{{ $setting->name }}">
                                            <span class="icon">
                                                <i class="bi bi-floppy"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                @break

                                @case('list')
                                <div class="field has-addons">
                                    <div class="select">
                                        <select id="settingname-{{ $setting->name }}">
                                            @foreach($listsDetails['setting.' . $setting->name] as $list)
                                                <option 
                                                    value="{{ $list['value'] }}"
                                                    @selected($setting->value == $list['value'])
                                                >
                                                    {{$list['i18n_key']}}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="control">
                                        <button class="button is-primary save-button" data-target-setting="{{ $setting->name }}">
                                            <span class="icon">
                                                <i class="bi bi-floppy"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                @break

                                @default {{--  text input type --}}
                                <div class="field has-addons">
                                    <div class="control">
                                        <input id="settingname-{{ $setting->name }}" class="input" size="30" type="text"  value="{{ $setting->value }}"/>
                                    </div>
                                    <div class="control">
                                        <button  class="button is-primary save-button" data-target-setting="{{ $setting->name }}">
                                            <span class="icon">
                                                <i class="bi bi-floppy"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                @break
                            @endswitch
                        </div> {{-- end $setting->name section --}}
                    @endforeach
                </div> {{-- end box --}}
            @endforeach
        </div>    {{-- END settings-wrapper --}}
    </div>
</div>
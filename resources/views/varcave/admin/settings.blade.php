@include('varcave.template.header')
@include('varcave.template.navbar')

<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{ Str::ucfirst(__('varcave.settings.site_settings')) }}</p>
        </div>
        <span class="show-adv-options">
            {{ Str::ucfirst(__('varcave.settings.show_adv_opt')) }}<input class="toggle-adv-opt" type="checkbox" disabled>
        </span>
    </section>
    
            <script>
                <x-varcave.admin-settings-js />
            </script>
            
            @foreach($stdSettings as $categoryName => $sCategory)
                <div class="box ">
                    <h5 class="title is-5">{{ Str::ucfirst( __('varcave.settings.category_name.'. $categoryName)) }}</h5>
                    @foreach($sCategory as $s)      
                        <div data-name="{{ $s->name }}" @class([
                            'field',
                            'is-hidden' => $s->is_advanced_option,
                            'is-advanced-opt' => $s->is_advanced_option,
                        ])>
                            <label class="label">{{ __('varcave.settings.'. $s->name .'_dsp') }}<i class="ml-4 bi bi-info-circle" title="{{ __('varcave.settings.'. $s->name .'_hlp') }}"></i></label>
                        @switch( $s->type)
                            @case('json')
                            <div class="field has-addons">
                                <div class="control">
                                    <input id="setting-add-{{ $s->id }}" class="input" type="text"  value=""/>
                                </div>
                                <div class="control">
                                    <button class="button is-primary setting-button-add" data-targetid="{{ $s->id }}">
                                        <span class="icon">
                                            <i class="bi bi-plus-square"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            @php( $select = json_decode( $s->value) )
                            <div class="field has-addons">
                                <div class="select is-multiple">
                                    <select id="settingid-{{ $s->id }}" multiple>
                                        @foreach($select as $sel)
                                            <option  value="{{ $sel }}">{{ $sel }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="control">
                                    <button  class="setting-remove-button button is-warning is-square" data-targetid="{{ $s->id }}">
                                        <span class="icon">
                                            <i class="bi bi-trash"></i>
                                        </span>
                                    </button>
                                    <button  class="button is-primary save-button" data-target-setting="{{ $s->id }}" data-is-multiple="true">
                                        <span class="icon">
                                            <i class="bi bi-floppy"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            @break

                            @case('bool')
                            @case('boolean')
                            <div class="field has-addons">
                                <div class="select">
                                    <select id="settingid-{{ $s->id }}">
                                        <option value="1" @selected( $s->value == true) >{{__('varcave.general.yes')}}</option>
                                        <option value="0" @selected( $s->value == false) >{{__('varcave.general.no')}}</option>
                                    </select>
                                </div>
                                <div class="control">
                                    <button class="button is-primary save-button" data-target-setting="{{ $s->id }}">
                                        <span class="icon">
                                            <i class="bi bi-floppy"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            @break;

                            @case('list')
                            <div class="field has-addons">
                                <div class="select">
                                    <select id="settingid-{{ $s->id }}">
                                        @foreach($listsDetails['setting.'.$s->name] as $list)
                                            <option 
                                                value="{{ $list['value'] }}"
                                                @selected($s->value == $list['value'])
                                            >
                                                {{$list['i18n_key']}}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="control">
                                    <button class="button is-primary save-button" data-target-setting="{{ $s->id }}">
                                        <span class="icon">
                                            <i class="bi bi-floppy"></i>
                                        </span>
                                    </button>
                                </div>
                            </div>
                            @break;

                            @default {{--  text input type --}}
                            <div class="field has-addons">
                                @if ( strlen($s->value) < 60 ) {{-- short to medium text --}}
                                    <div class="control">
                                        <input id="settingid-{{ $s->id }}" class="input" size="{{ strlen($s->value) > 30 ? 60 : 30 }}" type="text"  value="{{ $s->value }}"/>
                                    </div>
                                    <div class="control">
                                        <button  class="button is-primary save-button" data-target-setting="{{ $s->id }}">
                                            <span class="icon">
                                                <i class="bi bi-floppy"></i>
                                            </span>
                                        </button>
                                    </div>
                                @else {{-- Very long text --}}
                                    <div class="control">
                                        <textarea id="settingid-{{ $s->id }}"  rows="6" cols="40" class="textarea" placeholder="">{{ $s->value }}</textarea>
                                    </div>
                                    <div class="control">
                                        <button class="button is-primary save-button" data-target-setting="{{ $s->id }}">
                                            <span class="icon">
                                                <i class="bi bi-floppy"></i>
                                            </span>
                                        </button>
                                    </div>

                                @endif
                            </div>
                        @endswitch
                        </div> {{-- end $s->name section --}}
                    @endforeach
                </div> {{-- end box --}}
            @endforeach
</section>

@include('varcave.template.footer')

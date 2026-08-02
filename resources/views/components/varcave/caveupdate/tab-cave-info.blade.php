@props([
    'caveData',
    'caveDescription',
    'caveObj',
])

<div class="box">
        <div class="fixed-grid has-2-cols-mobile has-5-cols-desktop">
            <div class="grid grid-vertical">
                @foreach ($caveData['attributes']['model'] as $fieldName => $modelData)
                    <div class="cell mb-3 mx-2">
                        <p class="title is-5">
                            {{ Str::ucfirst($modelData['i18n_label']) }}&nbsp;:
                        </p>
                        <div class="field has-addons">
                            <p class="control">
                                @if($modelData['storage_type'] == 'list')
                                    <div class="select">
                                        <select class="cave-setting" data-fieldname="{{ $fieldName }}">
                                            @foreach($modelData['list_values'] as $key => $listName)
                                                <option 
                                                    value="{{ $key }}"
                                                    @selected( (int)$caveData['attributes']['data'][$fieldName] == $key)
                                                >
                                                    {{ $listName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                
                                @elseif($modelData['storage_type'] == 'relation') 
                                    <div class="select">
                                        <select class="cave-setting" data-fieldname="{{ $fieldName }}">
                                            <option
                                                value="-1"
                                                 @selected( $caveObj->{$fieldName} === null)
                                            >
                                                {{ Str::upper(__('varcave.general.none'))}}
                                            </option>
                                            @foreach($modelData['list_values'] as $key => $listName)
                                                <option 
                                                    value="{{ $key }}" 
                                                    @selected( $caveObj->{$fieldName} == $key)
                                                >
                                                    {{ $listName }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                @elseif($modelData['data_type'] == 'bool')
                                    <span class="select">
                                        <select class="cave-setting" data-fieldname="{{ $fieldName }}">
                                            <option value="1" @selected( $caveObj->{$fieldName} == 1) >{{ Str::upper(__('varcave.general.yes')) }}</option>
                                            <option value="0" @selected( $caveObj->{$fieldName} == 0)>{{ Str::upper(__('varcave.general.no')) }}</option>
                                        </select>
                                    </span>
                                    
                                @elseif($modelData['data_type'] == 'number')
                                    <input class="cave-setting input" data-fieldname="{{ $fieldName }}" type="number" value="{{ $caveObj->$fieldName }}"/>

                                @else {{--  text input type --}}
                                    <input class="cave-setting input" data-fieldname="{{ $fieldName }}" type="text"  value="{{ Str::ucfirst($caveData['attributes']['data'][$fieldName]) }}"/>
                                
                                    @endif
                            </p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <hr>
        <div>
            <p class="title is-5">
                {{ Str::ucfirst($caveDescription['attributes']['model']['description']['i18n_label']) }}
            </p>
            <textarea class="cave-setting p-2" data-fieldname="description" style="width:80%;height:15em" >{{ trim($caveDescription['attributes']['data']['description']) }}</textarea>
        </div>
       
</div>
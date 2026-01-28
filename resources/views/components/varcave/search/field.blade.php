@php
    $key = $field->key;
    $typeName = 'type_' . $key;
    $valueName = 'value_' . $key;

    $currentType = request($typeName);
    $currentValue = request($valueName);
@endphp

{{-- 
<div class="field">
  <label class="label">Name</label>
  <div class="control">
    <input class="input" type="text" placeholder="Text input">
  </div>
</div>

--}}

<div class="form-field box">
    <label class="label">
        {{ __('varcave.table_cave.'.$key) }}
        @if($field->unit)
            ({{ $field->unit }})
        @endif
    </label>
    <div class="control">
    @switch($field->data_type)

        {{-- STRING --}}
        @case('string')
            <select name="{{ $typeName }}">
                <option value="LIKE" @selected($currentType === 'LIKE' || !$currentType)>
                    contient
                </option>
                <option value="=" @selected($currentType === '=')>=</option>
                <option value="NOTEQUAL" @selected($currentType === 'NOTEQUAL')>≠</option>
            </select>

            <input
                type="text"
                name="{{ $valueName }}"
                value="{{ $currentValue }}"
                size="25"
            >
            @break

        {{-- NUMBER --}}
        @case('number')
            <select name="{{ $typeName }}">
                <option value="=" @selected($currentType === '=' || !$currentType)>=</option>
                <option value=">">></option>
                <option value="<"><</option>
                <option value=">=">>=</option>
                <option value="<="><=</option>
                <option value="NOTEQUAL">≠</option>
            </select>

            <input
                type="number"
                name="{{ $valueName }}"
                value="{{ $currentValue }}"
            >
            @break

        {{-- BOOL --}}
        @case('bool')
            <select name="{{ $typeName }}">
                <option value="" @selected($currentType === '' || !$currentType)></option>
                <option value="1">{{ Str::upper(__('varcave.general.yes')) }}</option>
                <option value="0">{{ Str::upper(__('varcave.general.no')) }}</option>
            </select>

            {{-- 
            <input
                type="checkbox"
                name="{{ $valueName }}"
                value="1"
                @checked($currentValue)
            >
            --}}
            @break

        {{-- DATE --}}
        @case('date')
            <select name="{{ $typeName }}">
                <option value="=" @selected($currentType === '=' || !$currentType)>=</option>
                <option value="<"><</option>
                <option value=">">></option>
            </select>

            <input
                type="date"
                name="{{ $valueName }}"
                value="{{ $currentValue }}"
            >
            @break

        {{-- FALLBACK --}}
        @default
            <em>Type non supporté</em>

    @endswitch
    </div>
</div>

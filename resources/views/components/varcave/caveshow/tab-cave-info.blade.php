@props([
    'caveData',
])

<div class="box">
    <div class="fixed-grid has-2-cols-mobile has-5-cols-desktop">
        <div class="grid grid-vertical">
            @foreach ($caveData['attributes']['model'] as $fieldName => $modelData)
                <div class="cell">
                    <p class="title is-5"> {{ Str::ucfirst($modelData['i18n_label']) }}&nbsp;: </p>
                    @if($modelData['storage_type'] === 'list')
                        
                        <p class="subtitle is-6">{{ Str::upper($modelData['list_values'][ (int)$caveData['attributes']['data'][$fieldName] ]) }}</p> 
                    @else
                        <p class="subtitle is-6">{{ Str::ucfirst($caveData['attributes']['data'][$fieldName]) }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>
@props([
    'caveData',
    'caveObj',
    'itemPerCol',
])

<div class="box">
    <div class="columns is-1-mobile">
        @foreach (array_chunk($caveData['attributes']['model'], $itemPerCol, true) as $column)
            <div class="column">

                    @foreach ($column as $fieldName => $modelData)
                        <div class="block m-1">
                            <p class="title is-5">
                                {{ Str::ucfirst($modelData['i18n_label']) }}&nbsp;:
                            </p>

                            @if ($modelData['storage_type'] === 'list')
                                <p class="subtitle is-6">
                                    {{ Str::upper($modelData['list_values'][$caveObj->{$fieldName}] ?? '') }}
                                </p>
                            @else
                                <p class="subtitle is-6">
                                    {{ Str::ucfirst($caveData['attributes']['data'][$fieldName] ?? '') }}
                                </p>
                            @endif
                        </div>
                    @endforeach
                
            </div>
        @endforeach
    </div>
</div>
@props(['caveData'])

<div class="box">
    <div class="fixed-grid has-2-cols-mobile has-5-cols-desktop">
        <div class="grid grid-vertical">
            @foreach ($caveData  as $field)
                <div class="cell">
                    <p class="title is-5"> {{ Str::ucfirst($field['i18n_label']) }}&nbsp;: </p>
                    <p class="subtitle is-6"> {{ Str::ucfirst($field['value']) }}</p>
                </div>
            @endforeach
        </div>
    </div>
</div>
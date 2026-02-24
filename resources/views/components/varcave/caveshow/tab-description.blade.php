@props(['caveDescription'])

<div class="box">
    <p class="title is-5">{{ Str::ucfirst($caveDescription['model']['description']['i18n_label']) }}&nbsp;: </p>
    <p class="content" style="white-space: pre-line;">
        {{ Str::ucfirst($caveDescription['data']['description']) }}
    </p>
</div>

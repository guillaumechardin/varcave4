@props(['caveDescription'])

<div class="box">
    <p class="title is-5"> {{ Str::ucfirst($caveDescription['label']) }} : </p>
    <p class="content" style="white-space: pre-line;">
        {{ Str::ucfirst($caveDescription['value']) }}
    </p>
</div>

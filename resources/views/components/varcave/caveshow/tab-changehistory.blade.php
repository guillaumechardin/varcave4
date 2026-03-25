@props([
    'changeHistory',
])


<div class="box">
    <p class="title is-5">{{ Str::ucfirst( __('varcave.caveshow.change_history')) }}&nbsp;: </p>
    @foreach($changeHistory as $change)
        <div class='block'>
            <span class="icon">
                <i class="bi bi-info-square"></i>
            </span>
            <span class="created_at ml-2">
                {{ \Carbon\Carbon::parse($change->created_at)->format('d/m/Y') }}
            </span>
            <span class="modification_note ml-3">
                {{ $change->modification_note }}
            </span>
        </div>
    @endforeach
    
</div>

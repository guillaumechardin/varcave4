@props([
    'changelog',
])


<div class="box">
    <p class="title is-5">{{ Str::ucfirst( __('varcave.caveshow.change_history')) }}&nbsp;: </p>
    <span class="icon is-icon-wrapper bi-md" >
        <i id="add-changelog" class="bi bi-plus-square "></i>
    </span>  
    @foreach($changelog as $change)
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
            <span class="modification_note ml-3">
                <i class="bi bi-trash has-text-warning" title="FR DELETE"></i>
            </span>
        </div>
    @endforeach
    
</div>

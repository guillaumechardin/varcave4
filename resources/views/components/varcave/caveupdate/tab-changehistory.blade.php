@props([
    'changelog',
])

<div class="box">
    <p class="title is-5">
        {{ Str::ucfirst( __('varcave.caveshow.change_history')) }}&nbsp;:
    </p>
    <div class="field has-addons">
        <div class="control">
            <input class="input" type="text" size="30"  placeholder="{{ __('varcave.cave_update.add_change_log') }}">
        </div>
        <div class="control">
            <button class="button is-info" id="add-change-history">
            {{ Str::ucfirst(__('varcave.general.add')) }}
            </button>
        </div>
    </div> 
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
            <span class="modification_note ml-3 is-icon-disabled">
                <i class="bi bi-trash has-text-warning " title="{{ Str::ucfirst(__('varcave.general.delete')) }}"></i>
            </span>
        </div>
    @endforeach
</div>

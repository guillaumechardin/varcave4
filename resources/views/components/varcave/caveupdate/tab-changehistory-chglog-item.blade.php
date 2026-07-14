@props([
    'change', //as an single object/Model CaveChangelog 
])

<div class="changelog-item box" data-changelog-id="{{ $change->id }}" >
    <span class="icon">
        <i class="bi bi-info-square"></i>
    </span>
    <span class="created_at ml-2">
        {{ \Carbon\Carbon::parse($change->created_at)->format('d/m/Y') }}
    </span>
    <span class="modification_note ml-3">
        {{ $change->modification_note }}
    </span>
    <span class="modification-note-actions">
        <i class="bi bi-trash has-text-warning ml-3 is-icon-disabled " title="{{ Str::ucfirst(__('varcave.general.delete')) }}"></i>
    
        <button>
            <i
                @class([
                    'set-note-visibility',
                    'bi',
                    'ml-2',
                    'bi-eye' => ! $change->is_homepage_visible,
                    'bi-eye-slash' => $change->is_homepage_visible,
                ])
                title="{{ Str::ucfirst(__('varcave.cave_update.show_hide_homepage')) }}"
                data-visible="{{ (int)$change->is_homepage_visible }}"
            >
            </i>
        </button>
    </span>
</div>
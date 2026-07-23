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
        <button>
            <i  class="bi bi-trash has-text-warning ml-3 " 
                title="{{ Str::ucfirst(__('varcave.general.delete')) }}"
            >
            </i>
        </button>
    
        <button>
            <i
                @class([
                    'set-note-visibility',
                    'bi',
                    'ml-2',
                    'bi-eye' => ! $change->is_homepage_visible,
                    'bi-eye-slash' => $change->is_homepage_visible,
                ])
                title=
                    @if($change->is_homepage_visible)
                        "{{  Str::ucfirst(__('varcave.cave_update.show_on_homepage')) }}"
                    @else
                        "{{  Str::ucfirst(__('varcave.cave_update.hide_on_homepage')) }}"
                    @endif
                data-visible="{{ (int)$change->is_homepage_visible }}"
            >
            </i>
        </button>
    </span>
</div>
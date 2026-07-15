@props([
    'item',
])
<div class="control">
    <div class="tags edit-item has-addons" data-tag-id="{{ $item->id }}">
        <button 
            @class([
                'tag',
                'is-medium',
                'is-tag-data',
                'is-link' => $item->url,
            ])
        >
            {{ $item->text }}
        </button>
        <button class="tag is-medium is-delete is-warning is-hidden tag-delete"></button>
    </div>
    <div class="tag-edit-form is-hidden box ml-5 has-background-primary-35">
        <div class="field">
            <label class="label">FR biblio</label>
            <div class="control">
                <input type="text" class="input item-text" value="{{ $item->text }}"/>
            </div>
        </div>
        <div class="field">
            <label class="label">FR URL</label>
            <div class="control">
                <input type="text" size="{{ strlen($item->url ?? '') ?: 22 }}" class="input item-url" value="{{ $item->url }}"/>
            </div>
        </div>
        <div class="field is-grouped">
            <div class="control">
                <button class="button save-item">{{ Str::ucfirst(__('varcave.general.save')) }}</button>
                <button class="button cancel-item">{{ Str::ucfirst(__('varcave.general.cancel')) }}</button>
            </div>
        </div>
    </div>
</div>
@props([
    'page',
])
<div id="search-form-wrapper" class="box">
    <form id="search-form">
        <div class="field">
            <input type="submit" class="button is-link container" value="{{ Str::ucfirst(__('varcave.navbar.search')) }}">
        </div>
        <div class="fixed-grid has-1-cols-mobile has-4-cols-desktop">
            <div class="grid grid-vertical">
                @foreach($page->pageFields as $pageField )
                    <div class="cell">
                        <x-varcave.search.field :field="$pageField->field" />
                    </div>
                @endforeach
            </div>
        </div>
        <div class="field">
            <input type="submit" class="button is-link container" value="{{ Str::ucfirst(__('varcave.navbar.search')) }}">
        </div>
    </form>
</div>
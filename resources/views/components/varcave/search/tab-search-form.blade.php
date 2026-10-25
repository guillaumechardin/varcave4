@props([
    'searchFormFields',
    'itemPerCol',
])

<div id="search-form-wrapper" class="box">
    <form id="search-form">
        <div class="field">
            <input type="submit" class="button is-link container" value="{{ Str::ucfirst(__('varcave.navbar.search')) }}">
        </div>
        
        <div class="columns is-1-mobile">
            @foreach (array_chunk($searchFormFields, $itemPerCol, true) as $column)
                <div class="column">
                    @foreach ($column as $key => $field)
                    {{-- @foreach($searchFormFields as $key => $field ) --}}
                        <div class="mt-1 mb-1">
                            <x-varcave.search.field :key="$key" :field="$field" />
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    
        <div class="field">
            <input type="submit" class="button is-link container" value="{{ Str::ucfirst(__('varcave.navbar.search')) }}">
        </div>
    </form>
</div>
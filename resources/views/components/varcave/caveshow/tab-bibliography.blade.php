@props([
    'bibliography',
])

<div class="box">
    <div class="field is-grouped is-grouped-multiline">
        @if(empty($bibliography['value']))
            {{ __('varcave.caveshow.nobiblio')}}
        @else
        @foreach($bibliography['value'] as   $item)
            <div class="control">
                <div class="tags has-addons">
                    <a class="tag is-medium">{{ $item }}</a>
                    {{-- <a class="tag is-delete"></a> --}}
                </div>
            </div>

        @endforeach
        @endif
    </div>
</div>
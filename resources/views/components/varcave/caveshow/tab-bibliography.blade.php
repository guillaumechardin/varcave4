@props([
    'bibliography',
])

<div class="box">
    <div class="field is-grouped is-grouped-multiline">
        @if(empty($bibliography) || $bibliography == '---') {{-- '---' is rendered by cave service if biblio is empty --}}
            {{ __('varcave.caveshow.nobiblio')}}
        @else
        <div class="tags are-medium force-tags-wrap" > {{-- force-tags-wrap for mobile/long bibliography --}}
            @foreach($bibliography as $item)
                <div class="control">
                    @if($item->url)
                        <a class="tag is-link force-tag-wrap" href="{{ $item->url }}" target="_blank" >{{ $item->text }}</a>
                        {{-- <a class="tag is-delete"></a> --}}
                    @else
                        <button class="tag copyable force-tag-wrap">{{ $item->text }}</button>
                    @endif
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
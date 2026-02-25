@props([
    'bookmarks',
])

<div class="columns m-1">
    <div class="column">
        <div class="box">
            <h3 class="title is-3">{{ Str::ucfirst(__('varcave.profile.bookmarks') ) }}</h3>
            <div class="field is-grouped is-grouped-multiline">
                @foreach($bookmarks as $bookmark)
                    <div  id="bookmark-id-{{ $bookmark['id'] }}" class="bookmark-tag tags has-addons " data-bookmark-id="{{ $bookmark['id'] }}" title="Ajouté le {{ $bookmark['created_at'] }}">
                        <span class="tag is-dark is-medium">
                            <a class="has-text-white " href="{{ route('varcave.caves.show', ['uuid' => $bookmark['caveUuid'] ]) }}" target="_blank">{{ $bookmark['caveName'] }}</a>
                        </span>
                        <a class="tag is-delete is-medium"></a>
                    </div>
                @endforeach
            </div>
        </div>    
    </div>
</div>
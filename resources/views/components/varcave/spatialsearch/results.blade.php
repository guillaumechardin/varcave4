@props([
    'datatablesFields',
])
<h1 class="title is-3">
    {{ __('varcave.spatial_search.title_results')  }}
</h1>
<div id="dl-gpx-results" class="mr-2 mb-3 is-hidden">
    <hr>
    <div class="is-flex is-align-items-flex-end">
        <a href="{{ route('varcave.caves.searchToGpx', ['origin' => 'spatialsearch' ]) }}" target="_blank" class="button is-link mr-2">
            {{ Str::ucfirst(__('varcave.general.download')) }} 
        </a>
        <span>{{ __('varcave.spatial_search.download_gpx') }} 
        </span>
    </div>
    <div id="load-gpx-msg">
    </div>
</div>
<table id="spatial-results-table" class="table is-fullwidth is-striped is-hoverable">
    <thead>
        <tr class="is-info">
            @foreach($datatablesFields as $key => $col)
                @continue($key === 'uuid')
                <th class="">{{ $col['i18n_label'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr class="is-info">
            @foreach($datatablesFields as $key => $col)
                @continue($key === 'uuid')
                <th class="">{{ $col['i18n_label'] }}</th>
            @endforeach
        </tr>
    </tfoot>
</table>
@include('varcave.template.header')
@include('varcave.template.navbar')



<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{ Str::ucfirst(__('varcave.searchPage.title')) }}</p>
        </div>
    </section>

    <script>
        <x-varcave.search.search-js :datatablesFields="$datatablesFields" :datatablesLang="$datatablesLang" :datatablesListSelector="$datatablesListSelector" />
    </script>
    

    <script src="/lib/DataTables/datatables.min.js"></script>
    <link   href="/lib/DataTables/datatables.min.css" rel="stylesheet">
    <div id="cavesearch-tabs" class="tabs is-toggle is-toggle-rounded is-centered is-fullwidth" data-bulma="tabs">
        <ul>
            <li>
                <a data-tabs-target="tab-search-form" >
                    <span class="icon is-small">
                        <i class="bi bi-search" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst(__('varcave.searchPage.title')) }}</span>
                </a>
            </li>
            <li>
                <a data-tabs-target="tab-coordinates-search" class="button is-disabled">
                    <span class="icon is-small">
                        <i class="bi bi-table" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst(__('varcave.searchPage.by_coords')) }}</span>
                </a>
            </li>
            <li>
                <a data-tabs-target="tab-search-results" class="button is-disabled">
                    <span class="icon is-small">
                        <i class="bi bi-table" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst(__('varcave.searchPage.results')) }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div id="tabs-contents">
        <div id="tab-search-form" class="tab-content mx-2 mt-2">
                <x-varcave.search.tab-search-form :searchFormFields="$searchFormFields" />
        </div>
        
        <div id="tab-coordinates-search" class="tab-content mx-2 mt-2">
                <x-varcave.search.tab-coordinates-search />
        </div>
        
        <div id="tab-search-results" class="tab-content mx-2 mt-2 ">
            @can('user-access') {{-- save to gpx button only for authenticated users --}}
                <div id="dl-gpx-results" class="mr-2 mb-3 is-hidden">
                    <hr>
                    <div class="is-flex is-align-items-flex-end">
                        <a href="{{ route('varcave.caves.searchToGpx', ['origin' => 'stdsearch' ]) }}" target="_blank" class="button is-link mr-2">
                            {{ Str::ucfirst(__('varcave.general.download')) }} 
                        </a>
                        <span>{{ __('varcave.spatial_search.download_gpx') }} 
                        </span>
                    </div>
                    <div id="load-gpx-msg">
                    </div>
                </div>
            @endcan
            <table id="results-table" class="table is-fullwidth is-striped is-hoverable">
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
        </div>
    </div>
</section>
@include('varcave.template.footer')
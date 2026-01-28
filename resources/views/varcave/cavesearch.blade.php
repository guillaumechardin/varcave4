@include('varcave.template.header')
@include('varcave.template.navbar')



<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{ Str::ucfirst(__('varcave.searchPage.title')) }}</p>
        </div>
    </section>

    <script src="/varcave/cavesearch.js"></script>
    <script src="/lib/DataTables/datatables.min.js"></script>
    <link   href="/lib/DataTables/datatables.min.css" rel="stylesheet">
    <div id="cavesearch-tabs" class="tabs is-toggle is-toggle-rounded is-centered is-fullwidth" data-bulma="tabs">
        <ul>
            <li>
                <a data-tabs-target="tab-search-form">
                    <span class="icon is-small">
                        <i class="bi bi-search" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst(__('varcave.searchPage.title')) }}</span>
                </a>
            </li>
            <li>
                <a data-tabs-target="tab-search-results">
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
                <x-varcave.search.tab-search-form :page="$page"/>
        </div>

        <div id="tab-search-results" class="tab-content mx-2 mt-2">
            <table id="results-table" class="table is-fullwidth is-striped is-hoverable">
                <thead>
                    <tr>
                        <th class="is-hidden">uuid</th>
                        <th>Nom</th>
                        <th>town</th>
                        <th>Numéro caviré</th>
                        <th>profondeur</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr>
                        <th class="is-hidden">uuid</th>
                        <th>Nom</th>
                        <th>town</th>
                        <th>Numéro caviré</th>
                        <th>profondeur</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</section>
@include('varcave.template.footer')
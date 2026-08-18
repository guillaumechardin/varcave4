@include('varcave.template.header')
@include('varcave.template.navbar')


<script src="/lib/DataTables/datatables.min.js"></script>
<link   href="/lib/DataTables/datatables.min.css" rel="stylesheet">
<script>
    <x-varcave.spatialsearch.js :datatablesLang="$datatablesLang" 
                                :datatablesFields="$datatablesFields"
                                :datatablesListSelector="$datatablesListSelector"
    />
</script>

<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{ __('varcave.spatial_search.title')}}</p>
        </div>
    </section>

    <div id="spatial-search-tabs" class="tabs is-centered is-toggle is-toggle-rounded is-centered is-fullwidth" data-bulma="tabs">
        <ul>
            <li>
                <a data-tabs-target="load-file">
                    <span class="icon is-small">
                        <i class="bi bi-cloud-upload" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst( __('varcave.spatial_search.load_file')) }}</span>
                </a>
            </li>
            <li>
                <a data-tabs-target="results">
                    <span class="icon is-small">
                        <i class="bi bi-eye" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst( __('varcave.spatial_search.view_results')) }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div id="tabs-contents">
        <ul>
            <li>
                <div id="load-file" class="tab-content mx-2 mt-2">
                    <x-varcave.spatialsearch.form />
                </div>
            </li>
            <li>
                <div id="results" class="tab-content mx-2 mt-2">
                    
                    <x-varcave.spatialsearch.results :datatablesFields="$datatablesFields"  />
                </div>
            </li> 
        </ul>
    </div>
</section>
@include('varcave.template.footer')
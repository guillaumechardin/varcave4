@include('varcave.template.header')
@include('varcave.template.navbar')
<script>
$(document).ready(function(){
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-import');
});
</script>
<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">{{ __('varcave.users.users_mgmt')}}</p>
        </div>
    </section>
    <div class="tabs is-centered" data-bulma="tabs">
        <ul>
            <li>
                <a data-tabs-target="tab-users">
                    <span class="icon is-small">
                        <i class="bi bi-people" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst( __('varcave.users.users')) }}</span>
                </a>
            </li>
            <li>
                <a data-tabs-target="tab-import">
                    <span class="icon is-small">
                        <i class="bi bi-cloud-upload" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst( __('varcave.users.import_data')) }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div id="tabs-contents">
        <ul>
            <li>
                <div id="tab-users" class="tab-content mx-2 mt-2">
                    <script src="/lib/DataTables/datatables.min.js"></script>
                    <link   href="/lib/DataTables/datatables.min.css" rel="stylesheet">
                    <script>
                        <x-varcave.users.users-js :users="$users" :datatablesLang="$datatablesLang" :user_cols="$user_cols" />
                    </script>
                    <x-varcave.users.tab-users :user_cols="$user_cols"/>
                </div>
            </li>
            <li>
                <div id="tab-import" class="tab-content mx-2 mt-2">
                    <x-varcave.users.tab-import :expirationDate="$expirationDate"/>
                </div>
            </li> 
        </ul>
    </div>
</section>

@include('varcave.template.footer')

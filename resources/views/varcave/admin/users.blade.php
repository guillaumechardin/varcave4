@include('varcave.template.header')
@include('varcave.template.navbar')
<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">Gestion des utilisateurs !!!!!</p>
        </div>
    </section>
    <div class="tabs is-centered" data-bulma="tabs">
        <ul>
            <li>
                <a  data-tabs-target="tab-users">
                    <span class="icon is-small">
                        <i class="bi bi-gear-wide-connected" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst( __('varcave.users.users')) }}</span>
                </a>
            </li>
            <li>
                <a  data-tabs-target="tab-roles">
                    <span class="icon is-small">
                        <i class="bi bi-bookmarks" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst( __('varcave.profile.groups')) }}</span>
                </a>
            </li>
            <li>
                <a  data-tabs-target="tab-import">
                    <span class="icon is-small">
                        <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst( __('varcave.profile.import_data')) }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div id="tabs-contents">
        <ul>
            <li>
                <div id="tab-users" class="tab-content mx-2 mt-2">
                    <x-varcave.users.users />
                </div>
            </li>
            <li>
                <div id="tab-roles" class="tab-content mx-2 mt-2">
                    <x-varcave.users.roles />
                </div>
            </li>
            <li>
                <div id="tab-import" class="tab-content mx-2 mt-2">
                    <x-varcave.users.import />
                </div>
            </li> 
        </ul>
    </div>
</section>
@include('varcave.template.footer')

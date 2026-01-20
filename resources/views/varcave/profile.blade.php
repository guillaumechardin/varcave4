@include('varcave.template.header')
@include('varcave.template.navbar')

<script src="/varcave/profile.js"></script>
<!-- to delete no more used ?
<script>
    const pwdConfirmationUrl = "{{ route('password.confirmation') }}";
</script>
-->
<section class="section">
    <section class="hero">
        <div class="hero-body">
            <p class="title">Hello {{$user->firstname }}</p>
            <p class="subtitle">Paramètres et sécurité du compte</p>

            @if (session('status') === 'password-updated')
                <script>
                    $(document).ready(function(){
                        let msg = {
                            'title' : "{{ Str::ucfirst(__('varcave.general.opSuccess')) }}",
                            'message' : "{{ __('varcave.profile.password-updated') }}", 
                        };
                        showMessageBox(msg);
                    });
                </script>   
            @endif
        </div>
    </section>
    <div class="tabs is-centered" data-bulma="tabs">
        <ul>
            <li>
                <a  data-tabs-target="tab-settings">
                    <span class="icon is-small">
                        <i class="bi bi-gear-wide-connected" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst( __('varcave.profile.settings')) }}</span>
                </a>
            </li>
            <li>
                <a  data-tabs-target="tab-security">
                    <span class="icon is-small">
                        <i class="bi bi-shield-lock" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst( __('varcave.profile.security')) }}</span>
                </a>
            </li>
            <li>
                <a data-tabs-target="tab-others">
                    <span class="icon is-small">
                        <i class="bi bi-bookmark-plus" aria-hidden="true"></i>
                    </span>
                    <span>{{ Str::ucfirst( __('varcave.profile.others')) }}</span>
                </a>
            </li>
        </ul>
    </div>

    <div id="tabs-contents">
        <ul>
            <li>
                <div id="tab-settings" class="tab-content mx-2 mt-2">
                    <div> 'asRoleAdmin' => {{ $asRoleAdmin ? 'true' : 'false' }},</div>
                    <div>'isSearcher' => {{ $isSearcher ? 'true' : 'false' }},</div>
                    <div>'asTwoRoles ['admin', 'user'] => {{ $asTwoRoles ? 'true' : 'false' }},</div>
                    <div>'asMissingRoles ['admin','user', 'dodger']' => {{ $asMissingRoles ? 'true' : 'false' }},</div>                    
                    
                    <div>'roles' => <pre>{{ print_r($roles)}}</pre></div>
                </div>
            </li>
            <li>
                <div id="tab-security" class="tab-content mx-2 mt-2">
                    <x-varcave.profile-tab-security />
                </div>
            </li>
            <li>
                <div id="tab-others" class="tab-content mx-2 mt-2">
                    autres
                </div>
            </li>   
        </ul>
    </div>
</section>

@include('varcave.template.footer')
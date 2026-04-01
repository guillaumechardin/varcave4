<nav class="navbar" role="navigation" aria-label="main navigation">
  
  <div class="navbar-brand">
    <a class="navbar-item" href="{{route('varcave.homepage')}}">
      <span class="has-text-weight-bold">{{ $settings->get('websiteFullName') }}</span>
      <img src="/img/logo_cds83_800x600.png">
    </a>

    <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasic">
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
      <span aria-hidden="true"></span>
    </a>
  </div>

  <div id="navbarBasic" class="navbar-menu">
    <div class="navbar-start">
       {{-- Unauthenticated navbar content --}}
      <a class="navbar-item" href="{{route('varcave.homepage')}}">
        {{__('varcave.navbar.home')}}
      </a>
      
      <div class="navbar-item has-dropdown is-hoverable">
        <a class="navbar-link">
          {{ Str::ucfirst( __('varcave.navbar.caves')) }}
        </a>

        <div class="navbar-dropdown">
          <a class="navbar-item" href="{{ route('varcave.caves.all') }}">
            {{ Str::ucfirst( __('varcave.navbar.allcaves')) }}
          </a>
          <!--<a class="navbar-item">
            Show configs
          </a>-->
          <a class="navbar-item" href="{{ route('varcave.caves.search') }}">
            {{ Str::ucfirst( __('varcave.navbar.search')) }}
          </a>
          <a class="navbar-item" href="{{ route('varcave.caves.statistics') }}">
            {{ Str::ucfirst( __('varcave.navbar.statistics')) }}
          </a>
          
          
        </div>
      </div>
      {{-- END UNauthenticated navbar content --}}

      {{-- admin navbar content --}}
      @can('admin-access')
        <div class="navbar-item has-dropdown is-hoverable">
          <a class="navbar-link">
            {{ Str::ucfirst( __('varcave.navbar.administration')) }}
          </a>
          <div class="navbar-dropdown">
            <a class="navbar-item" href="{{ route('varcave.admin-settings') }}">
              {{ Str::ucfirst( __('varcave.navbar.site_settings')) }}
            </a>

            <a class="navbar-item" href="{{ route('varcave.support-info') }}">
              {{ Str::ucfirst( __('varcave.navbar.support_info')) }}
            </a>

            <a class="navbar-item" href="{{ route('varcave.users.index') }}">
              {{ Str::ucfirst( __('varcave.navbar.users_mgmt')) }}
            </a>
          </div>
        </div>
      @endcan
      {{-- END admin navbar content --}}
      
    </div>
    

    <div class="navbar-end">
      <div class="navbar-item">
        <div class="field has-addons">
          <div class="control">
            <input id="quick-search-value" class="input is-small is-info" type="text" placeholder="{{ Str::ucfirst( __('varcave.navbar.findCave')) }}">
          </div>
          <div class="control">
            <button id="quick-search-button" class="button is-small is-info">{{ Str::upper(__('varcave.general.ok')) }}</button>
          </div>
        </div>
      </div>
      {{-- theme menu changer --}}
      <div class="navbar-item has-dropdown is-hoverable">
        <a id="theme-changer" class="navbar-link" data-targeturl="{{ route('varcave.profile.theme.store',['theme'=>'#']) }}">
          <span class="icon">
            <i class="bi bi-moon-fill"></i>
          </span>
        </a>
        <div class="navbar-dropdown is-right">
          <a class="navbar-item">
            <button class="button-select-theme" data-theme="light">
              <i class="bi bi-brightness-high-fill has-text-warning"></i>
              <span class="ml-3">{{__('varcave.navbar.modeLight')}}</span>
            </button>
          </a>
          <a class="navbar-item">
            <button class="button-select-theme" data-theme="dark">
              <i class="bi bi-moon-stars-fill has-text-link"></i>
              <span class="ml-3">{{__('varcave.navbar.modeDark')}}</span>
            </button>
          </a>
          <a class="navbar-item">
            <button class="button-select-theme" data-theme="system">
              <i class="bi bi-circle-half has-text-light"></i>
              <span class="ml-3">{{__('varcave.navbar.modeSystem')}}</span>
            </button>
          </a>
        </div>
      </div> {{-- END theme menu changer --}}

      @guest 
      <div class="navbar-item mr-2">
        <div class="buttons">
          <a class="button is-light" href="/login">
            {{__('varcave.navbar.log-in')}}
          </a>
        </div>
      </div>
      @endguest
      @auth
      <div class="navbar-item has-dropdown is-hoverable mr-2">
        <a class="navbar-link">{{ Auth::user()->firstname }}</a>
        <div class="navbar-dropdown is-right">
          <a class="navbar-item" href="{{ route('varcave.profile') }}"> {{__('varcave.navbar.account')}} </a>
          <!-- <a class="navbar-item" id="varcave-logout" data-target-url="{{ route('logout') }}" data-csrf-token="{{ csrf_token() }}" href="#logout"> {{__('varcave.navbar.logout')}} </a> -->
            <form method="POST" action="{{ route('logout') }}" style="display:inline;">
              @csrf
              <button class="button is-light  m-2" type="submit">
                  {{__('varcave.navbar.logout')}}
              </button>
          </form>
          
        </div>
      </div>
      @endauth
    </div> <!-- //navbar end -->
  </div>
</nav>

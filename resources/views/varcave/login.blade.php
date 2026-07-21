@include('varcave.template.header')
@include('varcave.template.navbar')
  <section class="section">
  <div class="container">
    <div class="columns is-centered">
      <div class="column is-one-third">

        <div class="notification is-warning">
          Si vous êtes fédéré et qu'il s'agit de votre premiere connexion, vous devez utiliser votre date de naissance comme mot de passe
          au format JJMMAAAA, votre nom d'utilisateur est votre numéro de licence.
        </div>

        @if(session('migrationError'))
          <div class="notification is-danger">
              {{ session('migrationError') }}
          </div>
        @endif

        <div class="box">
          <h3 class="title is-3">{{__('varcave.login.loginFormTitle')}}</h3>
          @if(session('accountState'))
            <div class="has-text-danger my-2">
              {{ session('accountState') }}
            </div>
          @endif
          <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="field">
              <label class="label" for="username">{{__('varcave.login.loginFormUser')}}</label>
               <div class="control has-icons-left has-icons-right">
                <input class="input is-success" id="username" name="username" type="text" value="{{ old('username') }}" placeholder="{{ $settings->get('user_login_tip') }}" required>
                <span class="icon is-small is-left has-text-black">
                    <i class="bi bi-person-fill"></i>
                </span>
              </div>
            </div>

            <div class="field">
              <label class="label" for="password">{{__("varcave.login.loginFormPwd")}}</label>
               <div class="control has-icons-left has-icons-right">
                <input class="input is-success" id="password" name="password" type="password" placeholder="{{__('varcave.login.loginFormPwd')}}" required>
                <span class="icon is-small is-left has-text-black">
                    <i class="bi bi-key-fill"></i>
                </span>
              </div>
            </div>

            <div class="field mt-5">
              <div class="control">
                <button class="button is-link is-fullwidth">{{__("varcave.login.login")}}</button>
              </div>
            </div>

            <div class="field has-text-centered mt-2">
              <a href="{{ route('password.email') }}">{{__("varcave.login.forgotten")}}</a>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</section>

@include('varcave.template.footer')

@include('varcave.template.header')
@include('varcave.template.navbar')
  <section class="section">
  <div class="container">
    <div class="columns is-centered">
      <div class="column is-one-third">
        <div class="box">
          <h3 class="title is-3">{{__('varcave.login.loginFormTitle')}}</h3>
          <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="field">
              <label class="label" for="username">{{__('varcave.login.loginFormUser')}}</label>
               <div class="control has-icons-left has-icons-right">
                <input class="input is-success" id="username" name="username" type="text" placeholder="{{ $settings->get('user_login_tip') }}" required>
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

          </form>
        </div>
      </div>
    </div>
  </div>
</section>

@include('varcave.template.footer')

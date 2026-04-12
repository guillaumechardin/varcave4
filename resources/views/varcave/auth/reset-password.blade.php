@include('varcave.template.header')
@include('varcave.template.navbar')

<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-5">
                <h1 class="title has-text-centered">{{__('varcave.profile.changepassword')}}</h1>
                
                @if (session('status'))
                    <div class="notification is-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ request()->route('token') }}"/>
                    <div class="field">
                        <label class="label" for="username">{{  Str::ucfirst(__('varcave.users.table_users.username')) }}</label>
                        <div class="control has-icons-left">
                            <input 
                                class="input @error('email') is-danger @enderror" 
                                type="text" 
                                name="username" 
                                id="username" 
                                value="{{ old('username') }}" 
                                required 
                                autofocus
                                placeholder="{{ $settings->get('user_login_tip') }}">
                            <span class="icon is-small is-left">
                                <i class="bi bi-person-badge"></i>
                            </span>
                        </div>
                        @error('username')
                            <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <input 
                        type="hidden" 
                        name="email" 
                        id="email" 
                        value="{{ old('email') ?? request('email') }}"
                    />

                    <div class="field">
                        <label class="label" for="email">{{ Str::ucfirst(__('varcave.users.table_users.password'))}}</label>
                        <div class="control has-icons-left">
                            <input 
                                class="input @error('password') is-danger @enderror" 
                                type="password" 
                                name="password" 
                                id="password" 
                                value="" 
                                required 
                                autofocus
                                placeholder="your password">
                            <span class="icon is-small is-left">
                                <i class="bi bi-key"></i>
                            </span>
                        </div>
                        @error('password')
                            <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="field">
                        <label class="label" for="password_confirmation">{{ Str::ucfirst(__('varcave.profile.confirm-password')) }}</label>
                        <div class="control has-icons-left">
                            <input 
                                class="input @error('password_confirmation') is-danger @enderror" 
                                type="password" 
                                name="password_confirmation" 
                                id="password_confirmation" 
                                value="" 
                                required 
                                autofocus
                                placeholder="Password confirmation">
                            <span class="icon is-small is-left">
                                <i class="bi bi-key"></i>
                            </span>
                        </div>
                        @error('password_confirmation')
                            <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <div class="control">
                            <button type="submit" class="button is-primary is-fullwidth">
                                {{ __('varcave.auth.change_pwd') }}
                            </button>
                        </div>
                    </div>

                    <div class="field has-text-centered">
                        <a href="{{ route('login') }}">{{ __('varcave.auth.to_login') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@include('varcave.template.footer')
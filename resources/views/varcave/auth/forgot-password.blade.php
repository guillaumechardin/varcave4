@include('varcave.template.header')
@include('varcave.template.navbar')

<section class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-5">
                <h1 class="title has-text-centered">{{ __('varcave.auth.reset_password') }}</h1>
                
                @if (session('status'))
                    <div class="notification is-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="field">
                        <label class="label" for="email">{{ Str::ucfirst( __('varcave.users.table_users.email')) }}</label>
                        <div class="control has-icons-left">
                            <input 
                                class="input @error('email') is-danger @enderror" 
                                type="email" 
                                name="email" 
                                id="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus
                                placeholder="exemple@mail.com">
                            <span class="icon is-small is-left">
                                <i class="bi bi-envelope-at"></i>
                            </span>
                        </div>
                        @error('email')
                            <p class="help is-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="field">
                        <div class="control">
                            <button type="submit" class="button is-link is-fullwidth">
                                {{ __('varcave.auth.send_link') }}
                            </button>
                        </div>
                    </div>

                    <div class="field has-text-centered">
                        <a href="{{ __('varcave.auth.to_login') }}"></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

@include('varcave.template.footer')
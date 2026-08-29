<div class="columns m-1">
    <div class="column is-one-third">
        <div class="box">
            <form id="password-form" action="{{ route('password.confirm.store') }}" method="post">
                @csrf
                <h3 class="title is-3">{{__('varcave.profile.current-password')}}</h3>
                
                <div class="field">
                    <p class="control has-icons-left has-icons-right">
                        <input class="input" id="password" name="password" type="password" placeholder="{{__('varcave.profile.current-password')}}"  >
                        <span class="icon is-small is-left">
                            <i class="bi bi-key"></i>
                        </span>
                    </p>
                </div>
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        <div class="has-text-danger my-2">
                            {{ $error }}
                        </div>
                    @endforeach
                @endif
                
                <div class="field">
                    <p class="control">
                        <input type="submit" class="button is-link" value="{{ Str::ucfirst( __('varcave.general.send')) }}" />
                    </p>
                </div>
            </form>
        </div>
    </div>
@include('varcave.template.header')
@include('varcave.template.navbar')
<div class="columns m-1">
    <div class="column is-one-third">
        <div class="box">
            <form id="password-form" action="{{ route('varcave.profile.password-update') }}" method="post">
                @csrf
                <h3 class="title is-3">{{__('varcave.profile.changepassword')}}</h3>
                
                <div class="field">
                    <p class="control has-icons-left has-icons-right">
                        <input class="input" id="current_password" name="current_password" type="password" placeholder="{{__('varcave.profile.current-password')}}"  >
                        <span class="icon is-small is-left">
                            <i class="bi bi-key"></i>
                        </span>
                    </p>
                </div>
                <div class="field">
                    <p class="control has-icons-left has-icons-right">
                        <input class="input" id="password" name="password" type="password" placeholder="{{__('varcave.profile.new-password')}}">
                        <span class="icon is-small is-left">
                            <i class="bi bi-key"></i>
                        </span>
                    </p>
                </div>
                <div class="field">
                    <p class="control has-icons-left has-icons-right">
                        <input class="input" id="password_confirmation" name="password_confirmation" type="password" placeholder="{{__('varcave.profile.confirm-password')}}">
                        <span class="icon is-small is-left">
                            <i class="bi bi-key"></i>
                        </span>
                    </p>
                </div>
                <div>
                    <span id="myaccount-errors"> </span>
                </div>
                <div class="field">
                    <p class="control">
                        <input type="submit" class="button is-link" value="{{ Str::ucfirst( __('varcave.general.send')) }}">
                    </p>
                </div>
            </form>
            @error('password')
                <p class="help is-danger">{{ $message }}</p>
            @enderror
        </div>
    </div>
    
</div>

@include('varcave.template.footer')
<div class="columns m-1">
    <div class="column is-one-third">
        <div class="box">
            <h3 class="title is-3">{{ __('varcave.profile.changepassword') }}</h3>
            <a href=" {{ route('varcave.profile.password-update')}} " class="button is-link">{{ Str::ucfirst( __('varcave.general.change')) }}</a>
        </div>    
    </div>

    <div class="column is-one-third">
        <div class="box">
            <h3 class="title is-3">{{ Str::ucfirst( __('varcave.profile.roles')) }}</h3>
            <div class="mb-4">
                {{ Str::ucfirst( __('varcave.profile.your_roles')) }}
            </div>
            @foreach($roles as $role)
                <span class="tag is-info">{{ $role }}</span>
            @endforeach
        </div>    
    </div>
    
    <div class="column is-one-third">
        <div class="box">
            <h3 class="title is-3">{{ Str::ucfirst( __('varcave.profile.show_eula_title')) }}</h3>
            <div class="mb-4">
                @if($user->eula_accepted)
                    {{ Str::ucfirst( __('varcave.profile.eula_was_accepted_at')) }}
                    {{ \Carbon\Carbon::parse($user->eula_accepted_at)->format('d/m/Y H:i') }}
                @else
                    {{ Str::ucfirst( __('varcave.profile.eula_not_yet_accepted')) }}
                @endif
            </div>
        </div>    
    </div>
</div>
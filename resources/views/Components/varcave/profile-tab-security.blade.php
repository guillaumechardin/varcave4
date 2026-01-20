<div class="columns m-1">
    <div class="column is-one-third">
        <div class="box">
            <h3 class="title is-3">Changer de mot de passe</h3>
            <a href=" {{ route('varcave.profile.password-update')}} " class="button is-link">{{ Str::ucfirst( __('varcave.general.change')) }}</a>
        </div>    
    </div>
</div>
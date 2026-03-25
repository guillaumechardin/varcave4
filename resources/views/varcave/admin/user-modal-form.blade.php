<div id="user-modal-wrapper">
    <h5 class="title is-5">{{ $user->firstname . ' ' . $user->lastname }}</h5>
    <form id="user-edit-form" data-userid="{{ $user->id }}">
        <div class="field">
            <label class="label">{{ Str::ucfirst( __('varcave.users.table_users.username')) }}</label>
            <div class="control">
                <input class="input" name="username" type="text" placeholder="User login" value="{{ $user->username }}" disabled>
            </div>
            <div class="control">
                <label class="checkbox">
                    <input id="change-username" type="checkbox" >
                    {{ Str::ucfirst( __('varcave.users.change-pwd')) }}
                </label>
            </div>
        </div>

        <div class="field">
            <label class="label">{{ Str::ucfirst( __('varcave.users.table_users.firstname')) }}</label>
            <div class="control">
                <input class="input" name="firstname" type="text" placeholder="User firstname" value="{{ $user->firstname }}">
            </div>
        </div>

        <div class="field">
            <label class="label">{{ Str::ucfirst( __('varcave.users.table_users.lastname')) }}</label>
            <div class="control">
                <input class="input" name="lastname" type="text" placeholder="User lastname" value="{{ $user->lastname }}">
            </div>
        </div>

        <div class="field">
            <label class="label">{{ Str::ucfirst( __('varcave.users.table_users.email')) }}</label>
            <div class="control">
                <input class="input" name="email" type="text" placeholder="email" value="{{ $user->email }}">
            </div>
        </div>

        <div class="field">
            <label class="label">{{ Str::ucfirst( __('varcave.users.table_users.expires_at')) }}</label>
            <div class="control">
                <input id="expiration-datepicker" class="input" name="expires_at" type="text" value="{{ $user->expires_at ? \Carbon\Carbon::parse($user->expires_at)->format('d/m/Y') : null }}">
            </div>
            <div class="control">
                <label class="checkbox">
                    <input name="no-expiry" type="checkbox" >
                    {{ Str::ucfirst( __('varcave.users.no_expiry')) }}
                </label>
            </div>
        </div>

        <div class="field">
            <label class="label">{{ Str::ucfirst( __('varcave.users.table_users.caving_group')) }}</label>
            <div class="control">
                <input class="input" name="caving_group" type="text" placeholder="caving group" value="{{ $user->caving_group }}">
            </div>
        </div>

        <div class="field">
            <div class="control">
                <label class="checkbox">
                    <input name="eula_accepted" type="checkbox"  @checked($user->eula_accepted)>
                    {{ Str::ucfirst( __('varcave.users.table_users.eula_accepted')) }}
                </label>
                <span>({{ $user->eula_accepted_at ? $user->eula_accepted_at :  __('varcave.general.never') }})</span>
            </div>
        </div>

        <div class="field">
            <label class="label">{{ Str::ucfirst( __('varcave.users.table_users.password')) }}</label>
            <div class="control">
                <input class="input" name="password" type="password" placeholder="User password" value="" disabled>
            </div>
            <div class="control">
                <label class="checkbox">
                    <input id="change-pwd" type="checkbox" >
                    {{ Str::ucfirst( __('varcave.users.change_pwd')) }}
                </label>
            </div>
        </div>

    </form>


</div>{{-- END user-modal-wrapper --}}
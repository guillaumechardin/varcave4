<div id="user-modal-wrapper">
    <h5 class="title is-5">{{ $user->firstname . ' ' . $user->lastname }}</h5>
    <form id="role-edit-form">
        <span class="field">
            <label class="label">roles</label>
            <div class="select is-multiple" >
                <select class="select" multiple id="user-roles" name="user-roles" data-userid="{{ $user->id }}">
                    <option disabled>{{Str::ucfirst(__('varcave.users.choose_role_del')) }}</option>
                    @foreach ($userRoles as $userRole)
                        <option value="{{$userRole['id']}}">{{$userRole['name']}}</option>
                    @endforeach
                </select>
            </div>
            <span>
            <a class="bi bi-chevron-double-left addRole" title="{{Str::ucfirst(__('varcave.users.role_add'))}}"></a>
            <a class="bi bi-chevron-double-right removeRole" title="{{Str::ucfirst(__('varcave.users.role_del')) }}"></a>
            <span>
            <div class="select is-multiple" >
                <select class="select" size="6" multiple id="available-roles">
                    <option disabled>{{Str::ucfirst(__('varcave.users.choose_role_add')) }}</option>
                    @foreach ($availableRoles as $role)
                        <option value="{{$role['id']}}">{{$role['name']}}</option>
                    @endforeach
                </select>
            </div>
        </span>
    </form>
</div>
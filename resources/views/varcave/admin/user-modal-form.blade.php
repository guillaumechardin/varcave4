<div id="user-modal-wrapper">
    <h5 class="title is-5">{{ $user->firstname . ' ' . $user->lastname }}</h5>
    <form id="user-edit-form">
        <div class="field">
            <label class="label">Username</label>
            <div class="control">
                <input class="input" type="text" placeholder="User login" value="{{ $user->username }}">
            </div>
        </div>

        <div class="field">
            <label class="label">Firstname</label>
            <div class="control">
                <input class="input" type="text" placeholder="User firstname" value="{{ $user->firstname }}">
            </div>
        </div>

        <div class="field">
            <label class="label">Lastname</label>
            <div class="control">
                <input class="input" type="text" placeholder="User lastname" value="{{ $user->lastname }}">
            </div>
        </div>

        <div class="field">
            <label class="label">Email</label>
            <div class="control">
                <input class="input" type="text" placeholder="email" value="{{ $user->email }}">
            </div>
        </div>

        <div class="field">
            <label class="label">Caving group</label>
            <div class="control">
                <input class="input" type="text" placeholder="caving group" value="{{ $user->caving_group }}">
            </div>
        </div>

        <div class="field">
            <div class="control">
                <label class="checkbox">
                    <input type="checkbox"  @checked(old('eula_accepted', $user->eula_accepted))>
                    EULA accepted
                </label>
            </div>
        </div>

        <div class="field">
            <label class="label">Password</label>
            <div class="control">
                <input class="input" type="password" placeholder="User password" value="">
            </div>
        </div>

    </form>


</div>{{-- END user-modal-wrapper --}}
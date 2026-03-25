@props([
    'user_cols',
])
<div class="field">
  <label class="label">{{ Str::ucfirst( __('varcave.users.unlock_delete')) }}</label>
  <div class="control">
    <button id="unlock-delete" class="button is-warning">{{ Str::ucfirst( __('varcave.general.unlock')) }}</button>
  </div>
</div>

<div id="tab-users" class="tab-content mx-2 mt-2 ">
    <table id="users-table" class="table is-fullwidth is-striped is-hoverable">
        <thead>
            <tr class="is-info">
                @foreach($user_cols as $c)
                    @continue($c === 'id')
                    <th class="">{{ Str::ucfirst( __('varcave.users.table_users.' . $c)) }}</th>
                @endforeach
                <th class="">id</th>
                <th class="">{{ Str::ucfirst( __('varcave.general.action')) }}</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr class="is-info">
                @foreach($user_cols as $c)
                    @continue($c === 'id')
                    <th class="">{{ Str::ucfirst( __('varcave.users.table_users.' . $c)) }}</th>
                @endforeach
                <th class="">id</th>
                <th class="">{{ Str::ucfirst( __('varcave.general.action')) }}</th>
            </tr>
        </tfoot>
    </table>
</div>
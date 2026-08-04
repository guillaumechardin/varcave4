@props([
    'users',
    'datatablesLang',
    'user_cols',
    'datatablesListSelector',
])

$(document).ready(function(){
    resultsTable = $('#users-table').DataTable({
        rowId: 'id',
        data:  @json($users),
        processing: true,
        language: {!!  $datatablesLang !!},
        columns: [    
            @foreach($user_cols  as $col)
                @continue($col === 'id')
                { data: '{{ $col }}'},
            @endforeach
            {data: 'id', visible: false}, //order must respect <table> structure uuid is at end
            {
                data: null, // is not Data linked
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <button class="button btn-role-user">{{ Str::ucfirst( __('varcave.users.roles')) }}</button>
                        <button class="button btn-delete-user" disabled>{{ Str::ucfirst( __('varcave.general.delete')) }}</button>
                    `;
                }
            }
        ],
        pageLength: {{ $UserPreference::get('datatables_items_selector') }}, //default nbr line shown
        layout: {
            topStart: { 
                pageLength: {
                    @php
                        $menu = array_map('intval', $datatablesListSelector);
                        sort($menu, SORT_NUMERIC);
                    @endphp
                    menu: @json($menu),
                }
            },
            top: null, //'info', 
            topEnd: {
                search: {

                },
            }, 
            
            bottomStart: {
                info: {
                    text: '{{ __('varcave.users.datatables.info') }}',
                    search: '{{ __('varcave.users.datatables.search') }}'
                },
            },
            bottom: null
                /* {
                div:{
                    className: 'is-warning button',
                    id: 'warn-btn',
                    html: '<button> Click button to acknowledge</button>'
                }*/
            ,
            bottomEnd: {
                paging: {
                },
            },
        }, 
        responsive: true,
    });

    /**
     * Open user modal window
     */
    let tableProcessing = false;
    $('#users-table tbody').on('click', 'td', function(e) {
        Logger.debug('td clicked');
        
        if(tableProcessing){
            Logger.info('Table interaction is disabled');
            return; 
        } 
        else if(
            $(e.target).closest('button.btn-delete-user').length ||
            $(e.target).closest('button.btn-role-user').length
        )
        { 
            return;
        }

        
        //show table loader
        var table = $('#users-table').DataTable();
        table.processing(true);
        tableProcessing = true;

        let $tr = $(this).closest('tr');
        let data = resultsTable.row($tr).data();

        //Quick & dirty to identify mobile screen and prevent modal opening
        // when 1td is clicked when Datatable responsiveness is on
        let isMobile = $(window).width() < 768; 
        Logger.debug('rowId:'+data.id);
        if ( isMobile && $(this).hasClass('dtr-control')) {
            tableProcessing = false;
            table.processing(false);
            return;
        }

        //let data = resultsTable.row($tr).data();
        Logger.debug('load modal for: '+ data.id );
        if (data && data.id) {
            const target = '';
            const url = "{{ route('varcave.users.user-modal-form', ['user' => '_ID_']) }}";
            //ajax request return on success a full webpage to be loaded into modal window
            sendAjaxRequest(url.replace('_ID_', data.id), 'get', '', loadUserModal, loadUserModalFailed);
        }
        
    });

    function loadUserModal(data){
        $('#modal-message-button-save').remove();
        var table = $('#users-table').DataTable();
        table.processing(false);
        tableProcessing = false;

        //insert web form into modal add add save button
        showModal('{{ __('varcave.users.edit_user') }}', data );
        
        const $expirationDatepicker = $('#expiration-datepicker');
        $expirationDatepicker.datepicker({
            format: 'dd/mm/yyyy',
            changeMonth: true,
            changeYear: true,
        });
        addSaveButton();
    }

    function loadUserModalFailed(){
        showMessageBox("{{ __('varcave.general.opFailed') }}", 'is-danger');
        var table = $('#users-table').DataTable();
        table.processing(false);
        tableProcessing = false;
    }

    function addSaveButton(){
        const saveButton = '<button id="user-save" class="button">{{ Str::ucfirst( __('varcave.general.save')) }}</button>';
        $('#modal-message-buttons').append(saveButton);
    }

    /**
     * Add or remove a role for user
     */
    $(document).on('click','.btn-delete-user, .btn-role-user', function(e){
        e.stopPropagation(); //prevent bubbling event to tr

        //show table loader
        var table = $('#users-table').DataTable();
        table.processing(true);

        let $td = $(this);
        let $tr = $td.closest('tr');
        let data = resultsTable.row($tr).data();
        const targetUserid = data.id;

        if($(this).hasClass('btn-delete-user')){
            Logger.debug('Delete user:'+targetUserid);

            const url = "{{ route('varcave.users.delete', ['user' => '_ID_']) }}";
            sendAjaxRequest(url.replace('_ID_', targetUserid), 'delete', '', deleteSuccess, deleteFailed);
        }
        else if($(this).hasClass('btn-role-user')) {
            Logger.debug('show user roles:'+targetUserid);

            const url = "{{ route('varcave.users.role', ['user' => '_ID_']) }}";
            sendAjaxRequest(url.replace('_ID_', targetUserid), 'get', '', getRoleSuccess, deleteFailed); //delete failed is generic function
        }
    });

    function deleteSuccess(response){
        Logger.debug('Remove corresponding line from table. ID :'+response.data);
        showMessageBox(response);
        var table = $('#users-table').DataTable();

        const row = table.row('#' + response.data);
        const $tr = $( row.node() );
        
        // animate removal
        $tr.fadeOut(500, function() {
            row.remove().draw(false);
            table.processing(false);
        });
    }

    function deleteFailed(response){
        showMessageBox(response, 'is-danger');
        var table = $('#users-table').DataTable();
        table.processing(false);
    }

    /**
     * Save user infos
     */
    $(document).on('click', '#user-save', function(e){
        Logger.info('save user settings');

        $form = $('#user-edit-form');
        var formData = $form.serialize();
        const targetUserid = $form.data('userid');
        console.log('formData:');
        console.log(formData);
        
        const url = "{{ route('varcave.users.save', ['user' => '_ID_']) }}";
        sendAjaxRequest(url.replace('_ID_', targetUserid), 'put', formData, userEditSuccess, deleteFailed); //use delete failed for same features
        var table = $('#users-table').DataTable();
        table.processing(true);
        
        closeModal( $(this).closest('.modal'), true );
    });

    $('#unlock-delete').on('click', function(e){
        Logger.debug('unlock delete user buttons');
        //if($(this).prop('disabled')) return true;
        $('.btn-delete-user').prop('disabled', false);
        //$(this).prop('disabled', true);
        //$(this).closest('.field').hide(500);
    });

    function getRoleSuccess(htmlForm){
        $('#modal-message-button-save').remove();
        let table = $('#users-table').DataTable();
        table.processing(false);
        tableProcessing = false;

        //insert web form into modal add add save button
        showModal('{{ __('varcave.users.edit_user') }}', htmlForm );
        
        //add button to modal
        const saveButton = '<button id="role-save" class="button">{{ Str::ucfirst( __('varcave.general.save')) }}</button>';
        $('#modal-message-buttons').append(saveButton);
    }

    /**
     * Save user roles
     */
    $(document).on('click', '#role-save', function(e){
        Logger.info('save user role settings');

        let userRoles = $('#user-roles option:not([disabled])').map(function() {
            return $(this).val();
        }).get();
        const targetUserid = $('#user-roles').data('userid');
        
        let formData = {
            userid : targetUserid,
            roles: userRoles,
        };
        
        Logger.debug('formData:');
        Logger.debug(formData);

        const url = "{{ route('varcave.users.role-save', ['user' => '_ID_']) }}";
        sendAjaxRequest(url.replace('_ID_', targetUserid), 'put', formData, RoleSaveSuccess, deleteFailed); //use generic deleteFailed for err msg display        
        
        closeModal( $(this).closest('.modal'), true );
    });

    function RoleSaveSuccess(response)
    {
        const data = response.data;
        Logger.debug('Update  user role complete :' + data);
        showMessageBox(response);
    }

    $('body').on('click', '.addRole, .removeRole', function(e){
        Logger.debug('Add remove/role');

        //remove role of user
        if( $(this).hasClass('removeRole') ){
            $('#user-roles option:selected').appendTo('#available-roles');
            return;
        }
        
        //move role to user-role
        $('#available-roles option:selected').appendTo('#user-roles');

    });

    $('#users-import-expiration-datepicker').datepicker({
        format: 'dd/mm/yyyy',
        changeMonth: true,
        changeYear: true,
    });

    //show small info on debug
    Logger.debug('Page load complete');
});

$(document).on('change', '#change-pwd', function(e) {
    Logger.debug('Password change checkbox');
    var isChecked = $(this).prop('checked');
    $('input[name="password"]').prop('disabled', !isChecked);
});

$(document).on('change', '#change-username', function(e) {
    Logger.debug('Username change checkbox');
    var isChecked = $(this).prop('checked');
    $('input[name="username"]').prop('disabled', !isChecked);
});

function userEditSuccess(response){
    const data = response.data;
    Logger.debug('Update  datatable rowID :' + data.id);
    showMessageBox(response);
    var table = $('#users-table').DataTable();
    row = table.row('#' + data.id);
    
    newRowData = {
        "username":  data.username,
        "firstname": data.firstname,
        "lastname": data.lastname,
        "id": data.id ,
        "expires_at": data.expires_at,
    };

    row.data(newRowData);
    table.draw();
    table.processing(false);
}


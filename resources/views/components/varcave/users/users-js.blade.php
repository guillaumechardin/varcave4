@props([
    'users',
    'datatablesLang',
    'user_cols',
])

$(document).ready(function(){
    Logger.debug('pagge loaded');

    resultsTable = $('#users-table').DataTable({
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
                data: null, // pas lié aux données
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    return `
                        <button class="button btn-edit" data-id="${row.id}">Edit</button>
                        <button class="button btn-delete" data-id="${row.id}">Delete</button>
                    `;
                }
            }
        ],
        pageLength: {{ \App\Services\UserPreferenceService::get('datatables_max_items', 'datatables_max_items') }},  // default number line
        layout: {
            topStart: { 
                pageLength: {
                    menu: {{ $settings->get('datatables_items_selector') }},
                }
            },
            top: null, //'info', 
            topEnd: {
                search: {

                },
            }, 
            
            bottomStart: {
                info: {
                    text: 'Affiche les utilisateurs _START_ à _END_ sur un total de _TOTAL_ ',
                    //postfix: 'All records shown are derived from real information.'
                    search: ' - filtré sur  _MAX_ enregistremnts'
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
     * Open cave in a new tab or window
     */
    $('#users-table tbody').on('click', 'td', function (e) {
        Logger.debug('td clicked');

        let $td = $(this);
        let $tr = $td.closest('tr');

        //Quick & dirty to identify mobile screen
        let isMobile = $(window).width() < 768; 
        if ( isMobile && $td.index() === 0) return;

        let data = resultsTable.row($tr).data();
        Logger.debug('load modal for: '+ data.id );
        if (data && data.id) {
            const target = '';
            const url = "{{ route('varcave.users.user-modal-form', ['user' => '_ID_']) }}";
            sendAjaxRequest(url.replace('_ID_', data.id), 'get', '', loadUserModal, 'failLoadUserModal');
            
        }
        
    });

});


function loadUserModal(data){
    $('#modal-message-button-save').remove();
    showModal('{{ __('varcave.users.edit_user') }}', data );
    addSaveButton();
}

function addSaveButton(){
    const saveButton = '<button id="modal-message-button-save" class="button">{{ Str::ucfirst( __('varcave.general.save')) }}</button>';
    $('#modal-message-buttons').append(saveButton);
}
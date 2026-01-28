$(document).ready(function($){
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-search-form');


    //Handle search button and results
    $('#search-form').submit(function(e){
        e.preventDefault();
        Logger.debug('Form clicked');

        var formData = $(this).serialize();

        $('#cavesearch-tabs').bulmaVar(
            'Tabs',
            'goToTabById',
            'tab-search-results'
        );

        //sendAjaxRequest("{{__route('varcave.cave.search')}}", 'get', formData, onSuccess, onError)
        
        if ( $.fn.DataTable.isDataTable('#results-table') ) {
           resultsTable = $('#results-table').DataTable().ajax.url('/cave/search?' + formData).load();
        } else {
            resultsTable = $('#results-table').DataTable({
                ajax: {
                    url:'/cave/search?' + formData,
                    method: 'get',
                    dataSrc: '',
                },
                processing: true,
                //serverSide: true,
                columns: [
                    { data: 'uuid',visible:false },
                    { data: 'name' },
                    { data: 'town' },
                    { data: 'cave_ref' },
                    { data: 'max_depth' }
                ],
                pageLength: 5,                   // nombre de lignes par défaut
                layout: {
                    topStart: { 
                        pageLength: {
                            menu: [5, 10, 15, 20],
                            pageLength: 5,
                        }
                    },
                    top: null, //'info', 
                    topEnd: {
                        search: {

                        },
                    }, 
                    
                    bottomStart: {
                        info: {
                            text: 'Affiche les cavités _START_ à _END_ sur un total de _TOTAL_ ',
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
            })
        }
    });

    let resultsTable;

    $('#results-table tbody').on('click', 'tr', function () {
        let data = resultsTable.row(this).data();
        if (data && data.uuid) {
            window.open('/cave/' + data.uuid, '_blank');
        }
    });



});
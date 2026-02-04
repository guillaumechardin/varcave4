@props([
    'datatablesFields',
    'datatablesLang',
])

$(document).ready(function($){
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-search-form');

    /**
     * This process a search request when using quick search input in navbar
     */
    const urlParams = new URLSearchParams(window.location.search);
    Logger.debug('url params:');
    Logger.debug(urlParams);
    if (urlParams.has('quicksearch')) {
        if(urlParams.get('value_name') != ''){
            $('#cavesearch-tabs').bulmaVar('Tabs', 'goToTabById', 'tab-search-results');
            var formData = urlParams.toString();
            doSearch(formData, "{{route('varcave.caves.search')}}" );
        }else{
            Logger.error('Unsupported cave search');
        }
    }


    /**
     * Handle search form button and process search request
     */
    $('#search-form').submit(function(e){
        e.preventDefault();
        Logger.debug('Form submited');
        
        var formData = getFilteredFormData($('#search-form'));
        
        
        formData = $.param(formData);
        Logger.debug(formData);
        
        

        $('#cavesearch-tabs').bulmaVar(
            'Tabs',
            'goToTabById',
            'tab-search-results'
        );
        
        doSearch(formData, "{{route('varcave.caves.search')}}" );
    });

    /**
     *  Handles click on row and open cave in new tab
     */
    /*$('#results-table tbody').on('click', 'tr', function () {
        let data = resultsTable.row(this).data();
        if (data && data.uuid) {
            const target = caveShowTemplaceUrl.replace('__UUID__', data.uuid);
            window.open(target, '_blank');
        }
    });*/

    /**
     * Open cave in a new tab or window
     */
    $('#results-table tbody').on('click', 'td', function (e) {
        Logger.debug('td clicked');

        let $td = $(this);
        let $tr = $td.closest('tr');

        //small workaround to identify mobile screen
        let isMobile = $(window).width() < 768; 
        if ( isMobile && $td.index() === 0) return;

        let data = resultsTable.row($tr).data();
        if (data && data.uuid) {
            const target = caveShowTemplaceUrl.replace('__UUID__', data.uuid);
            window.open(target, '_blank');
        }
    });
    
    function getFilteredFormData($container) {
        const raw = $container.find(':input').serializeArray();
        const data = {};
        const values = {};

        // Collect values
        $.each(raw, function (_, field) {
            if (field.name.startsWith('value_')) {
                values[field.name.replace('value_', '')] = field.value.trim();
            }
        });

        // keep only value/type peer
        $.each(raw, function (_, field) {
            const value = field.value.trim();

            // value_xxx
            if (field.name.startsWith('value_')) {
                if (value !== '') {
                    data[field.name] = value;
                }
                return;
            }

            // type_xxx
            if (field.name.startsWith('type_')) {
                const key = field.name.replace('type_', '');
                if (values[key] && values[key] !== '') {
                    data[field.name] = value;
                }
                return;
            }

            // other fields
            if (value !== '') {
                data[field.name] = value;
            }
        });

        return data;
    }

    
    /**
     *  Start a search request agains server an populate Datatables if any valid return available
     */
    var resultsTable;
    function doSearch(formData, url){
        
        if ( $.fn.DataTable.isDataTable('#results-table') ) {
           resultsTable = $('#results-table').DataTable().ajax.url(url + '?' + formData).load();
        } else {
            resultsTable = $('#results-table').DataTable({
                ajax: {
                    url:url + '?' + formData,
                    method: 'get',
                    dataSrc: '',
                    error: function (xhr, textStatus, errorThrown) {
                        resultsTable.clear();
                        resultsTable.rows.add([]).draw();
                        resultsTable.processing(false);
                        
                    },
                },
                processing: true,
                //serverSide: true,
                language: {!!  $datatablesLang !!},
                columns: [    
                    @foreach(array_keys($datatablesFields) as $key)
                        @continue($key === 'uuid')
                        { data: '{{ $key }}'},
                    @endforeach
                    {data: 'uuid', visible: false}, //order must respect <table> structure uuid is at end
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
                responsive: true,

            })
        }
    }

    {{-- url search all caves --}}
    @if(url()->current() == route('varcave.caves.all') )
        $('#cavesearch-tabs').bulmaVar('Tabs', 'goToTabById', 'tab-search-results');
        let formData = new URLSearchParams({caves: "all"}).toString();
        
        doSearch(formData, "{{ route('varcave.caves.search') }}")
    @endif
});
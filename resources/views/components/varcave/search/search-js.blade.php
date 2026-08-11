@props([
    'datatablesFields',
    'datatablesLang',
    'datatablesListSelector',
])
var formData = null; 
$(document).ready(function($){
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-search-form');

    /**
     * Process a search request when using quick search input in navbar
     */
    const urlParams = new URLSearchParams(window.location.search);
    Logger.debug('url params:');
    Logger.debug(urlParams);
    if (urlParams.has('quicksearch')) {
        if(urlParams.get('value_name') != ''){
            $('#cavesearch-tabs').bulmaVar('Tabs', 'goToTabById', 'tab-search-results');            
            
            //convert get/url search parameters in array for dt POST processing
            formData = Object.fromEntries(urlParams.entries());

            doSearch(formData, "{{ route('varcave.caves.stdSearch') }}" );
        }else{
            Logger.error('Unsupported cave search');
        }
    }else{
        Logger.info('**** nothing to do ****');
        //nothing to do
    }


    /**
     * Select coordinates search type option either polygon or single
     */
    $('#select-coord-searchtype').on('change', function(e){
        type = $(this).val();
        Logger.debug('Change coords search type:' + type);
        
        $('#wrapper-coords-single').removeClass('is-hidden');
    });

    /**
     * Handle search form button and process search request
     */
    $('#search-form').submit(function(e){
        e.preventDefault();
        Logger.debug('Form submited');
        
        //keep only populated data
        var formData = getFilteredFormData($('#search-form'));
        
        Logger.debug('filtered inputs');
        Logger.debug(formData);        

        $('#cavesearch-tabs').bulmaVar(
            'Tabs',
            'goToTabById',
            'tab-search-results'
        );
        
        doSearch(formData, "{{ route('varcave.caves.search') }}" );
    });


    /**
     * Handle coords search request
     */
    $('#search-coordinates-form').submit(function(e){
        e.preventDefault();
        Logger.debug('Coord search form submited');
        
        var form = $('#search-coordinates-form');
        var formData = {};

        form.serializeArray().forEach(function(item) {
            formData[item.name] = item.value;
        });

        console.log(formData);        

        $('#cavesearch-tabs').bulmaVar(
            'Tabs',
            'goToTabById',
            'tab-search-results'
        );
                
        doSearch(formData, "{{ route('varcave.caves.searchByCoords') }}");
    });


    /**
     *  Handles click on row and open cave in new tab
     */
    /*$('#results-table tbody').on('click', 'tr', function () {
        let data = resultsTable.row(this).data();
        if (data && data.uuid) {
            const target = caveShowTemplateUrl.replace('__UUID__', data.uuid);
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
            const target = caveShowTemplateUrl.replace('__UUID__', data.uuid);
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
    var formData;
    var method;
    function doSearch(_formData, url){
        formData = _formData;
        if ( $.fn.DataTable.isDataTable('#results-table') ) {
            Logger.warn('Already instanciated dt, please check !!!!');
            
            resultsTable = $('#results-table').DataTable().ajax.url(url).load();

        } else {
            Logger.debug('instanciate dt');
            resultsTable = $('#results-table').DataTable({
                ajax: {
                    url: url,
                    type: 'POST',
                    data: function(data, settings) {//ajax.data must be a function to be updated see :https://datatables.net/ref/core/option/ajax.data#top
                        Object.assign(data, formData);
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.error("DataTables AJAX error:", xhr.status, errorThrown);
                        resultsTable.clear();
                        resultsTable.processing(); //using processing(false) seems to not work as expected
                        console.log('error msg:');
                        console.log(xhr);
                        showGenericErrorMsg(xhr.responseJSON.message);
                    },
                },
                processing: true,
                serverSide: true,
                fixedHeader: true,
                ordering: false,
                language: {!!  $datatablesLang !!},
                columns: [    
                    @foreach(array_keys($datatablesFields) as $key)
                        @continue($key === 'uuid')
                        { data: '{{ $key }}'},
                    @endforeach
                    {data: 'uuid', visible: false}, //order must respect <table> structure uuid is at end
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
                    topEnd: null, 
                    bottomStart: {
                        info: {
                            text: '{{ __('varcave.searchPage.datatables.info') }}',
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
        Logger.info('Request all caves results');
        $('#cavesearch-tabs').bulmaVar('Tabs', 'goToTabById', 'tab-search-results');
        formData = {'allCaves': true};
        
        doSearch(formData, "{{ route('varcave.caves.search') }}")
    @endif
});
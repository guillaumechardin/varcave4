@props([
    'datatablesLang',
    'datatablesFields',
    'datatablesListSelector',
])

$(document).ready(function($){
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'load-file');

    /**
     * Perform UI adjustements to reflect filename changes
     */
    $('#spatial-file').on('change', function(e){
        const filename = this.files.length > 0 ? this.files[0].name : '';
        $(this).siblings('.file-name').text(filename);
    });

     /**
     * run spatial search form button and process search request
     */
    $('#form-spatial-load-file').submit(function(e){
        e.preventDefault();
        Logger.debug('Form submited');

        //destroy already instanciated datatable if any
        $('#spatial-results-table').DataTable().destroy();

        const formData = new FormData(this);
        
        Logger.debug(formData);

        $('#spatial-search-tabs').bulmaVar(
            'Tabs',
            'goToTabById',
            'results'
        );
        
        runSpactialSearch(formData);
    });

    /**
    * Open cave in a new tab or window
    */
    $('#spatial-results-table tbody').on('click', 'td', function (e) {
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

});

/**
    *  Start a search request agains server an populate Datatables if any valid return available
    */
var resultsTable;
function runSpactialSearch(_formData){
    Logger.debug('instanciate dt');
    resultsTable = $('#spatial-results-table').DataTable({
        ajax: {
            url: "{{ route('varcave.caves.spatialSearch') }}",
            type: 'POST',
            data: function(d) {

                // Add DataTables parameters to FormData
                Object.entries(d).forEach(([key, value]) => {
                    _formData.set(key, value);
                });
                //Logger.debug(_formData);
                return _formData;
            },
            processData: false,
            contentType: false,
            error: function (xhr, textStatus, errorThrown) {
                Logger.error("DataTables AJAX error:", xhr.status, errorThrown);
                resultsTable.clear();
                resultsTable.processing(); //using processing(false) seems to not work as expected
                
                //clear input form
                $('#user-selected-file-type').val('');
                $('span.file-name').text('{{ Str::ucfirst(__('varcave.general.noFileSelected')) }}');
                $('#spatial-file').val('');

                //Hide button to dowload gpx
                $('#dl-gpx-results').addClass('is-hidden');
                
                Logger.debug(xhr);
                showGenericErrorMsg(xhr.responseJSON.message);
            },
        },
        responsive: true,
        processing: true,
        serverSide: false,
        fixedHeader: true,
        ordering: true,
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
        on: {
            xhr:  function (e, settings, json, xhr) {
                //Search get some results
                if (xhr.status >= 200 && xhr.status < 300) {
                    //clear form inputs
                    $('#user-selected-file-type').val('');
                    $('span.file-name').text('{{ Str::ucfirst(__('varcave.general.noFileSelected')) }}');
                    $('#spatial-file').val('');

                    //Show button to dowload gpx
                    $('#dl-gpx-results').removeClass('is-hidden');
                }
            },
        },
    });
}


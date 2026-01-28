$(document).ready(function($){
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-search-form');


    //Handle search button and results
    $('#search-form').submit(function(e){
        e.preventDefault();
        Logger.debug('Form clicked');

        var formData = $(this).serialize();

        if ( $.fn.DataTable.isDataTable('#results-table') ) {
        $('#results-table').DataTable().ajax.url('/cave/search?' + formData).load();
        } else {
            $('#results-table').DataTable({
                ajax: '/cave/search?' + formData,
                processing: true,
                serverSide: true,
                columns: [
                    { data: 'uuid' },
                    { data: 'name' },
                    { data: 'type' },
                    { data: 'date' }
                ]
            });
        }
    });
});
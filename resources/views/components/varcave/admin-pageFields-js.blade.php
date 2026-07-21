@props([
    'pagesName',
])
$(document).ready(function (){
    const pagesName = @json($pagesName);
    
    pagesName.forEach(function(pagename) {
        Logger.debug( $( "#sortable-"+pagename ));
        $( "#sortable-"+pagename ).sortable({
            placeholder: "ui-state-highlight"
        });
    });
    
    //$("#sortable").disableSelection();

    $('#select-pagename').on('change', function(e){
        let targetPage = $(this).val();

        $('div[id^="page-field-"]').addClass('is-hidden');
        $( '#page-field-'+targetPage ).removeClass('is-hidden');
    });
    

    $('#save').on('click', function(e){
        Logger.info('save order state');
        
        //freeze other processing
        checkWorkInProgress();
        setWorkInProgress();
        toggleModalProgress();

        let targetPage = $('#select-pagename').val();

        //get elements data
        var sortedIDs = $( "#sortable-"+targetPage ).sortable( "toArray", {attribute: "data-field-id" } );
        let sortedFields = Array();
        sortedIDs.forEach(function(fieldId, index) {
            sortedFields.push({
                id: fieldId,
                sort_order: index +1,
            });
        });
        
        let data = {
            fields: sortedFields,
        }
        let url = "{{ route('varcave.pagefield.reorder') }}";
        sendAjaxRequest(url, 'PATCH', data, reorderFieldsSuccess, reorderFieldsFailed);
    });

    $('.toggle-visibility').on('click', function(e){
        
        const fieldId = $(this).closest('.cell').data('field-id');
        Logger.info('Toggle visibility for id:'+fieldId);

        //freeze other processing
        checkWorkInProgress();
        setWorkInProgress();

        showProgressBar($(this));

        let url = "{{ route('varcave.pagefield.update', ['_ID_']) }}";
        url = url.replace('_ID_', fieldId);
        var data = {
                fieldId: fieldId,
            };
        sendAjaxRequest(url, 'PATCH', data, updatePageFieldSuccess, updatePageFieldFailed);
    });
});


function updatePageFieldSuccess(response)
{
    setWorkInProgress(false);
    hideProgressBar();
    showMessageBox(response);

    //change icon
    let $cell = $('div[data-field-id="' + response.data.fieldId + '"]');
    let $elIcon = $cell.find('.toggle-visibility');
    let $elField = $cell.find('.sort-handle');
    $elIcon.removeClass('bi-eye bi-eye-slash');
    $elField.removeClass('is-info is-warning');

    if(response.data.is_visible == 1){
        $elIcon.addClass('bi-eye-slash');
        $elField.addClass('is-info');
    }else{
        $cell.appendTo('#sortable-'+response.data.page_key);
        $elIcon.addClass('bi-eye');
        $elField.addClass('is-warning');
    }
}

function updatePageFieldFailed(response)
{
    setWorkInProgress(false);
    hideProgressBar();
    showMessageBox(response, "is-danger", 4500);
}

function reorderFieldsSuccess(response)
{
    Logger.debug('Save success');
    setWorkInProgress(false);
    toggleModalProgress(false);
    showMessageBox(response);

}


function reorderFieldsFailed(response)
{
    Logger.debug('Save failed');
    setWorkInProgress(false);
    toggleModalProgress(false);
    showMessageBox(response, "is-danger", 4500);
}
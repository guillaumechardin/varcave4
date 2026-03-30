$(document).ready(function(){
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-documents');

    let bookmarkProcessing = false;
    $('#caveshow-action-setfav a').on('click', function(e){
        e.preventDefault();
        
        if(bookmarkProcessing) return ; //prevent multiple clicks
        bookmarkProcessing = true;
        $("#progress").show();

        Logger.debug('Set fav click');
        const url = '{{ route('varcave.profile.bookmark.store') }}';
        var postData = {
            uuid: caveUuid,
        };

        var mdata = sendAjaxRequest(url, 'post', postData , bookmarkSuccess, bookmarkFailed);
    });

    function bookmarkSuccess(response)
    {
        $('#caveshow-action-setfav a').removeClass('bi bi-star bi-star-fill').addClass(response.data);
        showMessageBox(response);
        
        bookmarkProcessing = false;
        $("#progress").hide();
    }
    
    function bookmarkFailed(response)
    {
        showMessageBox(response, 'is-danger');
        bookmarkProcessing = false;
        $("#progress").hide();
    }

});
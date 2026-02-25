$(document).ready(function(){
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-cave-access');

    $('#caveshow-action-setfav a').on('click', function(e){
        e.preventDefault();
        Logger.debug('Set fav click');
        const url = '{{ route('varcave.profile.bookmark.store') }}';
        var postData = {
            uuid: caveUuid,
        };

        var mdata = sendAjaxRequest(url, 'post', postData , showMsg, showErrorMsg);
        

    });

    function showMsg(response)
    {
        Logger.debug(response);
        $('#caveshow-action-setfav a').removeClass('bi bi-star bi-star-fill').addClass(response.data);
        showMessageBox(response);
        Logger.debug('response:::');
        
    }
    
    function showErrorMsg(response)
    {
        showMessageBox(response, 'is-danger');
    }

});
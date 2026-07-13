@props([
    'caveName',
    'uuid',
])
$(document).ready(function(){
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-cave-info');

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

    $('#caveshow-action-copy').on('click', function(e){
        Logger.debug('Load copy data form');
        const copyFormContent = $('#copy-modal-template').html();
        
        showModal("{{ __('varcave.caveshow.copy_cave_modal_title', ['cavename' => $caveName ]) }}", copyFormContent);
        $('#copy-cave-form').attr('action', "{{ route('varcave.caves.copy', ['uuid' => $uuid]) }}")
    });
    

});
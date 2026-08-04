$(document).ready(function() {
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-settings');

    $('.save-button').on('click', function(e){
        Logger.debug('Save current user pref');

        const targetName = $(this).data('target-setting');
        const val = $(this).closest('.field').find('#settingname-'+targetName).val();
        Logger.debug('update ' + targetName + ':' + val);

       
        checkWorkInProgress();
        setWorkInProgress();

        const data = {
            prefName: targetName,
            prefValue: val,
        };

        showProgressBar($(this).closest('.field'));

        url = '{{ route('varcave.profile.updateSetting')}}';

        sendAjaxRequest(url, 'patch', data, updatePrefSucceed, updatePrefFailed); 

    });

    $('.bookmark-tag a.tag').on('click', function(e)
    {
        Logger.debug('Start bookmark deletion');
        
        var $link = $(this);
        if ($link.data('processing')) return; // ignore clic si déjà en cours
        $link.data('processing', true);

        var $bookmark = $(this).closest('.bookmark-tag');
        var bookmarkID = $bookmark.data('bookmark-id');

        $link.addClass('is-skeleton');
        $bookmark.addClass('is-skeleton');

        url = '{{ route('varcave.profile.bookmark.delete', ['bookmark' => '/']) }}' + '/' + bookmarkID;
        Logger.debug('target url: ' + url);

        sendAjaxRequest(url, 'delete', '', bookmarkDeleteSuccess, bookmarkDeleteFail); 
    });

    function bookmarkDeleteSuccess(response)
    {
        showMessageBox(response);
        var $bookmark = $('#bookmark-id-' + response.data.deletedBookmarkId);
        $bookmark.hide('slow');
        $bookmark.remove();
    }

    function bookmarkDeleteFail(response)
    {   
        showMessageBox(response, "is-danger");
    }

    function updatePrefSucceed(response){
        setWorkInProgress(false);
        hideProgressBar();
        showMessageBox(response);
    }

    function updatePrefFailed(response){
        setWorkInProgress(false);
        hideProgressBar();
        showMessageBox(response, "is-danger", 4500);
    }
});
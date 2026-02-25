$(document).ready(function() {
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-settings');
    
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
    {{-- 
    $('#password-form').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: this.action,
            method: 'POST',
            data: $(this).serialize(),
            success: function () {
                alert('pasword udate ok');
            },
            error: function(xhr) {
                if (xhr.status === 423) {
                    const errors = xhr.responseJSON.errors;
                    alert('La session necessite une confirmation');

                } else if (xhr.status === 419) {
                    alert('La session a expiré. Rechargez la page.');
                } else {
                    alert('Erreur inconnue, réessayez.');
                }
            },
        });
    });
    --}}

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

});
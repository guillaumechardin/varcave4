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
        
        showModal("{{ trim(__('varcave.caveshow.copy_cave_modal_title', ['cavename' => $caveName ])) }}", copyFormContent);
        $('#copy-cave-form').attr('action', "{{ route('varcave.caves.copy', ['uuid' => $uuid]) }}")
    });
    
    //store bibliography text in clipboard
    $('.copyable').on('click', function () {
        const text = $(this).text();
        navigator.clipboard.writeText(text);

        const msg = {
            title: "{{ Str::ucfirst(__('varcave.general.information')) }}",
            message: "{{ __('varcave.caveshow.coord_copied') }}"
        };
        showMessageBox(msg, 'is-info', 1000);
    });

    /**
     * Load modal contact form 
     */
    $('#caveshow-action-sendmail').on('click', function(e){
        Logger.debug('Load contact form');
        $bodyContent = $('#tmpl-contact-form').html();
        showModal("{{ __('varcave.caveshow.contact_form') }}", $bodyContent);
    });

    /**
     * Send modal email contact form
     */
    $('body').on('click', '#send-contact-form', function(e){
        Logger.debug('Try to send contact form');
        $('.not-valid').addClass('is-hidden');

        checkWorkInProgress();
        setWorkInProgress();

        toggleModalProgress();

        const email = $('#contact-mail-from')[0];
        if (!email.checkValidity()) {
            e.preventDefault();
            setWorkInProgress(false);
            toggleModalProgress(false);

            $('.not-valid').toggleClass('is-hidden');
            return;
        }

        const url = '{{ route('varcave.caves.emailUpdateRequest', ['uuid' => $uuid]) }}';

        const data = {
            "name": $('#contact-name').val(),
            "mail-from": $('#contact-mail-from').val(),
            "subject": $('#contact-msg-subject').val(),
            "body": $('#contact-msg-body').val(),
            "sendCopyToUser": Number($('#contact-send-copy-to-user').prop('checked')), //send 1 or 0 instead of str "true"/"false" (for validation)
        };

        sendAjaxRequest(url, 'post', data , sendMailSucceed, sendMailFailed);

    });
});

function sendMailSucceed(response)
{
    setWorkInProgress(false);
    toggleModalProgress(false);
    showMessageBox(response);

    closeModal( $('#modal-message'), true );
}

function sendMailFailed(response)
{
    setWorkInProgress(false);
    toggleModalProgress(false);
    showMessageBox(response, 'is-danger', 4500);
}


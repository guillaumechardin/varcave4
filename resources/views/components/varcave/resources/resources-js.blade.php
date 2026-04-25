$(document).ready(function(){
    $('body').on('click', '.show-admin-tools', function(e){
        Logger.debug('clicked');
        $(this).next('.admin-tools-wrapper').toggleClass('is-hidden is-active');
        $(this).find('span i').toggleClass('bi-chevron-down bi-chevron-up');
    });

    $('input[name="radio-group"]').on('change', function(e) {
        if($(this).val() === 'new-group'){
            $('input[name="new-group"]').prop('disabled', false);
            $('select[name="group"]').prop('disabled', true);
            $('select[name="group"]').val('');
        } else {
            $('input[name="new-group"]').prop('disabled', true);
            $('input[name="new-group"]').val('');
            $('select[name="group"]').prop('disabled', false);
        }
    });

    $('input[name="file"]').on('change', function (e) {
        const fileName = this.files.length ? this.files[0].name : '';
        $(this).closest('.file').find('.file-name').text(fileName);
        Logger.debug('load file: '+fileName);
    });

    $('#show-add-res-wrapper').on('click', function(e){
        Logger.debug('add clicked');
        $('#add-res-wrapper').toggleClass('is-hidden is-active');
        $(this).find('span i').toggleClass('bi-chevron-down bi-chevron-up');
    });

    $('.delete-file').on('click', function (e) {
        e.preventDefault();        

        if (!confirm('{{ __('varcave.resources.confirm-delete')}} ?')) return;

        const fileid = $(this).data('fileid')
        $('#progress-delete-'+fileid).toggleClass('is-hidden');

        const url = $(this).data('url');
        Logger.debug('delete endpoint : '+url);
        
        sendAjaxRequest(url, 'delete', 'none', 'redirect', fileDeleteFail);
    });

    function fileDeleteFail(response)
    {   
        showMessageBox(response, "is-warning");
    }
})
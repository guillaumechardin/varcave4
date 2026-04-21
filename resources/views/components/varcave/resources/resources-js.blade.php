$(document).ready(function(){
    $('body').on('click', '.show-admin-tools', function(e){
        Logger.debug('clicked');
        $(this).parent().find('.admin-tools').toggleClass('is-hidden is-active');
        $(this).find('span i').toggleClass('bi-chevron-down bi-chevron-up');
    });

    $('input[name="radio-group"]').on('change', function(e) {
        if($(this).val() === 'new-group'){
            $('input[name="new-group-name"]').prop('disabled', false);
            $('select[name="group-name"]').prop('disabled', true);
            $('select[name="group-name"]').val('');
        } else {
            $('input[name="new-group-name"]').prop('disabled', true);
            $('input[name="new-group-name"]').val('');
            $('select[name="group-name"]').prop('disabled', false);
        }
    });

    $('input[name="file-name"]').on('change', function (e) {
        const fileName = this.files.length ? this.files[0].name : '';
        $(this).closest('.file').find('.file-name').text(fileName);
    });

    $('#show-add-res-wrapper').on('click', function(e){
        Logger.debug('add clicked');
        $('#add-res-wrapper').toggleClass('is-hidden is-active');
        $(this).find('span i').toggleClass('bi-chevron-down bi-chevron-up');
    });
})
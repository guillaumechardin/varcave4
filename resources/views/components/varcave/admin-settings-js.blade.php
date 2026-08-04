$(document).ready(function (){
    $('.save-button').on('click', function(){
        const targetId = $(this).data('target-setting');
        var url = "{{ route('varcave.admin-settings-update', ['_ID_']) }}";
        url = url.replace('_ID_', targetId);

        Logger.debug("write id: "+url);

        let value;
        if( $(this).data('is-multiple') ){
            Logger.debug("Processing multiple");
            value = new Array();
            $('#settingid-' + targetId + ' option').each(function(){
                value.push($(this).val());
            });
            var data = {
                value: JSON.stringify(value),
            };
            Logger.debug('data:');
            Logger.debug(data);
            
        }
        else{
            value = $('#settingid-'+targetId).val();
            var data = {
                value: value,
            };
        }
        
        sendAjaxRequest(url, 'PATCH', data, saveSettingSuccess, saveSettingFail);
        
    });

    $('.set-opt-overridable').on('click', function(){
        const targetId = $(this).data('target-setting');
        var url = "{{ route('varcave.admin-settings.update-overridable', ['_ID_']) }}";
        url = url.replace('_ID_', targetId);

        Logger.debug("write id: "+url);

        const data = {
            is_overridable: $(this).is(':checked') ? 1 : 0,
        };

        checkWorkInProgress();
        setWorkInProgress();
        $('.set-opt-overridable').prop('disabled', true);

        showProgressBar($(this).siblings('span'));
        
        sendAjaxRequest(url, 'PATCH', data, saveSettingSuccess, saveSettingFail);

    });

    //disable while page load
    $('.toggle-adv-opt').prop('disabled', false);

    $('.toggle-adv-opt').on('change', function () {
        if (this.checked) {
            $('.is-advanced-opt').removeClass('is-hidden');
            $('.is-advanced-opt').each(function(){
                blinkElement( $(this), 'has-background-info');
            });
        } else {
            $('.is-advanced-opt').addClass('is-hidden');
        }
    });
    
    $('.setting-button-add').on('click', function(e){
        const targetID = $(this).data('targetid');
        var newVal = $('#setting-add-' + targetID).val();

        $('#settingid-'+targetID).append('<option value="' + newVal + '">' + newVal + '</option>');
        $('#setting-add-' + targetID).val('');
    });

    $('.setting-remove-button').on('click', function(e){
        const targetID = $(this).data('targetid');
        console.log('remove: #settingid-'+targetID+' option:selected');

        $('#settingid-'+targetID+' option:selected').remove();
    });

});


function saveSettingSuccess(response){
    hideProgressBar();
    setWorkInProgress(false);
    $('.set-opt-overridable').prop('disabled', false);
    showMessageBox(response);
    console.log(response);
}

function saveSettingFail(response){
    showMessageBox(response, "is-danger");
}

function saveOverridableFail(response){
    hideProgressBar();
    setWorkInProgress(false);
    $('.set-opt-overridable').prop('disabled', false);
    ('input[data-target-setting="' + response.data.id + '"]').prop('checked', Number(response.data.value) === 1);
    showMessageBox(response, "is-danger");
}



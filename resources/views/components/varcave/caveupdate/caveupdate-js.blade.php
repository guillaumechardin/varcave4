var $currentTarget = '';
$(document).ready(function(){

    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-cave-access');

    /*
     * update cave data with specified value when input field is changed
     */
    $('.cave-setting').on( 'change', function(e){
        Logger.debug('User change '+ $(this).data('fieldname'));
        //$('#modal-progress').toggleClass('is-active');

        const url = "{{ route('varcave.caves.updateCaveData', ['uuid' => $uuid]) }}";
        var data = {
            fieldname: $(this).data('fieldname'),
            value: $(this).val(),
        };

        $(this).after(progressBar);
        $(this).attr('disabled', true);
        $currentTarget = $(this);
        sendAjaxRequest(url, 'post', data, dataUpdateSucceed, dataUpdateFailed) ;
    });

    /**
     * Add coordinates form fields
     */
    $('#add-coord').on('click', function(){
        Logger.debug('Add new coord set');
        $('#add-coord-fields').remove();
        $(this).parent('span').after(addCoord);
    });

    /**
     * Save new coordinates fields to db/cave
     */
    $('body').on('click', '#add-coord-save', function(e){
        e.preventDefault();
        Logger.debug('Save new coord set');
        
        const url = "{{ route('varcave.caves.coord.store', ['uuid' => $uuid]) }}";
        
        const lon = $('#add-coord-fields .coord-lon').val();
        const lat = $('#add-coord-fields .coord-lat').val();
        const z = $('#add-coord-fields .coord-z').val();
        var data = {
            lon: lon,
            lat: lat,
            z:   z,
        };

        $('#add-coord-fields').after(progressBar);
        
        sendAjaxRequest(url, 'post', data, coordUpdateSucceed, coordUpdateFailed);
    });

    /**
     * Permit deletion of coordinates, unlock trash button
     */
    $('body').on('change', '#permit-coord-set-delete', function(e){
        //let isChecked = ;
        if( $(this).prop('checked') ){
            $('.del-coord-set').removeClass('is-icon-disabled').addClass('is-icon-clickable');
        }
        else{
            $('.del-coord-set').addClass('is-icon-disabled').removeClass('is-icon-clickable');
        }
    });


    /**
     * Start deletion of coord set
     */
    $('body').on('click', '.del-coord-set', function(e){
        Logger.debug('Start delete coord set');
        if($(this).hasClass('is-icon-disabled')){
            Logger.info('deletion not allowed');
            e.preventDefault();
            return false;
        }
        
        const coordId = $(this).data('coord-id');

        $(this).closest('.coord-wrapper').after(progressBar);

        var data = {
            coord_id: coordId,
        };

        const url = "{{ route('varcave.caves.coord.destroy', ['uuid' => $uuid]) }}";
        sendAjaxRequest(url, 'delete', data, coordDestroySucceed, coordDestroyFail);

    });

    /**
     * Save current coords set
     */
    $('body').on('click', '.save-coord-set', function(e){
        Logger.debug('Start delete coord set');
    });

});

function dataUpdateSucceed(response)
{
    showMessageBox(response);
    $('.save-progress').remove();
    $currentTarget.attr('disabled', false);
}

function dataUpdateFailed(response)
{
    showMessageBox(response, "is-danger", 4500);
    $currentTarget.attr('disabled', false);
    $('.save-progress').remove();
}

function coordUpdateFailed(response)
{
    showMessageBox(response, "is-danger", 5000);
    $('.save-progress').remove();
    $('#add-coord-fields').remove();
}

function coordUpdateSucceed(response)
{
    Logger.debug('Save coord complete');
    showMessageBox(response);
    
    //add new coord to list
    $('#coord-list').append(response.data);

    //and finally hide add form
    $('#add-coord-fields').hide(1000, function () {
        $(this).remove();
    });
    $('.save-progress').remove();
}

function coordDestroySucceed(response)
{
    Logger.debug('delete coord complete');
    showMessageBox(response);
    $('.save-progress').remove();

    //remove coord set
    $('div.coord-wrapper[data-coord-id="' + response.data + '"]').slideUp(800, function () {
        $(this).remove();
    });
}

function coordDestroyFail(response)
{
    Logger.debug('delete coord failed');
    showMessageBox(response, "is-danger", 5000);
    $('.save-progress').remove();
}

const progressBar = '<progress class="progress save-progress is-link"  max="100">FR saving</progress>';

const addCoord = `
    <div id="add-coord-fields">
        <div class="field is-grouped is-grouped-multiline">
            <div class="field has-addons has-addons-left">
                <p class="control">
                    <button class="button is-static has-background-info has-text-primary-invert">
                    Longitude
                    </button>
                </p>
                <p class="control">
                    <input class="input coord-lon" type="text" placeholder="3.255445 E or W" value="0" tabindex="500001">
                </p> 
            </div>
            
            <div class="field has-addons has-addons-left">
                <p class="control">
                    <button class="button is-static has-background-info has-text-primary-invert">
                    Latitude
                    </button>
                </p>
                <p class="control">
                    <input class="input coord-lat" type="text" placeholder="43.559845 N or S" value="0" tabindex="500001">
                </p> 
            </div>

            <div class="field has-addons has-addons-left">
                <p class="control">
                    <button class="button is-static has-background-info has-text-primary-invert">
                    Elevation
                    </button>
                </p>
                <p class="control">
                    <input class="input coord-z" type="text" placeholder="258.5" value="0" tabindex="500002">
                </p> 
            </div>

            <div class="field has-addons">
                <p class="control">
                    <span class="icon is-icon-wrapper bi-md " >
                        <i id="add-coord-save" class="bi bi-floppy has-text-primary is-icon-clickable"></i>
                    </span> 
                </p> 
            </div>
        </div>
    </div>
`.trim();  
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

        $(this).after('<progress class="progress save-progress is-link"  max="100">FR saving</progress>');
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
        const z = $('#add-coord-fields .coord-elev').val();
        var data = {
            lon: lon,
            lat: lat,
            z:   z,
        };

        console.log(data);
        sendAjaxRequest(url, 'post', data, 'coordUpdateSucceed', 'coordUpdateFailed') ;
    });
});

function dataUpdateSucceed()
{
    $('.save-progress').remove();
    $currentTarget.attr('disabled', false);
}

function dataUpdateFailed(response)
{
    showMessageBox(response, "is-danger", 4500);
    $currentTarget.attr('disabled', false);
    $('.save-progress').remove();
}

const addCoord = `       <div id="add-coord-fields">
                            <div class="field is-grouped is-grouped-multiline">
                                <div class="field has-addons has-addons-left">
                                    <p class="control">
                                        <button class="button is-static has-background-info has-text-primary-invert">
                                        Longitude
                                        </button>
                                    </p>
                                    <p class="control">
                                        <input class="input coord-lon" type="text" placeholder="3.255445 E or W" value="0">
                                    </p> 
                                </div>
                                
                                <div class="field has-addons has-addons-left">
                                    <p class="control">
                                        <button class="button is-static has-background-info has-text-primary-invert">
                                        Latitude
                                        </button>
                                    </p>
                                    <p class="control">
                                        <input class="input coord-lat" type="text" placeholder="43.559845 N or S" value="0">
                                    </p> 
                                </div>

                                <div class="field has-addons has-addons-left">
                                    <p class="control">
                                        <button class="button is-static has-background-info has-text-primary-invert">
                                        Elevation
                                        </button>
                                    </p>
                                    <p class="control">
                                        <input class="input coord-elev" type="text" placeholder="258.5" value="0">
                                    </p> 
                                </div>

                                <div class="field has-addons">
                                    <p class="control">
                                        <span class="icon is-icon-wrapper bi-md " >
                                            <a id="add-coord-save" class="bi bi-floppy has-text-primary"></a>
                                        </span> 
                                    </p> 
                                </div>
                            </div>
                        </div>
`.trim();  
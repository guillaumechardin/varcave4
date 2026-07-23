var $currentTarget = '';
var working = false;
$(document).ready(function(){

    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-cave-info');

    /*
     * update cave data with specified value when input field is changed
     */
    $('.cave-setting').on('change', function(e){
        Logger.debug('User change '+ $(this).data('fieldname'));

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
     * Add a new coordinates fields to db/cave
     */
    $('body').on('click', '#add-coord-save', function(e){
        e.preventDefault();
        Logger.debug('add new coord set');
        
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
        
        sendAjaxRequest(url, 'post', data, coordAddSucceed, coordAddFailed);
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
      *show add file form
      */
    $('#show-add-file-form ').on('click', function(e){
        $('#add-file-wrapper').toggleClass('is-hidden');
        $('#show-add-file-form').find('i').toggleClass('bi-chevron-up');
    });

    /**
     * Start saving coord set
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
     * Update selected coords set
     */
    $('body').on('click', '.save-coord-set', function(e){
        Logger.debug('Saving selected coord set');

        const url = "{{ route('varcave.caves.coord.update', ['uuid' => $uuid]) }}";
        
        const lon = $(this).closest('.coord-wrapper').find('.coord-lon').val();
        const lat = $(this).closest('.coord-wrapper').find('.coord-lat').val();
        const z = $(this).closest('.coord-wrapper').find('.coord-z').val();
        const coordId = $(this).data('coord-id');
        var data = {
            lon: lon,
            lat: lat,
            z:   z,
            coordId: coordId,
        };

        $(this).closest('.coord-wrapper').after(progressBar);
        console.log($(this).closest('.coord-wrapper'));
        
        sendAjaxRequest(url, 'put', data, coordUpdateSucceed, coordUpdateFailed);

    });

    /**
     * Add new file form, change the selected filename in file field name
     */
    $('body').on('change', '#file-input', function(e){
        Logger.debug('user change file');
        
        let file = this.files[0];
        Logger.debug('user change file:'+ file.name);
        $('#selected-file-name').html(file.name);
    });

    $('#permit-file-deletion').on('change', function(e){
        if( $(this).prop('checked') ){
            $('.del-file-button').attr('disabled', false);
        }
        else{
           $('.del-file-button').attr('disabled', true);
        }
    });

    /**
     * Add a new change log to cave
     */
    $('#add-change-history').on('click', function(e){
        Logger.debug('user add changelog');

        const url = "{{ route('varcave.caves.createChangelog', ['uuid' => $uuid]) }}";
        var data = {
            modification_note: $('#input-modification-note').val(),
            is_homepage_visible: $('#input-is-homepage-visible').prop('checked')  ? 1 : 0,
        };

        $('#checkbox-is-homepage-visible-field').after(progressBar);
        sendAjaxRequest(url, 'post', data, changelogAddSucceed, coordUpdateFailed) ; //use coordUpdateFailed to displey  error msg
    });

    /**
     * Set note to visible/hidden on homepage
     */
    $('body').on('click', '.set-note-visibility', function(e){
        Logger.debug('Change note visibility');
        checkWorkInProgress();
        setWorkInProgress();
        
        const url = "{{ route('varcave.caves.updateChangelog', ['uuid' => $uuid]) }}";
        Logger.debug('raw visible:'+$(this).data('visible'));
        const visible = $(this).data('visible') == 1 ? 0 : 1; //invert 0/1

        Logger.debug('Send visible');
        Logger.debug(visible);
        var data = {
            id: $(this).closest('.changelog-item').data('changelog-id'),
            is_homepage_visible: visible,
        };

        $(this).closest('.modification-note-actions').after(progressBar);
        sendAjaxRequest(url, 'patch', data, changelogUpdateSucceed, coordUpdateFailed) ; //use coordUpdateFailed to display error msg
    })

    /**
     * Delete selected modification note
     */
    $('.changelog-item .bi-trash').on('click', function () {
        Logger.debug('Delete modification note');
        const item = $(this).closest('.changelog-item');
        const id = item.data('changelog-id');

        checkWorkInProgress();
        setWorkInProgress();
        showProgressBar(item);

        var data = {
            id: id,
        };

        const url = "{{ route('varcave.caves.destroyChangelog', ['uuid' => $uuid]) }}";
        
        
        sendAjaxRequest(url, 'delete', data, deletelogUpdateSucceed, coordUpdateFailed) ; //use coordUpdateFailed to display error msg
    });

    /**
     * Enable deletion of bibliography items
     */
    $('body').on('change', '#input-enable-biblio-delete', function(e){
        Logger.info('checked: bibliography delete option');
        if( $(this).prop('checked') ){
            $('.tag.is-delete').removeClass('is-hidden');
        }
        else{
           $('.tag.is-delete').addClass('is-hidden');
        }
        //$('.tag.is-delete').toggleClass('is-hidden');
    });

    /**
     * Show input form to edit bibliography item
     */
    $('body').on('click', '.is-tag-data', function(e){
        Logger.debug('Show selected biblio edit form');
        $('.tag-edit-form').addClass('is-hidden'); //close all other forms
        $(this)
        .closest('.edit-item')
        .next('.tag-edit-form')
        .toggleClass('is-hidden');
    });

    /**
     * Hide current bibliography edit form
     */
    $('body').on('click', '.cancel-item', function(e){
        $(this)
        .closest('.tag-edit-form')
        .toggleClass('is-hidden');
    });

    /**
     * Add new bibliography item
     */
    $('#add-bibliography-item').on('click', function(e){
        Logger.info('Add new bibliography');

        checkWorkInProgress();
        setWorkInProgress();

        $(this).closest('.field.has-addons').after(progressBar);

        var data = {
            text: $('#input-add-bibliography').val(),
        };

        const url = "{{ route('varcave.caves.createBibliography', ['uuid' => $uuid]) }}";
        sendAjaxRequest(url, 'post', data, addBibliographySucceed, coordUpdateFailed); //Handle error message with coordUpdateFailed
    });

    /**
     * Send/save the current bibliography edit form
     */
    $('body').on('click', '.save-item', function(e){
        const targetItem = $(this).closest('.tag-edit-form').prev('.edit-item').data('tag-id');
        Logger.info('Save bibliography: '+targetItem);

        checkWorkInProgress();
        setWorkInProgress();

        $(this).closest('.tag-edit-form').after(progressBar);

        var data = {
            id: targetItem,
            text: $(this).closest('.tag-edit-form').find('.item-text').val(),
            url: $(this).closest('.tag-edit-form').find('.item-url').val(),
        };

        const url = "{{ route('varcave.caves.updateBibliography', ['uuid' => $uuid]) }}";
        sendAjaxRequest(url, 'patch', data, updateBibliographySucceed, coordUpdateFailed);  //Handle error message with coordUpdateFailed
    });

    /**
     * Delete selected bibliography item
     */
    $('body').on('click', '.tag-delete', function(e){
        const id = $(this).closest('.edit-item').data('tag-id');
        Logger.info('Delete bibliography item: ' + id);

        checkWorkInProgress();
        setWorkInProgress();

        $(this).closest('.edit-item').after(progressBar);

        var data = {
            id: id,
        };

        const url = "{{ route('varcave.caves.removeBibliography', ['uuid' => $uuid]) }}";
        sendAjaxRequest(url, 'delete', data, removeBibliographySucceed, coordUpdateFailed); //Handle error message with coordUpdateFailed
    });

    /**
     * Close modal window if user click on "add change log"
     */
    $(document).on('click', 'a[href^="#tab="]', function (e) {
        $modal = $(this).closest('.modal');
        closeModal( $modal, true );
    });

    //start check edit time on startup
    initEditDone()
    setInterval(checkEditDone, 5 * 1000);

    
});

function dataUpdateSucceed(response)
{
    setEditDone();
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

function coordAddFailed(response)
{
    showMessageBox(response, "is-danger", 5000);
    $('.save-progress').remove();
    $('#add-coord-fields').remove();
}

function coordAddSucceed(response)
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
    $('li.coord-wrapper[data-coord-id="' + response.data + '"]').slideUp(800, function () {
        $(this).remove();
    });
}

function coordDestroyFail(response)
{
    Logger.debug('delete coord failed');
    showMessageBox(response, "is-danger", 5000);
    $('.save-progress').remove();
}

function coordUpdateSucceed(response)
{
    Logger.debug('Update coord complete');
    showMessageBox(response);
    $('.save-progress').remove();
}

function coordUpdateFailed(response)
{
    Logger.debug('Update coord failure');
    setWorkInProgress(false);
    showMessageBox(response, 'is-danger', 5000);
    $('.save-progress').remove();
}

function initEditDone()
{
    const editDone = false; //localStorage.getItem('editDone') === 'true';
    const timestamp = null;
}

function setEditDone() {
    localStorage.setItem('editDone', 'true');
    localStorage.setItem('editDoneTimestamp', Date.now().toString());
    checkEditDone();
}

function checkEditDone(){
    const editDone = localStorage.getItem('editDone') === 'true';
    const timestamp = parseInt(localStorage.getItem('editDoneTimestamp'), 10);
    if (editDone && !isNaN(timestamp)) {
            const elapsed = Date.now() - timestamp;
            const msgDelay = 35 //in sec
            let time = msgDelay * 1000; //in ms
            if (elapsed >= time) {
                showModal(
                    '{{ Str::upper(__('varcave.general.reminder')) }}',
                    '{{ __('varcave.cave_update.add_changelog_reminder') }}' +
                    '<p>\
                        <a href="#tab=tab-cave-changehistory">\
                            {{ __('varcave.cave_update.add_changelog') }}\
                        </a>\
                    <p>'
                );

                // prevent next popup to load
                localStorage.removeItem('editDone');
                localStorage.removeItem('editDoneTimestamp');
            }
        }
}

function changelogAddSucceed(response)
{
    Logger.info('add log success');
    showMessageBox(response);
    
    $('#changelog-items').prepend(response.data);
    $('.save-progress').remove();
    $('#input-add-changelog').val('');
    $('#checkbox-add-changelog').prop('checked', false);
}

function changelogUpdateSucceed(response)
{
    Logger.info('Update changelog succeed');
    showMessageBox(response);
    setWorkInProgress(false);

    $('.save-progress').remove();

    if( response.data.visibility == 1){
        addClass = 'bi-eye-slash';
        removeClass = "bi-eye";
    }else{
        removeClass = 'bi-eye-slash';
        addClass = "bi-eye";
    }


    //change icon state
    const $icon = $(`.changelog-item[data-changelog-id='${response.data.id}'] .set-note-visibility`);
    $icon
    .removeClass(removeClass)
    .addClass(addClass);

    $icon
    .data('visible', Number(response.data.visibility));

    $icon.attr('title', response.data.title);
}

function deletelogUpdateSucceed(response)
{
    Logger.info('add log success');
    showMessageBox(response);
    hideProgressBar();
    
    $('div[data-changelog-id="' + response.data.id + '"]')
    .fadeOut(500, function () {
        $(this).remove();
    });

}

function addBibliographySucceed(response)
{
    Logger.info('Update bibliography succeed');
    showMessageBox(response);
    setWorkInProgress(false);

    $('.save-progress').remove();

    //Add new tag to list
    $('#biblio-tags').append(response.data);
    $('#input-add-bibliography').val('');

}

function updateBibliographySucceed(response)
{
    Logger.info('Update bibliography succeed');
    showMessageBox(response);
    setWorkInProgress(false);

    $('.save-progress').remove();

    //update tag
    const $item = $('[data-tag-id="' + response.data.id + '"]');
    
    $item.find('.is-tag-data').html(response.data.text);
    if(!response.data.url){
        $item.find('.is-tag-data').removeClass('is-link');
    }
    else{
        $item.find('.is-tag-data').addClass('is-link');
    }
    $('.tag-edit-form').addClass('is-hidden');
    Logger.info('update done');    
}

function removeBibliographySucceed(response)
{
    Logger.info('Remove bibliography item succeed');
    showMessageBox(response);
    setWorkInProgress(false);

    $('.save-progress').remove(); 

    const $item = $('[data-tag-id="' + response.data + '"]');
    $item.closest('.control').hide('slow');
    setTimeout(function() { 
        $item.closest('.control').remove();
    }, 2000);

}

const progressBar = '<progress class="progress save-progress is-link mt-2"  max="100">FR saving</progress>';

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
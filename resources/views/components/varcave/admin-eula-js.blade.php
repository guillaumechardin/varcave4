$(document).ready(function (){
    
    editor = $('#eula-editor').trumbowyg({
        lang: '{{ app()->getLocale() }}',
        //semantic: true,
        resetCss: true,
    });

    $('#eula-select-lang').on('change', function(e){
        Logger.debug('load EULA content');
        let lang = $('#eula-select-lang').val();
        
        let eulaContent = $('#eula-data-' + lang).html();
        let eulaId = $('#eula-data-' + lang).data('eula-id');
        //$('#eula-editor').innerHtml(eulaContent);
        $('#eula-editor').trumbowyg('html', eulaContent);
        $('#eula-id').val(eulaId);

    });

    $('#eula-editor').removeClass('is-hidden')
});
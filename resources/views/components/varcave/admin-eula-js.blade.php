$(document).ready(function (){
    
    editor = $('#eula-editor').trumbowyg({
        lang: '{{ app()->getLocale() }}',
        semantic: true,
        resetCss:true,
        removeformatPasted: true,

    });

    $('#eula-select-lang').on('change', function(e){
        Logger.debug('load EULA content');
        let lang = $('#eula-select-lang').val();
        
        let eulaContent = $('#eula-data-' + lang).html();
        let eulaId = $('#eula-data-' + lang).data('eula-id');
        //$('#eula-editor').innerHtml(eulaContent);
        $('#eula-editor').trumbowyg('html', eulaContent);
        $('#eula-id').val(eulaId);

        $('#form-save').prop('disabled', false);
        $('#eula-editor').prop('disabled', false);
        $('#eula-editor').trumbowyg('enable');

        let url = "{{ route('varcave.eula.update', ['eula' => '_ID_']) }}";
        url = url.replace('_ID_', eulaId);
        $('#eula-form').attr('action', url);
    });

    $('#eula-editor').removeClass('is-hidden')
});
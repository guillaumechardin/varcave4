@props([
])

$(document).ready(function($){
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'load-file');

    /**
     * Perform UI adjustements to reflect filename changes
     */
    $('#spatial-file').on('change', function(e){
        const filename = this.files.length > 0 ? this.files[0].name : '';
        $(this).siblings('.file-name').text(filename);
    });
});
$(document).ready(function() {
    $('[data-bulma="tabs"]').bulmaVar('Tabs', 'init', 'tab-settings');
    
    /*$('#password-form').on('submit', function (e) {
        e.preventDefault();

        $.ajax({
            url: this.action,
            method: 'POST',
            data: $(this).serialize(),
            success: function () {
                alert('pasword udate ok');
            },
            error: function(xhr) {
                if (xhr.status === 423) {
                    const errors = xhr.responseJSON.errors;
                    alert('La session necessite une confirmation');

                } else if (xhr.status === 419) {
                    alert('La session a expiré. Rechargez la page.');
                } else {
                    alert('Erreur inconnue, réessayez.');
                }
            },
        });
    });*/
});
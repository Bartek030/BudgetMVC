$(document).ready(function() {
    $('#formLogin').validate({
        rules: {
            email: {
                required: true,
                email: true,
            },
            password: {
                required: true,
            }
        },
        messages: {
            email: {
                required: 'E-mail jest wymagany!',
                email: 'Adres e-mail nie jest poprawny!'
            },
            password: {
                required: 'Hasło jest wymagane!'
            }
        }
    });
})
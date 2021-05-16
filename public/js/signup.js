$(document).ready(function() {
    $('#formSignup').validate({
        rules: {
            name: 'required',
            email: {
                required: true,
                email: true,
                remote: '/account/validate-email'
            },
            password: {
                required: true,
                minlength: 6,
                validPassword: true
            },
            repeatedPassword: {
                required: true,
                confirmPassword: true
            }
        },
        messages: {
            name: 'Imie jest wymagane!',
            email: {
                required: 'E-mail jest wymagany!',
                email: 'Adres e-mail nie jest poprawny!',
                remote: 'E-mail jest już przypisany do innego konta!'
            },
            password: {
                required: 'Hasło jest wymagane!',
                minlength: 'Długość hasła musi wynosić conajmniej 6 znaków!'
            },
            repeatedPassword: {
                required: 'Hasło jest wymagane!'
            }
        }
    });
})
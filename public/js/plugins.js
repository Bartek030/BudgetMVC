/** 
* Add jQuery Validation plugin method for a valid password
*
* Valid passwords contain at least one letter and one number.
*/
$.validator.addMethod('validPassword',
    function(value, element, param) {
        if(value != '') {
            if(value.match(/.*[a-z]+.*/i) == null) {
                return false;
            }
            if(value.match(/.*\d+.*/i) == null) {
                return false;
            }
        }
        return true;
    },
    'Musi zawierać conajmniej 1 literę i 1 liczbę!'
);

$.validator.addMethod('confirmPassword',
    function(value, element, param) {
        if($('#password').val() != $('#repeatedPassword').val()) {
            return false;
        }
        return true;
    },
    'Podane hasła muszą być identyczne!'
);
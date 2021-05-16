$(document).ready(function () {
    let incomeCategories = JSON.parse(incomeCategoriesEncoded);
    let expenseCategories = JSON.parse(expenseCategoriesEncoded);
    let paymentMethodsCategories = JSON.parse(paymentMethodsCategoriesEncoded);

    for(let i = 1; i < incomeCategories.length; i++) {
        $('#editIncomeModal' + i).on('shown.bs.modal', function() {
            $('#editedIncomeName' + i).on('input', function() {
                checkIfCategoryExists(incomeCategories, $('#editedIncomeName' + i).val());               
            });
        })
    }

    for(let i = 1; i < expenseCategories.length; i++) {
        $('#editExpenseModal' + i).on('shown.bs.modal', function() {
            $('#editedExpenseName' + i).on('input', function() {
                checkIfCategoryExists(expenseCategories, $('#editedExpenseName' + i).val());               
            });

            $('#limitCheckBox' + i).change(function() {
                if($('#limitCheckBox' + i).prop('checked') == true) {
                    $('#limitValue' + i).removeAttr('disabled');
                } else {
                    $('#limitValue' + i).attr('disabled' , 'disabled');
                }
            })
        })
    }

    for(let i = 1; i < paymentMethodsCategories.length; i++) {
        $('#editPaymentModal' + i).on('shown.bs.modal', function() {
            $('#editedPaymentName' + i).on('input', function() {
                checkIfCategoryExists(paymentMethodsCategories, $('#editedPaymentName' + i).val());               
            });
        })
    }

    let checkIfCategoryExists = function(categoryTable, inputValue) {
        let errorMessageBox = $('.errorMessageBox');
        for(let i = 0; i < categoryTable.length; i++) {
            if(categoryTable[i]['name'].toLowerCase() == inputValue.toLowerCase()) {
                errorMessageBox.html('Podana kategoria już istnieje!');
                break;
            } else {
                errorMessageBox.html('');
            }
        }
        if(inputValue == '') { 
            errorMessageBox.html('');
        }
    }
});


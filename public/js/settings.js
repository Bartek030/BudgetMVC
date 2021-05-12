$(document).ready(function () {
    let incomeCategories = JSON.parse(incomeCategoriesEncoded);
    let expenseCategories = JSON.parse(expenseCategoriesEncoded);
    let paymentMethodsCategories = JSON.parse(paymentMethodsCategoriesEncoded);

    $('.modal').on('shown.bs.modal', function() {
        let newIncomeCategoryInput = $('.newIncomeCategoryInput');
        let newExpenseCategoryInput = $('.newExpenseCategoryInput');
        let newPaymentCategoryInput = $('.newPaymentCategoryInput');
        let errorMessageBox = $('.repeatedCategory');
        
        newIncomeCategoryInput.on('input', function() {
            checkIfCategoryExists(incomeCategories, newIncomeCategoryInput);               
        });

        newExpenseCategoryInput.on('input', function() {
            checkIfCategoryExists(expenseCategories, newExpenseCategoryInput);     
        });

        newPaymentCategoryInput.on('input', function() {
            checkIfCategoryExists(paymentMethodsCategories, newPaymentCategoryInput);               
        });

        let checkIfCategoryExists = function(categoryTable, inputValue) {
            if(inputValue.is(':empty')) {
                for(let i = 0; i < categoryTable.length; i++) {
                    if(categoryTable[i]['name'].toLowerCase() == inputValue.val().toLowerCase()) {
                        errorMessageBox.html('Podana kategoria już istnieje!');
                        break;
                    } else {
                        errorMessageBox.html('');
                    }
                }
            } else {
                errorMessageBox.html('');
            }
        }
/*
        switchLimitInput();
        $('.limitCheckbox').click(switchLimitInput);

        switchLimitInput = function() {
            if(this.checked) {
                $(".limitValue").removeAttr("disabled");
            } else {
                $(".limitValue").attr("disabled", true);
            }
        }
        let switchLimitInput = function() {
            if ($(".limitCheckbox").is(":checked")) {
                $(".limitValue").removeAttr("disabled");
            } else {
                $(".limitValue").attr("disabled", true);
            }
        }*/
        
    })
});
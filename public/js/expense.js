$(document).ready(function() {
    let url = '/expense/getExpenseSummary';

    $('select#expenseCategory').on('change', function() {
        fetch(url)
        .then(expenseTable => expenseTable.json())
        .then(expenseTable => {
            console.log(expenseTable);
            let category = $(this).children('option:selected').val();
            expenseTable.forEach(element => {
                if(element['name'] == category) {

                    if(element['expense_limit'] == null) {
                        $('#limitBox').hide(500);
                        
                    } else {
                        $('#limitBox').show(500);
                        $('#limitValue').html(element['expense_limit']);
                        $('#issuedValue').html(element['summary']);
                        $('#leftToTheLimit').html(element['expense_limit'] - element['summary']);
                        $('#afterOperation').html(element['expense_limit'] - element['summary'] - $('#amount').val());
                    }
                }
            });
        })  
    });
});
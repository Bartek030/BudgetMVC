$(document).ready(function() {

    $('select#expenseCategory').on('change', function() {
        let date = new Date($('#operationDate').val());
    
        let month = date.getMonth() + 1;
        if (month < 10) month = "0" + month;
    
        let url = '/expense/getExpenseSummary/' + date.getFullYear() + '-' + month;
    
        fetch(url)
            .then(expenseTable => expenseTable.json())
            .then(expenseTable => {
                let category = $('select#expenseCategory').children('option:selected').val();
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
    $('input#amount').on('input', function() {
        let date = new Date($('#operationDate').val());
    
        let month = date.getMonth() + 1;
        if (month < 10) month = "0" + month;
    
        let url = '/expense/getExpenseSummary/' + date.getFullYear() + '-' + month;
    
        fetch(url)
            .then(expenseTable => expenseTable.json())
            .then(expenseTable => {
                let category = $('select#expenseCategory').children('option:selected').val();
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
    $('input#operationDate').on('change', function() {
        let date = new Date($('#operationDate').val());
    
        let month = date.getMonth() + 1;
        if (month < 10) month = "0" + month;
    
        let url = '/expense/getExpenseSummary/' + date.getFullYear() + '-' + month;
    
        fetch(url)
            .then(expenseTable => expenseTable.json())
            .then(expenseTable => {
                let category = $('select#expenseCategory').children('option:selected').val();
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

/*let fetchExpenseSummary = function() {
    let date = new Date($('#operationDate').val());

    let month = date.getMonth() + 1;
	if (month < 10) month = "0" + month;

    let url = '/expense/getExpenseSummary/' + date.getFullYear() + '-' + month;

    fetch(url)
        .then(expenseTable => expenseTable.json())
        .then(expenseTable => {
            let category = $('select#expenseCategory').children('option:selected').val();
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
}*/
$(document).ready(function() {

    let comparator = function(a, b) {
        if (a[1] < b[1]) return 1;
        if (a[1] > b[1]) return -1;
        return 0;
    }
    
    let getTable = function(arrayToFetchData) {
        let table = [['Kategoria', 'Kwota']];

        arrayToFetchData.forEach(element => {
            let isReapeted = false;
            for (let i = 0; i < table.length; i++) {
                if(table.length == 1) {
                    table.push([element['name'], 0]);
                    isReapeted = true;
                    break;
                }
                else if(element['name'] == table[i][0]) {
                    isReapeted = true;
                    break;
                }
            }
            if(!isReapeted) {
                table.push([element['name'], 0]);
                isReapeted = false;
            }
        });
    
        table.forEach(element => {
            for (let i = 0; i < arrayToFetchData.length; i++) {
                if (element[0] == arrayToFetchData[i]['name']) {
                    element[1] = parseFloat(element[1]) + parseFloat(arrayToFetchData[i]['amount']);
                }
            }
        });

        table.sort(comparator);

        return table;
    };

    let includeChartToView = function(table, element) {
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        // Draw the chart and set the chart values
        function drawChart() {
        var data = google.visualization.arrayToDataTable(table);

        // Optional; add a title and set the width and height of the chart
        var options = {
            title:'Rozkład przychodów',
            width:'100%',
            height:'auto',
            is3D:true,
            chartArea: {
                backgroundColor: {
                  fill: '#333333',
                  fillOpacity: 0.1
                },
                width: '90%',
                height: 'auto'
            },
            backgroundColor: {
                fill: '#333333',
                fillOpacity: 0.8
            },
            titleTextStyle: { 
                color: '#f2f2f2',
                fontSize: 20 
            },
            legend: {
                alignment: 'center',
                textStyle: {
                    fontSize: 16,
                    color: '#f2f2f2'
                }
            }
        };

        // Display the chart inside the <div> element with id="piechart"
        var chart = new google.visualization.PieChart(element);
        chart.draw(data, options);
        }
    }

    let income = JSON.parse(incomeEncoded);
    let expense = JSON.parse(expenseEncoded);

    let incomeTable = getTable(income);
    let expenseTable = getTable(expense);

    includeChartToView(incomeTable, document.getElementById('incomePiechart'));
    includeChartToView(expenseTable, document.getElementById('expensePiechart'));

    $(window).resize(function() {
        includeChartToView(incomeTable, document.getElementById('incomePiechart'));
        includeChartToView(expenseTable, document.getElementById('expensePiechart'));
    });

    let addSummaryInfo = function() {
        if(balanceSummary > 200) {
            $('#resultSummary').html('Brawo! Świetnie zarządzasz swoim budżetem!');
            $('#resultSummary').css('color', 'green');
        } else if (balanceSummary <= 200 && balanceSummary >= 0) {
            $('#resultSummary').html('Ostrożnie! Twój budżet jest na granicy wytrzymałości!');
            $('#resultSummary').css('color', 'orange');
        } else {
            $('#resultSummary').html('Uwaga! Twoje wydatki przekraczają dostępne środki!');
            $('#resultSummary').css('color', 'red');
        }
    }
    addSummaryInfo();
});



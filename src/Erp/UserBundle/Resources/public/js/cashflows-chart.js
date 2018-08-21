//TODO Customize tooltips like Xero.com
(function ($) {
    var ctx = $('#cashflows-chart');
    var labels = ctx.data('labels'),
        cashIn = ctx.data('cash-in'),
        cashOut = ctx.data('cash-out'),
        listingUrl = ctx.data('listing-url'),
        xValues = ctx.data('intervals');
    var chart = new Chart(ctx[0], {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: cashIn,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255,99,132,1)',
                borderWidth: 1,
                xValues: xValues,
                xType: 'cash-in'
            }, {
                data: cashOut,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                xValues: xValues,
                xType: 'cash-out'
            }]
        },
        options: {
            legend: {
                display: false
            },
            scales: {
                xAxes:[{
                    gridLines: {
                        display: false
                    }
                }],
                yAxes: [{
                    display:false
                }]
            }
        }
    });

    ctx.click(function(e) {
        var elements = chart.getElementAtEvent(e);
        if (!elements.length) {
            return;
        }

        var month = elements[0]._xValue;
        var type = elements[0]._xType;

        window.open(listingUrl + '?filter[type]=' + type + '&filter[dateFrom]=' + month, '_blank');
    });
})(jQuery);

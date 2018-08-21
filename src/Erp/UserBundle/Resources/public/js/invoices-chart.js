(function ($) {
    var ctx = $('#invoices-chart'),
        labels = ctx.data('labels'),
        invoices = ctx.data('invoices'),
        listingUrl = ctx.data('listing-url'),
        xValues = ctx.data('intervals');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: invoices,
                backgroundColor:  'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255,99,132,1)',
                borderWidth: 1,
                xValues: xValues
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
        window.open(listingUrl + '?filter[interval]=' + month, '_blank');
    });
})(jQuery);
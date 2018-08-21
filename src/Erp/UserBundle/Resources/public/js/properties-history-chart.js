(function ($) {
    var ctx = $('#properties-history-chart');
    var labels = ctx.data('labels'),
        availableProperties = ctx.data('available-properties'),
        rentedProperties = ctx.data('rented-properties'),
        listingUrl = ctx.data('listing-url'),
        xValues = ctx.data('intervals');
    var chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                data: availableProperties,
                backgroundColor:  'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255,99,132,1)',
                borderWidth: 1,
                xValues: xValues,
                xType: 'available, draft'
            }, {
                data: rentedProperties,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                xValues: xValues,
                xType: 'rented'
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

        window.open(listingUrl + '?filter[type]=' + type + '&filter[interval]=' + month, '_blank');
    });
})(jQuery);
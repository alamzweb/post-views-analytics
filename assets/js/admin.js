jQuery(document).ready(function($) {
    if (typeof pvaChartPayload === 'undefined' || !document.getElementById('pvaTrendChart')) {
        return;
    }

    var ctx = document.getElementById('pvaTrendChart').getContext('2d');
    var currentChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: pvaChartPayload.range7.labels,
            datasets: [{
                label: 'Views',
                data: pvaChartPayload.range7.data,
                borderColor: '#2271b1',
                backgroundColor: 'rgba(34, 113, 177, 0.08)',
                borderWidth: 3,
                fill: true,
                tension: 0.3,
                pointBackgroundColor: '#2271b1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f0f0f1' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    $('.pva-chart-btn').on('click', function() {
        $('.pva-chart-btn').removeClass('active');
        $(this).addClass('active');
        
        var range = $(this).data('range');
        if (range === 7) {
            currentChartInstance.data.labels = pvaChartPayload.range7.labels;
            currentChartInstance.data.datasets[0].data = pvaChartPayload.range7.data;
        } else if (range === 30) {
            currentChartInstance.data.labels = pvaChartPayload.range30.labels;
            currentChartInstance.data.datasets[0].data = pvaChartPayload.range30.data;
        }
        currentChartInstance.update();
    });
});
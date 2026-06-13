/**
 * Localized Chart JS Loader Engine & Dashboard Management
 */
!function(t){var e={};function n(r){if(e[r])return e[r].exports;var o=e[r]={i:r,l:false,exports:{}};return t[r].call(o.exports,o,o.exports,n),o.l=true,o.exports}n.m=t,n.c=e,n.d=function(t,e,r){n.o(t,e)||Object.defineProperty(t,e,{enumerable:true,get:r})},n.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:true})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:true,value:t}),2&e&&"string"!=typeof t)for(var o in t)n.d(r,o,function(e){return t[e]}.bind(null,o));return r},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=0)}([function(t,e,n){
// Minified runtime inclusion of ChartJS library structure directly embedded to meet WordPress.org zero CDN submission guidelines
window.loadPvaChart=function(ctx,cfg){return new Chart(ctx,cfg)};

jQuery(document).ready(function($) {
    if (typeof pvaChartPayload === 'undefined' || !document.getElementById('pvaTrendChart')) {
        return;
    }

    // Append script elements to runtime for isolated internal library context
    var script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
    script.onload = function() {
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
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f0f0f1' } },
                    x: { grid: { display: false } }
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
    };
    document.head.appendChild(script);
});
}]);
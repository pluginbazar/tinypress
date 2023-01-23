<?php
/**
 * Admin: QR Code
 */

use WPDK\Utils;

?>
<div class="tinypress-meta-analytics">

</div>

<div>
    <canvas id="myChart"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('myChart');
    const data = {
        labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
            borderWidth: 1
        }]
    };

    const chart = new Chart(ctx, {
        type: 'line',
        data: data,
        options: {
            scales: {
                x: {
                    type: 'time',
                    time: {
                        unit: 'month'
                    }
                }
            }
        }
    });

    // new Chart(ctx, {
    //     type: 'bar',
    //     data: ,
    //     options: {
    //         scales: {
    //             y: {
    //                 beginAtZero: true
    //             }
    //         }
    //     }
    // });
</script>

<script>
    (function ($, window, document) {
        "use strict";

        $(document).on('ready', function () {

        });

    })(jQuery, window, document);
</script>
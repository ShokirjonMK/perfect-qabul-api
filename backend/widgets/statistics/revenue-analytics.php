<?php

use \backend\models\Dashboard;

$results = Dashboard::getSellingsMonthly();

$current_currency = get_current_currency();
$chart_orders = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
$chart_price = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

$chart_month = [_e("Jan"), _e("Feb"), _e("Mar"), _e("Apr"), _e("May"), _e("Jun"), _e("Jul"), _e("Aug"), _e("Sep"), _e("Oct"), _e("Nov"), _e("Dec")];

if ($results) {
    $i = -1;

    foreach ($results as $date => $items) {
        $i++;
        $chart_orders[$i] = count($items);

        if ($items) {
            $total_price = 0;

            foreach ($items as $item) {
                $total_price_str = $item['total_price'];

                if (!is_null($total_price_str) && !empty($total_price_str)) {
                    $total_price_arr = unserialize($total_price_str);
                    $currency = array_value($total_price_arr, 'currency');
                    $price = array_value($total_price_arr, 'price');
                    $price = string_to_price($price);

                    if ($price > 0) {
                        if ($currency != $current_currency) {
                            $_price = convert_price_to('USD', $price, $currency, '{{price}}');
                            $price = string_to_price($_price);
                        }

                        $total_price = ($price + $total_price);
                    }
                }
            }

            $format_price = format_price($total_price, $current_currency, '{{price}}');
            $chart_price[$i] = string_to_price($format_price);
        }
    }
} ?>

<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4"><?= _e('Revenue analytics'); ?></h4>
        <div>
            <div id="revenue-analytics-chart" class="apex-charts" dir="ltr"></div>
        </div>
    </div>
</div>

<script>
    var RACH_options = {
        series: [{
                name: "<?= _e('Sale price'); ?>",
                data: <?= json_encode($chart_price); ?>
            },
            {
                name: "<?= _e('Orders'); ?>",
                type: "column",
                data: <?= json_encode($chart_orders); ?>
            }
        ],
        chart: {
            height: 381,
            type: 'line',
            toolbar: {
                show: false
            }
        },
        colors: ['#1cbb8c', '#5664d2'],
        dataLabels: {
            enabled: false
        },
        legend: {
            enabled: false
        },
        stroke: {
            width: [5, 0],
            curve: 'smooth'
        },
        markers: {
            size: 1
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: "35%"
            }
        },
        yaxis: [{
            labels: {
                formatter: function(value) {
                    return value.formatMoney(2, ',', '.') + " " + "<?= get_current_currency(); ?>";
                }
            },
        }, {
            labels: {
                formatter: function(value) {
                    return parseInt(value);
                }
            },
        }],
        xaxis: {
            categories: <?= json_encode($chart_month); ?>,
        }
    };
</script>

<?php
$this->registerJs(
    <<<JS
    var RAChart = new ApexCharts(document.querySelector("#revenue-analytics-chart"), RACH_options);
    RAChart.render();
JS
); ?>
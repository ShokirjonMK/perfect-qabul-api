<?php

use \backend\models\Dashboard;

$chart_days = array();
$chart_data = array();

$date_1 = date('Y-m-d', strtotime('-10 days'));
$date_2 = date('Y-m-d', strtotime('tomorrow'));

$devices = Dashboard::getDevicesCount();
$visitors = Dashboard::getVisitorsCount(['date_from' => $date_1, 'date_to' => $date_2]);
$sessions = Dashboard::getSessionCount(['date_from' => $date_1, 'date_to' => $date_2]);

$period = new DatePeriod(
    new DateTime($date_1),
    new DateInterval('P1D'),
    new DateTime($date_2)
);

foreach ($period as $key => $value) {
    $date_key = $value->format('Y-m-d');
    $chart_days['visitors'][] = $value->format('d/m');
    $chart_days['sessions'][] = $value->format('d/m');

    if (isset($visitors[$date_key])) {
        $chart_data['visitors'][] = $visitors[$date_key];
    } else {
        $chart_data['visitors'][] = 0;
    }

    if (isset($sessions[$date_key])) {
        $chart_data['sessions'][] = $sessions[$date_key];
    } else {
        $chart_data['sessions'][] = 0;
    }
} ?>

<div class="card">
    <div class="card-body">
        <div class="float-right d-none d-md-inline-block">
            <div class="btn-group mb-2">
                <button type="button" class="btn btn-sm btn-light active" apex-chart-btn="#visitors-analytics-chart">
                    <?= _e('Visitors'); ?>
                </button>
                <button type="button" class="btn btn-sm btn-light" apex-chart-btn="#sessions-analytics-chart">
                    <?= _e('Sessions'); ?>
                </button>
                <button type="button" class="btn btn-sm btn-light" apex-chart-btn="#devices-analytics-chart">
                    <?= _e('Devices'); ?>
                </button>
            </div>
        </div>

        <h4 class="card-title mb-4"><?= _e('Website analytics'); ?></h4>
        <div>
            <div id="visitors-analytics-chart" class="apex-charts" dir="ltr"></div>
            <div id="sessions-analytics-chart" class="apex-charts d-none" dir="ltr"></div>
            <div id="devices-analytics-chart" class="apex-charts d-none" dir="ltr"></div>
        </div>
    </div>
</div>

<script>
    var VUACH_options = {
        series: [{
            name: "<?= _e('Visitors'); ?>",
            data: <?= json_encode($chart_data['visitors']); ?>
        }],
        chart: {
            type: 'area',
            height: 351,
            toolbar: {
                show: false
            }
        },
        colors: ['#1cbb8c'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        xaxis: {
            categories: <?= json_encode($chart_days['visitors']); ?>,
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return parseInt(value);
                }
            }
        },
        legend: {
            horizontalAlign: 'left'
        }
    };

    var VSACH_options = {
        series: [{
            name: "<?= _e('Sessions'); ?>",
            data: <?= json_encode($chart_data['sessions']); ?>
        }],
        chart: {
            type: 'area',
            height: 351,
            toolbar: {
                show: false
            }
        },
        colors: ['#5664d2'],
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        xaxis: {
            categories: <?= json_encode($chart_days['sessions']); ?>,
        },
        yaxis: {
            labels: {
                formatter: function(value) {
                    return parseInt(value);
                }
            }
        },
        legend: {
            horizontalAlign: 'left'
        }
    };

    var VDACH_options = {
        series: [{
            name: "<?= _e('Desktop'); ?>",
            data: <?= json_encode(array($devices['desktop'])); ?>
        },
        {
            name: "<?= _e('Tablet'); ?>",
            data: <?= json_encode(array($devices['tablet'])); ?>
        },
        {
            name: "<?= _e('Mobile'); ?>",
            data: <?= json_encode(array($devices['mobile'])); ?>
        }],
        chart: {
            type: 'bar',
            height: 351,
            toolbar: {
                show: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        xaxis: {
            categories: ["<?= _e('Device type'); ?>"],
        },
        legend: {
            horizontalAlign: 'left'
        }
    };
</script>

<?php
$this->registerJs(
    <<<JS
    var VUAChart = new ApexCharts(document.querySelector("#visitors-analytics-chart"), VUACH_options);
    VUAChart.render();

    var VSAChart = new ApexCharts(document.querySelector("#sessions-analytics-chart"), VSACH_options);
    VSAChart.render();

    var VDAChart = new ApexCharts(document.querySelector("#devices-analytics-chart"), VDACH_options);
    VDAChart.render();

    $(document).on('click', '[apex-chart-btn="#visitors-analytics-chart"]', function () {
        VUAChart.resetSeries();
    });

    $(document).on('click', '[apex-chart-btn="#sessions-analytics-chart"]', function () {
        VSAChart.resetSeries();
    });

    $(document).on('click', '[apex-chart-btn="#devices-analytics-chart"]', function () {
        VDAChart.resetSeries();
    });
JS
); ?>
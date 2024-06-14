<?php

use \backend\models\Dashboard;

$results = Dashboard::getMostSuppliers();

if ($results) {
    $total_selling = 0;
    $chart_series = array();
    $chart_labels = array();
    $chart_percentage = array();
    $selling_counts = array();
    $chart_colors = ["#5664d2", "#1cbb8c", "#eeb902", "#8a7d98"];

    foreach ($results as $item) {
        $total_sales = (int) $item['total_sales'];

        if ($total_sales > 0) {
            $chart_series[] = $total_sales;
            $selling_counts[] = $total_sales;
            $total_selling = ($total_selling + $total_sales);
        } else {
            $chart_series[] = 0;
            $selling_counts[] = 0;
        }

        $chart_labels[] = $item['title'];
    }

    foreach ($selling_counts as &$number) {
        $number = ($number / $total_selling) * 100;
        $chart_percentage[] = round($number, 0, PHP_ROUND_HALF_UP);
    }
} else {
    $chart_series = ['100'];
    $chart_percentage = ['100'];
    $chart_labels = ["No data"];
    $chart_colors = ["#b9b9b9"];
} ?>

<div class="card">
    <div class="card-body" style="min-height: 462px;">
        <h4 class="card-title mb-4"><?= _e('Top suppliers ({year})', ['year' => date('Y')]); ?></h4>

        <div id="most-selled-suppliers-chart" class="apex-charts"></div>

        <?php if (count($chart_labels) > 1) : ?>
            <div class="row mt-2">
                <?php foreach ($chart_labels as $i => $chart_label) : ?>
                    <div class="col-6">
                        <div class="mt-2">
                            <p class="mb-1 text-truncate" title="<?= $chart_label; ?>">
                                <i class="mdi mdi-circle font-size-10 mr-1" style="color:<?= $chart_colors[$i]; ?>"></i>
                                <?= $chart_label; ?>
                            </p>
                            <p><?= $chart_percentage[$i]; ?>% - <?= _e('{count} sales', ['count' => $chart_series[$i]]); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="mt-2 text-center">
                <p><?= _e('Data not found'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    var MSSCH_options = {
        series: <?= json_encode($chart_series); ?>,
        chart: {
            height: 230,
            type: "donut"
        },
        labels: <?= json_encode($chart_labels); ?>,
        plotOptions: {
            pie: {
                donut: {
                    size: "75%"
                }
            }
        },
        dataLabels: {
            enabled: !1
        },
        legend: {
            show: !1
        },
        colors: <?= json_encode($chart_colors); ?>
    };
</script>

<?php
$this->registerJs(
    <<<JS
    var MSSCHart = new ApexCharts(document.querySelector("#most-selled-suppliers-chart"), MSSCH_options);
    MSSCHart.render();
JS
); ?>
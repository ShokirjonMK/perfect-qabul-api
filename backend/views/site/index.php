<?php

use \backend\widgets\Statistics;

$for_refresh = true;
$not_refresh = true;

if (input_get('for-refresh') == '1') {
    $not_refresh = false;
}

$this->title = _e('Dashboard'); ?>

<div id="dashboard-widgets-block">
    <?= Statistics::widget(['type' => 'basic-counts', 'load' => $for_refresh]); ?>

    <div class="row">
        <div class="col-xl-8">
            <?= Statistics::widget(['type' => 'website-analytics', 'load' => $not_refresh]); ?>
        </div>

        <div class="col-xl-4">
            <?= Statistics::widget(['type' => 'user-online', 'load' => $for_refresh]); ?>
        </div>

        <div class="col-xl-12">
            <?= Statistics::widget(['type' => 'map-analytics', 'load' => $not_refresh]); ?>
        </div>
    </div>
</div>

<script>
    setInterval(() => {
        refreshDashboardWidgets();
    }, 5000);
</script>
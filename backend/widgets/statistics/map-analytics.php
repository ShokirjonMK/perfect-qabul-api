<?php

use \backend\models\Dashboard;

$data = array();
$results = Dashboard::getCountryCount();

if ($results) {
    foreach ($results as $key => $item) {
        $data[$key] = ' (' . _e('Visits') . ': ' . $item . ')';
    }
} ?>

<div class="card">
    <div class="card-body">
        <h4 class="card-title mb-4"><?= _e('Geo location'); ?></h4>
        <div>
            <div id="geo-location-map" style="height: 400px"></div>
        </div>
    </div>
</div>

<script>
    var gdpCount = <?= json_encode($results); ?>;
    var gdpData = <?= json_encode($data); ?>;
</script>

<?php
$this->registerJs(
    <<<JS
    $('#geo-location-map').vectorMap({
        map: 'world_mill_en',
        backgroundColor: '#FFFFFF',
        regionStyle: {
            initial: {
                fill: '#CCC',
                "fill-opacity": 1,
                stroke: 'none',
                "stroke-width": 0,
                "stroke-opacity": 1
            },
            hover: {
                "fill-opacity": 0.6,
                cursor: 'pointer'
            },
            selected: {
                fill: '#000'
            },
            selectedHover: {
            }
        },
        series: {
            regions: [{
                values: gdpCount,
                scale: ['#5764d0'],
                normalizeFunction: 'polynomial'
            }]
        },
        onRegionTipShow: function(e, el, code) {
            if (gdpData[code] != undefined && gdpData[code] != '') {
                el.html(el.html() + gdpData[code]);
            }
        }
    });
JS
); ?>
<?php
/*
 * Available variables:
 * 
 * Queried object: $obj
 * Segment info: $info
 * Segment model: $segment
 * Old attributes: $oldAttributes
 */

$this->getPartial('header');

$this->getPartial('page-header', [
    'title' => $info->title,
]); ?>

<div class="page-section">
    <div class="container">
        <h1><?= $info->title; ?></h1>
        <h4>Type: <?= $segment->type; ?></h4>
    </div>
</div>

<?php $this->getPartial('footer'); ?>
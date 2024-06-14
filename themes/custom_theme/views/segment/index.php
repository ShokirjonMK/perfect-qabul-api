<?php 
/*
 * Available variables:
 * 
 * Queried object: $obj
 * Segment info: $info
 * Segment model: $segment
 * Old attributes: $oldAttributes
 */

$this->getPartial('header'); ?>

<div class="container">
    <h3><?= $info->title; ?></h3>
    <h4>Type: <?= $segment->type; ?></h4>
</div>

<?php $this->getPartial('footer'); ?>
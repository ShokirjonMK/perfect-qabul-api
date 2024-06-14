<?php 
/*
 * Available variables:
 * 
 * Queried object: $obj
 * Content info: $info
 * Content model: $content
 * Old attributes: $oldAttributes
 */

$this->getPartial('header'); ?>

<div class="container">
    <h3><?= $info->title; ?></h3>
    <h4>Type: <?= $content->type; ?></h4>
</div>

<?php $this->getPartial('footer'); ?>
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
    <h1><?= isset($info->title) ? $info->title : _e('Home'); ?></h1>
    <h4>Default theme</h4>
</div>

<?php $this->getPartial('footer'); ?>
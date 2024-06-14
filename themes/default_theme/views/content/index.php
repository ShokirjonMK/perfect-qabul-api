<?php
/*
 * Available variables:
 * 
 * Queried object: $obj
 * Content info: $info
 * Content model: $content
 * Old attributes: $oldAttributes
 */

$this->getPartial('header');

$this->getPartial('page-header', [
    'title' => $info->title,
]); ?>

<div class="page-section">
    <div class="container">
        <h1><?= $info->title; ?></h1>
        <h4 class="mb-4">Type: <?= $content->type; ?></h4>
    </div>
</div>

<?= content_editor_sections($info->content_blocks); ?>

<?php $this->getPartial('footer'); ?>
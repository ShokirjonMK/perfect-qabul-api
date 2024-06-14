<?php
/*
 * Available variables:
 * 
 * Queried object: $obj
 * Content info: $info
 * Content model: $content
 * Old attributes: $oldAttributes
 */

use common\models\Content;

$lang = get_current_lang();
$page_header_args = array('title' => $info->title,);

$parent = Content::find()
    ->alias('content')
    ->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id')
    ->where(['content.resource_type' => 'post', 'content.status' => 1, 'content.deleted' => 0])
    ->andWhere(['info.language' => $lang])
    ->with('info')
    ->one();

if ($parent) {
    $page_header_args['links'] = array(
        get_content_url($parent->info) => $parent->info->title,
    );
}

$this->getPartial('header');
$this->getPartial('page-header', $page_header_args); ?>

<div class="page-section">
    <div class="container">
        <h1><?= $info->title; ?></h1>
        <h4 class="mb-4">Type: <?= $content->type; ?></h4>
    </div>
</div>

<?= content_editor_sections($info->content_blocks); ?>

<?php $this->getPartial('footer'); ?>
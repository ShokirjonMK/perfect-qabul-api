<?php
$controller = Yii::$app->controller;
$lexicon = array_value($controller->settings, 'lexicon');

$this->title = str_replace('{title}', $info->title, array_value($lexicon, 'edit_item_title2'));
$this->page_title = str_replace('{title}', '<span>' . $info->title . '</span>', array_value($lexicon, 'edit_item_title2'));

$this->breadcrumb_title = array_value($lexicon, 'edit_item_title');
$this->breadcrumbs[] = ['label' => array_value($lexicon, 'title'), 'url' => $all_url]; ?>

<div class="Segment-update">
    <?= $this->render('_form', [
        'all_url' => $all_url,
        'main_url' => $main_url,
        'model' => $model,
        'info' => $info,
        'translations' => $translations,
    ]); ?>
</div>
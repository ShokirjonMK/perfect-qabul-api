<?php
$controller = Yii::$app->controller;
$lexicon = array_value($controller->settings, 'lexicon');

$this->title = array_value($lexicon, 'new_item_title');
$this->page_title = array_value($lexicon, 'new_item_title') . ': ';
$this->breadcrumbs[] = ['label' => array_value($lexicon, 'title'), 'url' => $all_url]; ?>

<div class="Page-create">
    <?= $this->render('_form', [
        'all_url' => $all_url,
        'main_url' => $main_url,
        'model' => $model,
        'info' => $info,
    ]); ?>
</div>
<?php
$controller = Yii::$app->controller;
$lexicon = array_value($controller->settings, 'lexicon');

$this->title = _e('Edit ' . $settings['title']['singular']);
$this->page_title =  _e('Edit ' . $settings['title']['singular']);

$this->breadcrumb_title = _e('Edit ' . $settings['title']['singular']);
$this->breadcrumbs[] = ['label' => $settings['title']['plural'], 'url' => $main_url . '/all']; ?>

<div class="Page-update">
<?= $this->render('_form', [
        'main_url' => $main_url,
        'model' => $model,
        'info' => $info
    ]); ?>
</div>
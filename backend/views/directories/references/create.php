<?php
$controller = Yii::$app->controller;

$this->title = _e('Create ' . $settings['title']['singular']);
$this->page_title = $this->title;
$this->breadcrumbs[] = ['label' => $settings['title']['plural'], 'url' => $main_url . '/all']; ?>

<div class="Page-create">
    <?= $this->render('_form', [
        'main_url' => $main_url,
        'model' => $model,
        'info' => $info
    ]); ?>
</div>
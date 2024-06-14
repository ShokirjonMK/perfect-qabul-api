<?php
$controller = Yii::$app->controller;

$this->title = _e('Create speciality');
$this->page_title = $this->title;
$this->breadcrumbs[] = ['label' => _e('Specialities'), 'url' => $main_url]; ?>

<div class="Page-create">
    <?= $this->render('_form', [
        'main_url' => $main_url,
        'model' => $model,
        'info' => $info
    ]); ?>
</div>
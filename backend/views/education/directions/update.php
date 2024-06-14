<?php
$controller = Yii::$app->controller;
$lexicon = array_value($controller->settings, 'lexicon');

$this->title = _e('Edit direction');
$this->page_title =  _e('Edit direction');

$this->breadcrumb_title = _e('Edit direction');
$this->breadcrumbs[] = ['label' => _e('Directions of education'), 'url' => $main_url]; ?>

<div class="Page-update">
<?= $this->render('_form', [
        'main_url' => $main_url,
        'model' => $model,
        'info' => $info,
        'listDepartments' => $listDepartments,
    ]); ?>
</div>
<?php
$controller = Yii::$app->controller;
$lexicon = array_value($controller->settings, 'lexicon');

$this->title = _e('Edit department');
$this->page_title =  _e('Edit department');

$this->breadcrumb_title = _e('Edit department');
$this->breadcrumbs[] = ['label' => _e('Departments'), 'url' => $main_url]; ?>

<div class="Page-update">
<?= $this->render('_form', [
        'main_url' => $main_url,
        'model' => $model,
        'info' => $info,
        'listTypes' => $listTypes,
        'listOtherDepartments' => $listOtherDepartments,
    ]); ?>
</div>
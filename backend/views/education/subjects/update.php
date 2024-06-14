<?php
$controller = Yii::$app->controller;
$lexicon = array_value($controller->settings, 'lexicon');

$this->title = _e('Edit subject');
$this->page_title =  _e('Edit subject');

$this->breadcrumb_title = _e('Edit subject');
$this->breadcrumbs[] = ['label' => _e('Subjects'), 'url' => $main_url]; ?>

<div class="Page-update">
<?= $this->render('_form', [
        'main_url' => $main_url,
        'model' => $model,
        'info' => $info
    ]); ?>
</div>
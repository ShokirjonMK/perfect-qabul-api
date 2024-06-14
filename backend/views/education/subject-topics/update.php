<?php
$controller = Yii::$app->controller;
$lexicon = array_value($controller->settings, 'lexicon');

$this->title = _e('Edit topic of subject');
$this->page_title =  _e('Edit topic of subject');

$this->breadcrumb_title = _e('Edit topic of subject');
$this->breadcrumbs[] = ['label' => _e('Topics of subjects'), 'url' => $main_url]; ?>

<div class="Page-update">
<?= $this->render('_form', [
        'main_url' => $main_url,
        'model' => $model,
        'info' => $info,
        'listSubjects' => $listSubjects,
        'listTypes' => $listTypes
    ]); ?>
</div>
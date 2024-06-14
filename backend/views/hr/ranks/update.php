<?php
$controller = Yii::$app->controller;
$lexicon = array_value($controller->settings, 'lexicon');

$this->title = _e('Edit rank');
$this->page_title =  _e('Edit rank');

$this->breadcrumb_title = _e('Edit rank');
$this->breadcrumbs[] = ['label' => _e('Ranks'), 'url' => $main_url]; ?>

<div class="Page-update">
<?= $this->render('_form', [
        'main_url' => $main_url,
        'model' => $model,
        'info' => $info
    ]); ?>
</div>
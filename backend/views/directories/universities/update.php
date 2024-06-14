<?php
$controller = Yii::$app->controller;
$lexicon = array_value($controller->settings, 'lexicon');

$this->title = _e('Edit university');
$this->page_title =  _e('Edit university');

$this->breadcrumb_title = _e('Edit university');
$this->breadcrumbs[] = ['label' => _e('Universities'), 'url' => $main_url]; ?>

<div class="Page-update">
<?= $this->render('_form', [
        'main_url' => $main_url,
        'model' => $model,
        'info' => $info,
        'countries' => $countries,
        'regions' => $regions,
    ]); ?>
</div>
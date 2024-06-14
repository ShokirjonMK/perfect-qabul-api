<?php
$this->title = _e('Edit region: {name}', [
    'name' => $model->name,
]);

$this->breadcrumb_title = _e('Edit region');
$this->breadcrumbs[] = ['label' => _e('Regions'), 'url' => $main_url . '/cities']; ?>

<div class="City-update">
    <?= $this->render('_form_city', [
        'model' => $model,
    ]) ?>
</div>
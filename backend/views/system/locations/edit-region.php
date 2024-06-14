<?php
$this->title = _e('Edit city: {name}', [
    'name' => $model->name,
]);

$this->breadcrumb_title = _e('Edit city');
$this->breadcrumbs[] = ['label' => _e('Cities'), 'url' => $main_url . '/regions']; ?>

<div class="Region-create">
    <?= $this->render('_form_region', [
        'model' => $model,
    ]) ?>
</div>
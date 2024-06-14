<?php
$this->title = _e('New region');
$this->breadcrumbs[] = ['label' => _e('Regions'), 'url' => $main_url . '/cities']; ?>

<div class="City-create">
    <?= $this->render('_form_city', [
        'model' => $model,
    ]) ?>
</div>
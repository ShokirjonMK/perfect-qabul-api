<?php
$this->title = _e('New city');
$this->breadcrumbs[] = ['label' => _e('Cities'), 'url' => $main_url . '/regions']; ?>

<div class="Region-create">
    <?= $this->render('_form_region', [
        'model' => $model,
    ]) ?>
</div>

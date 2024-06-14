<?php
$this->title = _e('New employee');
$this->breadcrumbs[] = ['label' => _e('Employees'), 'url' => $main_url]; ?>

<div class="User-create">
    <?= $this->render('_form', array_merge([
        'model' => $model,
        'profile' => $profile,
        'employee' => $employee,
    ],$dirs)); ?>
</div>
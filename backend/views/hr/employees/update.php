<?php
$this->title = _e('Edit employee: {email}', [
    'email' => $model->username,
]);

$this->breadcrumb_title = _e('Edit employee');
$this->breadcrumbs[] = ['label' => _e('Employees'), 'url' => $main_url]; ?>

<div class="User-update">
    <?= $this->render('_form', array_merge([
        'model' => $model,
        'profile' => $profile,
        'employee' => $employee,
    ],$dirs)); ?>
</div>
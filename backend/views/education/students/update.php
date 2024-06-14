<?php
$this->title = _e('Edit student: {email}', [
    'email' => $model->username,
]);

$this->breadcrumb_title = _e('Edit student');
$this->breadcrumbs[] = ['label' => _e('Students'), 'url' => $main_url]; ?>

<div class="User-update">
    <?= $this->render('_form', array_merge([
        'model' => $model,
        'profile' => $profile,
        'student' => $student,
    ],$dirs)); ?>
</div>
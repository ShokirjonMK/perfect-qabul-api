<?php
$this->title = _e('New student');
$this->breadcrumbs[] = ['label' => _e('Students'), 'url' => $main_url]; ?>

<div class="User-create">
    <?= $this->render('_form', array_merge([
        'model' => $model,
        'profile' => $profile,
        'student' => $student,
    ],$dirs)); ?>
</div>
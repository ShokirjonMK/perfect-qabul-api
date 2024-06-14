<?php
$this->title = _e('Edit user: {email}', [
    'email' => $model->username,
]);

$this->breadcrumb_title = _e('Edit user');
$this->breadcrumbs[] = ['label' => _e('User'), 'url' => $main_url]; ?>

<div class="User-update">
    <?= $this->render('_form',[
        'model' => $model,
        'profile' => $profile,
    ]); ?>
</div>
<?php
$this->title = _e('New user');
$this->breadcrumbs[] = ['label' => _e('User'), 'url' => $main_url]; ?>

<div class="User-create">
    <?= $this->render('_form', [
        'model' => $model,
        'profile' => $profile,
    ]); ?>
</div>
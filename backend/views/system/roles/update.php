<?php
$this->title = Yii::t('app', 'Edit Role: {title}', [
    'title' => 'Administrator',
]);

$this->page_title = Yii::t('app', 'Edit Role: <span>{title}</span>', [
    'title' => 'Administrator',
]);

$this->breadcrumb_title = 'Edit Role';
$this->breadcrumbs[] = ['label' => 'Roles', 'url' => $main_url]; ?>

<div class="Role-update">
    <?= $this->render('_form'); ?>
</div>
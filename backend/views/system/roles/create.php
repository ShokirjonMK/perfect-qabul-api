<?php
$this->title = Yii::t('app', 'New Role');
$this->page_title = Yii::t('app', 'New Role: ');
$this->breadcrumbs[] = ['label' => 'Roles', 'url' => $main_url]; ?>

<div class="Role-create">
    <?= $this->render('_form'); ?>
</div>
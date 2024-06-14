<?php
$controller = Yii::$app->controller;
$lexicon = array_value($controller->settings, 'lexicon');

$this->title = _e('Not found');
$this->breadcrumbs[] = ['label' => array_value($lexicon, 'edit_item_title'), 'url' => $main_url]; ?>

<div class="card">
    <div class="card-body page-item-not-found">
        <i class="ri-error-warning-line"></i>
        <h3><?= array_value($lexicon, 'not_found_message'); ?></h3>
        <p><?= array_value($lexicon, 'not_found_message_full'); ?></p>

        <a href="<?= $all_url; ?>" class="btn btn-secondary waves-effect btn-with-icon">
            <i class="ri-arrow-left-line mr-1"></i>
            <?= array_value($lexicon, 'back_to_message'); ?>
        </a>
    </div>
</div>
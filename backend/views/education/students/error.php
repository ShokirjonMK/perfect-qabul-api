<?php
$this->title = $error_info['title'];
$this->breadcrumbs[] = ['label' => _e('Students'), 'url' => $main_url]; ?>

<div class="card">
    <div class="card-body page-item-not-found">
        <i class="ri-error-warning-line"></i>
        <h3><?= $error_info['text']; ?></h3>
        <p><?= $error_info['desc']; ?></p>

        <a href="<?= $main_url; ?>" class="btn btn-secondary waves-effect btn-with-icon">
            <i class="ri-arrow-left-line mr-1"></i>
            <?= _e('Back to students'); ?>
        </a>
    </div>
</div>
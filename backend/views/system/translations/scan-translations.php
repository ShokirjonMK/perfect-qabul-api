<?php
$this->title = _e('Scan translations');
$this->breadcrumbs[] = ['label' => _e('Translations'), 'url' => $main_url]; 

require('js-vars.php'); ?>

<div class="card">
    <div class="card-body page-item-not-found">
        <i class="ri-error-warning-line"></i>
        <h3><?= _e('The translations have not been scanned yet!'); ?></h3>
        <p><?= _e('Click the button to start scanning the translations.'); ?></p>

        <a href="javascript:void(0);" data-scan-translations="<?= $path_key; ?>" data-redirect-to="<?= $main_url; ?>/list-languages?id=<?= $path_key; ?>" class="btn btn-primary waves-effect btn-with-icon">
            <i class="ri-refresh-line mr-1"></i>
            <?= _e('Scan translations'); ?>
        </a>
    </div>
</div>
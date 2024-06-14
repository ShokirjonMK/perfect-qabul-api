
<?php

use common\models\Profile;
use yii\helpers\Url;

$user = current_user();
$profile = current_user_profile(); ?>

<header id="page-topbar">
    <div class="navbar-header">
        <div class="d-flex">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <a href="<?= admin_url(); ?>" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="<?= images_url('default-logo-icon.png') ?>" alt="logo" height="35">
                    </span>
                    <span class="logo-lg">
                        <img src="<?= images_url('default-logo-icon.png') ?>" alt="logo" height="35">
                        <b><?= _e('Control Panel'); ?></b>
                    </span>
                </a>

                <a href="<?= admin_url(); ?>" class="logo logo-light text-center">
                    <span class="logo-sm">
                        <img src="<?= images_url('default-logo-icon.png') ?>" alt="logo" height="35">
                    </span>
                    <span class="logo-lg">
                        <img src="<?= images_url('default-logo-icon.png') ?>" alt="logo" height="35">
                        <b><?= _e('Control Panel'); ?></b>
                    </span>
                </a>
            </div>

            <button type="button" class="btn btn-sm px-3 font-size-24 header-item waves-effect" id="vertical-menu-btn">
                <i class="ri-menu-2-line align-middle"></i>
            </button>
        </div>

        <div class="d-flex">
            <?php
            $languages = admin_area_langs();
            $current_lang = admin_current_lang(); ?>
            <div class="dropdown d-inline-block">
                <button type="button" class="btn header-item waves-effect" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img src="<?= $current_lang['flag']; ?>" alt="<?= $current_lang['name']; ?>" height="15" style="margin-bottom: 3px;">
                </button>
                <div class="dropdown-menu dropdown-menu-end">
                    <?php foreach ($languages as $language) : ?>
                        <a href="<?= set_query_var('language', $language['lang_code']); ?>" class="dropdown-item notify-item">
                            <img src="<?= $language['flag']; ?>" alt="user-image" class="mr-1" width="18">
                            <span class="align-middle"><?= $language['name']; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="d-inline-block">
                <a href="<?= site_url(); ?>" class="btn header-item header-item-icon waves-effect" target="_blank">
                    <i class="ri-global-line" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Go to site'); ?>"></i>
                </a>
            </div>

            <div class="dropdown d-inline-block user-dropdown">
                <button type="button" class="btn header-item waves-effect" id="page-header-user-dropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <img class="rounded-circle header-profile-user" src="<?= Profile::getAvatar($profile); ?>" alt="<?= Profile::getFullname($profile); ?>">
                    <span class="d-none d-xl-inline-block ml-1"><?= Profile::getFullname($profile); ?></span>
                    <i class="mdi mdi-chevron-down d-none d-xl-inline-block"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                    <!-- item-->
                    <a class="dropdown-item" href="<?= Url::to(['/profile']); ?>">
                        <i class="ri-user-line align-middle mr-1"></i> <?= _e('Profile'); ?>
                    </a>
                    <a class="dropdown-item d-block" href="<?= Url::to(['/profile/settings']); ?>">
                        <i class="ri-settings-2-line align-middle mr-1"></i> <?= _e('Edit profile'); ?>
                    </a>
                    <a class="dropdown-item d-block" href="<?= Url::to(['/profile/password']); ?>">
                        <i class="ri-key-2-line align-middle mr-1"></i> <?= _e('Password'); ?>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="<?= Url::to(['/auth/logout']); ?>">
                        <i class="ri-shut-down-line align-middle mr-1 text-danger"></i> <?= _e('Log out'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>
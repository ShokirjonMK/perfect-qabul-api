<?php

use \backend\models\Dashboard;

$site_url = site_url();
$last_visted_pages = Dashboard::getLastVistedPages(); ?>

<style>
    .analytics-last-seen-pages li {
        display: block;
        padding: 3px 0;
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }
</style>

<div class="card">
    <div class="card-body" style="min-height: 433px;">
        <h4 class="card-title mb-">
            <?= _e('User online: '); ?>
        </h4>

        <h1 class="mt-0 mb-4" data-dw-load="users-online-counter">
            <?= Dashboard::getOnlineUsersCount(); ?>
        </h1>

        <h4 class="card-title mb-2">
            <?= _e('Last seen pages: '); ?>
        </h4>

        <?php if ($last_visted_pages) : ?>
            <ul class="analytics-last-seen-pages list-unstyled mb-0" data-dw-load="last-seen-pages-list">
                <?php foreach ($last_visted_pages as $page) : ?>
                    <li>
                        <a href="<?= array_value($page, 'value', '#'); ?>" title="<?= array_value($page, 'value'); ?>" target="_blank">
                            <?php
                            $page_link = array_value($page, 'value');
                            $page_url = str_replace($site_url, '/', $page_link);

                            echo ($page_url == '/') ? _e('Home page') : $page_url; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p><?= _e('Pages not found.'); ?></p>
        <?php endif; ?>
    </div>
</div>
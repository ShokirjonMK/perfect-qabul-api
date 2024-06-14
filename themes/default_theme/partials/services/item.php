<?php
$url = get_content_url($item->info);
$image_url = $this->getAssetsUrl('theme/images/project_img.png');

if (!empty($item->info->image)) {
    $image_url = $item->info->image;
} ?>

<div class="project">
    <div class="project_img">
        <a href="<?= $url; ?>">
            <img src="<?= $image_url; ?>" alt="<?= $item->info->title; ?>" />
        </a>
    </div>

    <a href="<?= $url; ?>" class="project_text">
        <h3><?= $item->info->title; ?></h3>
        <p>There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised</p>
    </a>
</div>
<?php

use yii\helpers\Url;

$array = $this->breadcrumbs;
$title = isset($this->breadcrumb_title) && $this->breadcrumb_title ? $this->breadcrumb_title : $this->title;
$page_title = isset($this->page_title) && $this->page_title ? $this->page_title : $this->title; ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-sm-6 content-header-col-1">
            <h4 class="m-0 text-dark" ep-bind="title">
                <?= $page_title; ?>
            </h4>
        </div>

        <div class="col-sm-6 content-header-col-2">
            <ol class="breadcrumb m-0">
                <li class="breadcrumb-item">
                    <a href="<?= admin_url(); ?>"><?= _e('Home'); ?></a>
                </li>
                <?php if (is_array($array) && $array) : ?>
                    <?php foreach ($array as $key => $value) : ?>
                        <li class="breadcrumb-item">
                            <a href="<?= Url::to([$value['url']]); ?>"><?= $value['label']; ?></a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
                <li class="breadcrumb-item active"><?= $title; ?></li>
            </ol>
        </div>
    </div>
</div>
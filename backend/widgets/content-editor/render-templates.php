<?php
$templates = __DIR__ . '/templates';

if (is_dir($templates)) {
    foreach (glob($templates . '/*.php') as $file) {
        ob_start();
        include($file);
        $content = ob_get_contents();
        ob_end_clean();
        echo $content;
    }
} ?>

<div ceditor-render-element-template="image">
    <?php
    echo \backend\widgets\StorageWidget::widget([
        'label' => '',
        'format' => 'image',
        'select_type' => 'single',
        'action' => 'image_select',
        'input' => array(
            'class' => 'ceditor-input',
            'attrs' => 'content-editor-element-item-data="image"',
        ),
    ]); ?>
</div>

<div ceditor-render-element-template="gallery">
    <?php
    echo \backend\widgets\StorageWidget::widget([
        'label' => '',
        'format' => 'gallery',
        'action' => 'gallery_select',
        'input' => array(
            'class' => 'ceditor-input',
            'name' => 'gallery_images',
            'attrs' => 'content-editor-element-item-data="gallery"',
        ),
    ]); ?>
</div>

<div ceditor-render-element-template="file">
    <?php
    echo \backend\widgets\StorageWidget::widget([
        'label' => '',
        'format' => 'file',
        'select_type' => 'single',
        'action' => 'file_select',
        'input' => array(
            'class' => 'ceditor-input',
            'attrs' => 'content-editor-element-item-data="file"',
        ),
    ]); ?>
</div>

<div ceditor-render-element-template="color">
    <div class="input-group ceditor-colorpicker-block">
        <input type="text" class="ceditor-input ceditor-colorpicker-input form-control input-lg" value="#000000" content-editor-element-item-data="color" />
        <span class="input-group-append">
            <span class="input-group-text colorpicker-input-addon"><i></i></span>
        </span>
    </div>
</div>

<div ceditor-render-element-template="button">
    <input type="hidden" content-editor-element-item-data="button">

    <div class="input-group mb-2">
        <div class="input-group-prepend">
            <label class="input-group-text"><?= _e('Text'); ?></label>
        </div>
        <input type="text" class="form-control input-lg input-text" ceditor-element-input-button="text">
    </div>

    <div class="input-group">
        <div class="input-group-prepend">
            <label class="input-group-text"><?= _e('Link'); ?></label>
        </div>
        <input type="text" class="form-control input-lg input-link" ceditor-element-input-button="link">
    </div>
</div>

<div ceditor-render-element-template="checkbox">
    <div class="custom-control custom-checkbox">
        <input type="checkbox" class="ceditor-input custom-control-input" content-editor-element-item-data="checkbox">
        <label class="ceditor-label custom-control-label"></label>
    </div>
</div>

<div ceditor-render-element-template="radio">
    <div class="custom-control custom-radio">
        <input type="radio" class="ceditor-input custom-control-input" content-editor-element-item-data="radio">
        <label class="ceditor-label custom-control-label"></label>
    </div>
</div>
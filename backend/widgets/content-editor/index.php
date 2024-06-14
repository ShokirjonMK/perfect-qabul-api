<?php
$element_json = array();
$element_items = array();
$element_groups = array_value($elements, 'groups');

if ($element_groups) {
    foreach ($element_groups as $element_group_key => $element_group_title) {
        $element_items_array = array_value($elements, $element_group_key);

        if ($element_items_array) {
            $element_items[] = array(
                'group_title' => $element_group_title,
                'group_items' => $element_items_array,
            );

            $element_json = array_merge($element_json, $element_items_array);
        }
    }
} ?>

<div class="content-editor-block" ceditor-block="disabled" ceditor-block-id="ce<?= rand(100000, 999999); ?>">
    <div class="content-editor-preloader"></div>

    <div class="content-editor-data" ceditor-data="all-items" ceditor-block-save-input="<?= $save_input; ?>"></div>

    <div class="card">
        <div class="card-body">
            <button type="button" class="content-editor-add-section-btn" ceditor-side-menu-open="sections">
                <i class="ri-add-circle-line"></i>
                <span><?= _e('Add section'); ?></span>
            </button>
        </div>
    </div>

    <div class="content-editor-render-items d-none">
        <?= $this->render('render-items', ['elements' => $element_items, 'sections' => $sections]); ?>
        <?= $this->render('render-templates', ['elements' => $element_items, 'sections' => $sections]); ?>
    </div>

    <div class="d-none">
        <textarea content-editor-objects="sections"><?= json_encode($sections); ?></textarea>
        <textarea content-editor-objects="elements"><?= json_encode($element_json); ?></textarea>
    </div>

    <div class="content-editor-side-menu-block">
        <?= $this->render('side-menus', ['elements' => $element_items, 'sections' => $sections]); ?>
    </div>
</div>
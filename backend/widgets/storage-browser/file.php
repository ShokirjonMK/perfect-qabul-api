<?php
$input_data = array();
$input_name = array_value($input, 'name');
$input_attrs = array_value($input, 'attrs');
$input_class = array_value($input, 'class');
$input_value = array_value($input, 'value');

if (is_string($input_name) && $input_name) {
    $input_data[] = 'name="' . $input_name . '"';
}

if (is_string($input_value) && $input_value) {
    $input_data[] = 'value="' . $input_value . '"';
}

if (is_string($input_attrs) && $input_attrs) {
    $input_data[] = $input_attrs;
}

if (is_string($input_class) && $input_class) {
    $input_data[] = 'class="' . $input_class . '"';
} ?>

<div class="storage-browser-frame-group">
    <?php if ($label) : ?>
        <label class="control-label"><?= $label; ?></label>
    <?php endif; ?>

    <div class="input-group">
        <input type="text" class="form-control" storage-browser-input>

        <div class="input-group-append">
            <button type="button" class="btn-image-clear" style="display: none;">
                <i class="ri-close-fill"></i>
            </button>
            <button type="button" class="btn btn-secondary" storage-browser-show="<?= $action ? $action : 'file'; ?>" storage-browser-select-type="<?= $select_type; ?>">
                <?= _e('Select'); ?>
            </button>
        </div>
    </div>

    <div class="storage-browser-input-img"></div>
    <div class="storage-browser-input-files"></div>

    <div class="storage-browser-data d-none">
        <input type="hidden" storage-browser-value="file" <?= $input_data ? implode(' ', $input_data) : ''; ?>>
        <input type="hidden" storage-browser-message="count-files" value="<?= _e('{count} files'); ?>">
        <input type="hidden" storage-browser-message="count-files-selected" value="<?= _e('{count} files selected'); ?>">
    </div>
</div>
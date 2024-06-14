<?php
$input_data = array();
$items_array = array();

$input_name = array_value($input, 'name');
$input_attrs = array_value($input, 'attrs');
$input_class = array_value($input, 'class');

if (is_string($input_name) && $input_name) {
    $input_data[] = 'name="' . $input_name . '"';
}

if (is_string($input_attrs) && $input_attrs) {
    $input_data[] = $input_attrs;
}

if (is_string($input_class) && $input_class) {
    $input_data[] = 'class="' . $input_class . '"';
}

if (isset($items) && $items) {
    $items_array = $items;
} ?>

<?php if ($label) : ?>
    <label class="control-label"><?= $label; ?></label>
<?php endif; ?>

<div class="stg-gallery--block storage-browser-frame-group">
    <input type="hidden" stg-gallery--elements="all" <?= $input_data ? implode(' ', $input_data) : ''; ?>>
    <input type="hidden" stg-gallery--elements="data" value='<?= json_encode($items_array); ?>'>

    <div class="stg-gallery--upload">
        <i class="ri-image-add-line"></i>

        <button type="button" class="btn btn-primary waves-effect waves-light" storage-browser-show="gallery" storage-browser-select-type="multi">
            <?= _e('Add Image'); ?>
        </button>
    </div>

    <div class="stg-gallery--elements d-none">
        <div class="stg-gallery--item-element">
            <div class="stg-gallery--item">
                <input type="hidden" stg-gallery--element="input">

                <div class="stg-gallery--item-in">
                    <button class="stg-gallery--item-remove">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                    <a class="image-popup-vertical-fit stg-gallery--item-zoom" href="#" stg-gallery--element="zoom-link">
                        <i class="ri-zoom-in-line"></i>
                    </a>
                    <img class="stg-gallery--item-img" alt="Image" stg-gallery--element="src">
                </div>
            </div>
        </div>
    </div>

    <div class="stg-gallery--in"></div>
</div>
<div class="storage-browser-frame-group">
    {label}

    <div class="input-group">
        <input type="text" class="form-control" storage-browser-input>

        <div class="input-group-append">
            <button type="button" class="btn-image-clear" style="display: none;">
                <i class="ri-close-fill"></i>
            </button>
            <button type="button" class="btn btn-secondary" storage-browser-show="<?= $action; ?>" storage-browser-select-type="<?= $select_type; ?>">
                <?= _e('Select'); ?>
            </button>
        </div>
    </div>

    <div class="storage-browser-input-img"></div>
    <div class="storage-browser-input-files"></div>

    <div class="storage-browser-data d-none">
        {input}
        <input type="hidden" storage-browser-message="count-files" value="<?= _e('{count} files'); ?>">
        <input type="hidden" storage-browser-message="count-files-selected" value="<?= _e('{count} files selected'); ?>">
    </div>
</div>
<div ceditor-render-element-template="gallery_block">
    <input type="hidden" content-editor-element-item-data="gallery_block">
    <input type="hidden" content-editor-element-gallery-items="data">

    <div ceditor-element-template-item="gallery_block"></div>

    <div ceditor-element-configs="image_block">
        <div class="content-editor-element-preview-block">
            <i class="fas fa-images mb-1"></i>
            <div class="h4"><?= _e('Gallery'); ?></div>

            <button type="button" class="btn btn-text" storage-browser-show="content-editor-gallery-block" storage-browser-select-type="multi">
                <i class="ri-add-fill mr-1"></i>
                <span><?= _e('Add image'); ?></span>
            </button>
        </div>

        <div class="d-none" ceditor-element-template-block>
            <div class="content-editor-gallery-image">
                <div class="content-editor-gallery-image-in">
                    <img src="" alt="image" ceditor-element-gallery-image="">
                    <button type="button" class="btn btn-block btn-secondary" ceditor-element-gallery-block-btn="remove-item">
                        <?= _e('Remove image'); ?>
                    </button>
                </div>
            </div>
        </div>

        <div class="d-none" ceditor-element-extra-buttons>
            <button type="button" ceditor-element-gallery-block-btn="add" storage-browser-show="content-editor-gallery-block" storage-browser-select-type="multi">
                <i class="ri-add-line mr-2"></i>
                <span><?= _e('Add image'); ?></span>
            </button>

            <button type="button" class="d-none" ceditor-element-gallery-block-btn="clear" data-toggle="tooltip" data-placement="top" title="<?= _e('Delete images'); ?>">
                <i class="ri-restart-line"></i>
            </button>
        </div>
    </div>
</div>
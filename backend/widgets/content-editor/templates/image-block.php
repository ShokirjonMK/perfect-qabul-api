<div ceditor-render-element-template="image_block">
    <input type="hidden" content-editor-element-item-data="image_block">

    <div ceditor-element-template-item="image_block"></div>

    <div ceditor-element-configs="image_block">
        <div class="content-editor-element-preview-block">
            <i class="ri-image-fill"></i>
            <div class="h4"><?= _e('Image'); ?></div>

            <button type="button" class="btn btn-text" storage-browser-show="content-editor-image-block">
                <i class="ri-add-fill mr-1"></i>
                <span><?= _e('Add image'); ?></span>
            </button>
        </div>

        <div class="d-none" ceditor-element-template-block>
            <figure>
                <img src="" alt="image">
                <figcaption data-placeholder="<?= _e('Write image caption....'); ?>"></figcaption>
            </figure>
        </div>

        <div class="d-none" ceditor-element-image-block-modal>
            <div class="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= _e('Image properties'); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 form-group">
                                    <label><?= _e('Width'); ?></label>
                                    <input type="text" class="form-control" name="ceditor-element-image-modal-width">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label><?= _e('Height'); ?></label>
                                    <input type="text" class="form-control" name="ceditor-element-image-modal-height">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label><?= _e('Alt text'); ?></label>
                                    <input type="text" class="form-control" name="ceditor-element-image-modal-alt">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label><?= _e('CSS class'); ?></label>
                                    <input type="text" class="form-control" name="ceditor-element-image-modal-class">
                                </div>
                                <div class="col-md-12 form-group">
                                    <label><?= _e('HTML ID'); ?></label>
                                    <input type="text" class="form-control" name="ceditor-element-image-modal-id">
                                </div>
                                <div class="col-md-12">
                                    <label><?= _e('HTML attributes'); ?></label>
                                    <input type="text" class="form-control" name="ceditor-element-image-modal-attrs">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _e('Close'); ?></button>
                            <button type="submit" class="btn btn-primary"><?= _e('Save changes'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none" ceditor-element-extra-buttons>
            <button type="button" class="ceditor-element-image-block-btn-add" storage-browser-show="content-editor-image-block">
                <i class="ri-add-line mr-2"></i>
                <span><?= _e('Add image'); ?></span>
            </button>
            <button type="button" class="ceditor-element-image-block-btn-replace d-none" storage-browser-show="content-editor-image-block" data-toggle="tooltip" data-placement="top" title="<?= _e('Replace image'); ?>">
                <i class="ri-repeat-2-line"></i>
            </button>
            <button type="button" class="ceditor-element-image-block-btn-clear d-none" data-toggle="tooltip" data-placement="top" title="<?= _e('Delete image'); ?>">
                <i class="ri-restart-line"></i>
            </button>
            <button type="button" class="ceditor-element-image-block-btn-attrs d-none" ceditor-element-image-block-btn="properties" data-toggle="tooltip" data-placement="top" title="<?= _e('Image properties'); ?>">
                <i class="ri-edit-fill"></i>
            </button>
        </div>
    </div>
</div>
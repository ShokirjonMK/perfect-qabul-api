<div ceditor-render-element-template="social_media">
    <textarea style="display:none;" class="d-none" content-editor-element-item-data="social_media"></textarea>
    <input type="hidden" content-editor-element-social-media-type="">
    <input type="hidden" content-editor-element-item-message="error" value="<?= _e('Invalid embed code. Please try again.'); ?>">

    <div ceditor-element-template-item></div>

    <div ceditor-element-configs="social_media">
        <div class="content-editor-element-preview-block">
            <div class="content-editor-social-media-icon">
                <i class="ri-code-s-slash-line"></i>
            </div>
            <div class="h4 content-editor-social-media-title">Social media</div>

            <button type="button" class="btn btn-text" ceditor-element-social-media-btn="add">
                <i class="ri-add-fill mr-1"></i>
                <span><?= _e('Insert embed code'); ?></span>
            </button>
        </div>

        <div class="d-none" ceditor-element-social-media-modal>
            <div class="modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title content-editor-social-media-title">Social media</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <label><?= _e('Embed code'); ?></label>
                                    <textarea class="form-control" name="embed_code" rows="4"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= _e('Close'); ?></button>
                            <button type="submit" class="btn btn-primary"><?= _e('Save'); ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-none" ceditor-element-extra-buttons>
            <button type="button" ceditor-element-social-media-btn="add">
                <i class="ri-add-line mr-2"></i>
                <span><?= _e('Insert code'); ?></span>
            </button>

            <button type="button" class="d-none" ceditor-element-social-media-btn="replace" data-toggle="tooltip" data-placement="top" title="<?= _e('Replace code'); ?>">
                <i class="ri-repeat-2-line"></i>
            </button>

            <button type="button" class="d-none" ceditor-element-social-media-btn="clear" data-toggle="tooltip" data-placement="top" title="<?= _e('Delete code'); ?>">
                <i class="ri-restart-line"></i>
            </button>
        </div>
    </div>
</div>
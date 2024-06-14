<div class="d-none" ceditor-section-block>
    <div class="content-editor-item" ceditor-section-item>
        <div class="content-editor-card-top">
            <div class="content-editor-card-left">
                <div class="content-editor-card-index"></div>
                <input type="text" class="content-editor-card-title">
            </div>

            <div class="content-editor-card-right">
                <div class="btn" ceditor-action="settings-section-item" data-toggle="tooltip" data-placement="top" title="<?= _e('Settings'); ?>">
                    <i class="ri-settings-5-fill"></i>
                </div>
                <div class="btn" ceditor-action="clone-section-item" data-toggle="tooltip" data-placement="top" title="<?= _e('Clone'); ?>">
                    <i class="ri-file-copy-line"></i>
                </div>
                <div class="btn" ceditor-action="delete-section-item" data-toggle="tooltip" data-placement="top" title="<?= _e('Delete'); ?>">
                    <i class="ri-delete-bin-6-line"></i>
                </div>
                <div class="btn" ceditor-action="collapse-section-item" data-toggle="tooltip" data-placement="top" title="<?= _e('Collapse'); ?>">
                    <i class="ri-arrow-up-s-line"></i>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="content-editor-data-html" content-editor-html></div>

                <div class="content-editor-add-element-btn-block">
                    <button type="button" class="content-editor-add-element-btn" ceditor-side-menu-open="elements">
                        <i class="ri-add-circle-line"></i>
                        <span><?= _e('Add element'); ?></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="content-editor-add-section-down">
            <button type="button" ceditor-section-action="add-down">
                <i class="ri-add-circle-fill"></i>
            </button>
        </div>
    </div>
</div>

<div class="d-none" ceditor-section-settings-modal>
    <div class="modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= _e('Section settings'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            <label><?= _e('CSS classes'); ?></label>
                            <input type="text" class="form-control" name="ceditor-section-settings-class">
                        </div>
                        <div class="col-md-12 form-group">
                            <label><?= _e('HTML ID'); ?></label>
                            <input type="text" class="form-control" name="ceditor-section-settings-id">
                        </div>
                        <div class="col-md-12">
                            <label><?= _e('HTML attributes'); ?></label>
                            <input type="text" class="form-control" name="ceditor-section-settings-attrs">
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

<div class="d-none" ceditor-add-element-btn>
    <div class="content-editor-add-element-btn-block">
        <button type="button" class="content-editor-add-element-btn" ceditor-side-menu-open="elements">
            <i class="ri-add-circle-line"></i>
            <span><?= _e('Add element'); ?></span>
        </button>
    </div>
</div>

<div class="d-none" ceditor-add-element-repeat-btn>
    <div class="content-editor-add-element-btn-block">
        <button type="button" class="content-editor-add-element-btn" ceditor-action="element-repeat">
            <i class="ri-add-circle-line"></i>
            <span><?= _e('Add element'); ?></span>
        </button>
    </div>
</div>

<div class="d-none" ceditor-element-actions>
    <div class="content-editor-element-top-actions">
        <button type="button" ceditor-action="element-up" data-toggle="tooltip" data-placement="top" title="<?= _e('Move up'); ?>">
            <i class="ri-arrow-up-line"></i>
        </button>
        <button type="button" ceditor-action="element-down" data-toggle="tooltip" data-placement="top" title="<?= _e('Move down'); ?>">
            <i class="ri-arrow-down-line"></i>
        </button>
        <button type="button" ceditor-action="element-delete" data-toggle="tooltip" data-placement="top" title="<?= _e('Delete element'); ?>">
            <i class="ri-delete-bin-6-line"></i>
        </button>
    </div>
    <div class="content-editor-element-bottom-actions">
        <button type="button" ceditor-action="add-element-down">
            <i class="ri-add-line"></i>
        </button>
    </div>
</div>
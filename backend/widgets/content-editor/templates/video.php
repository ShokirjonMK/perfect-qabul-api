<div ceditor-render-element-template="video">
    <input type="hidden" content-editor-element-item-data="video">

    <div ceditor-element-template-item="video"></div>

    <div ceditor-element-configs="video">
        <div class="content-editor-element-preview-block">
            <i class="ri-play-circle-line"></i>
            <div class="h4"><?= _e('Video'); ?></div>

            <button type="button" class="btn btn-text" storage-browser-show="content-editor-video">
                <i class="ri-add-fill mr-1"></i>
                <span><?= _e('Add file'); ?></span>
            </button>
        </div>

        <div class="d-none" ceditor-element-template-block>
            <video width="500" controls>
                <source src="" type="video/mp4">
                <?= _e('Your browser does not support HTML video.'); ?>
            </video>
        </div>

        <div class="d-none" ceditor-element-extra-buttons>
            <button type="button" class="ceditor-element-video-btn-add" storage-browser-show="content-editor-video">
                <i class="ri-add-line mr-2"></i>
                <span><?= _e('Add file'); ?></span>
            </button>

            <button type="button" class="ceditor-element-video-btn-replace d-none" storage-browser-show="content-editor-video" data-toggle="tooltip" data-placement="top" title="<?= _e('Replace file'); ?>">
                <i class="ri-repeat-2-line"></i>
            </button>

            <button type="button" class="ceditor-element-video-btn-clear d-none" data-toggle="tooltip" data-placement="top" title="<?= _e('Delete file'); ?>">
                <i class="ri-restart-line"></i>
            </button>
        </div>
    </div>
</div>
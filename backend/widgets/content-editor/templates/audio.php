<div ceditor-render-element-template="audio">
    <input type="hidden" content-editor-element-item-data="audio">

    <div ceditor-element-template-item="audio"></div>

    <div ceditor-element-configs="audio">
        <div class="content-editor-element-preview-block">
            <i class="ri-music-fill"></i>
            <div class="h4"><?= _e('Audio'); ?></div>

            <button type="button" class="btn btn-text" storage-browser-show="content-editor-audio">
                <i class="ri-add-fill mr-1"></i>
                <span><?= _e('Add file'); ?></span>
            </button>
        </div>

        <div class="d-none" ceditor-element-template-block>
            <audio controls>
                <source src="" type="audio/mpeg">
                <?= _e('Your browser does not support the audio tag.'); ?>
            </audio>
        </div>

        <div class="d-none" ceditor-element-extra-buttons>
            <button type="button" class="ceditor-element-audio-btn-add" storage-browser-show="content-editor-audio">
                <i class="ri-add-line mr-2"></i>
                <span><?= _e('Add file'); ?></span>
            </button>

            <button type="button" class="ceditor-element-audio-btn-replace d-none" storage-browser-show="content-editor-audio" data-toggle="tooltip" data-placement="top" title="<?= _e('Replace file'); ?>">
                <i class="ri-repeat-2-line"></i>
            </button>

            <button type="button" class="ceditor-element-audio-btn-clear d-none" data-toggle="tooltip" data-placement="top" title="<?= _e('Delete file'); ?>">
                <i class="ri-restart-line"></i>
            </button>
        </div>
    </div>
</div>
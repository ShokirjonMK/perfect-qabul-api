<?php if ($elements) : ?>
    <div class="content-editor-side-menu" ceditor-side-menu="elements">
        <div class="content-editor-side-menu-in">
            <?php foreach ($elements as $element_item) : ?>
                <div class="content-editor-side-menu-item">
                    <div class="h4"><?= array_value($element_item, 'group_title'); ?></div>

                    <?php $group_items = array_value($element_item, 'group_items'); ?>
                    <ul class="content-editor-side-menu-list">
                        <?php foreach ($group_items as $group_key => $group_item) : ?>
                            <li>
                                <div ceditor-insert-element="<?= $group_key ?>" class="content-editor-side-menu-list-item">
                                    <?php
                                    $element_icon = array_value($group_item, 'icon');
                                    $element_image = array_value($group_item, 'image');

                                    if ($element_icon) {
                                        echo $element_icon;
                                    } else if ($element_image) {
                                        if (is_url($element_image)) {
                                            echo '<img src="' . $element_image . '" alt="image">';
                                        } else {
                                            echo '<img src="' . $this->assetsUrl($element_image) . '" alt="image">';
                                        }
                                    } else {
                                        echo '<img class="ri-plug-line"></img>';
                                    } ?>
                                    <span><?= array_value($group_item, 'title'); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
        </div>

        <button type="button" class="content-editor-side-menu-close">
            <i class="ri-arrow-left-line"></i>
            <span><?= _e('Close'); ?></span>
        </button>
    </div>
<?php endif; ?>

<?php if ($sections) : ?>
    <div class="content-editor-side-menu" ceditor-side-menu="sections">
        <div class="content-editor-side-menu-in">
            <div class="content-editor-side-menu-item">
                <div class="h4"><?= _e('Sections'); ?></div>

                <ul class="content-editor-side-menu-list">
                    <?php foreach ($sections as $section_key => $section) : ?>
                        <li>
                            <div ceditor-insert-section="<?= $section_key; ?>" class="content-editor-side-menu-list-item">
                                <?php
                                $section_icon = array_value($section, 'icon');
                                $section_image = array_value($section, 'image');

                                if ($section_icon) {
                                    echo $section_icon;
                                } else if ($section_image) {
                                    if (is_url($section_image)) {
                                        echo '<img src="' . $section_image . '" alt="image">';
                                    } else {
                                        echo '<img src="' . $this->assetsUrl($section_image) . '" alt="image">';
                                    }
                                } else {
                                    echo '<img class="ri-plug-line"></img>';
                                } ?>
                                <span><?= array_value($section, 'title', '-'); ?></span>
                            </div>

                            <div class="d-none" ceditor-section-type="<?= $section_key; ?>">
                                <?= array_value($section, 'html'); ?>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <button type="button" class="content-editor-side-menu-close">
            <i class="ri-arrow-left-line"></i>
            <span><?= _e('Close'); ?></span>
        </button>
    </div>
<?php endif; ?>
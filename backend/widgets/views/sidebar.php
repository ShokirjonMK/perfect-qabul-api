<?php
$app = Yii::$app;
$current_url = get_current_url();

use yii\helpers\Url; ?>

<div class="vertical-menu">
    <div data-simplebar class="h-100">
        <div id="sidebar-menu">
            <ul class="metismenu list-unstyled" id="side-menu">
                <?php foreach (backend_sidebar_menu() as $item) : ?>
                    <?php
                    $a_class_name = 'waves-effect';
                    $li_class_name = 'mm-menu-item';
                    $sort = array_value($item, 'sort');
                    $childs = array_value($item, 'childs');
                    $active_link = array_value($item, 'active_link');
                    $menu_title = array_value($item, 'menu_title');

                    $item_url = array_value($item, 'url');
                    $item_url_str = array_value($item, 'url') == '#' ? 'javascript: void(0);' : Url::to([$item_url]);

                    if ($childs) {
                        $a_class_name .= ' has-arrow';
                    }

                    if ($active_link) {
                        $a_class_name .= ' mm-active';
                        $li_class_name .= ' mm-active';
                    } 

                    if ($menu_title) {?>
                        <li class="menu-title"><?= array_value($item, 'name'); ?></li>
                    <?}else{?>
                        <li class="<?= trim($li_class_name); ?>" data-sort="<?= $sort; ?>">
                            <a href="<?= $item_url_str; ?>" class="<?= trim($a_class_name); ?>">
                                <?= array_value($item, 'icon'); ?>
                                <span><?= array_value($item, 'name'); ?></span>
                            </a>
                            <?php if ($childs) : ?>
                                <ul class="sub-menu" aria-expanded="false">
                                    <?php foreach ($childs as $child_item) : ?>
                                        <?php
                                        $child_a_class_name = 'mm-submenu-link';
                                        $child_li_class_name = 'mm-submenu-item';
                                        $child_active_link = array_value($child_item, 'active_link');

                                        $child_item_url = array_value($child_item, 'url');
                                        $child_item_url_str = array_value($child_item, 'url') == '#' ? 'javascript: void(0);' : Url::to([$child_item_url]);

                                        if ($child_active_link) {
                                            $child_a_class_name .= ' active';
                                            $child_li_class_name .= ' mm-active';
                                        } ?>
                                        <li class="<?= trim($child_li_class_name); ?>">
                                            <a href="<?= $child_item_url_str; ?>" class="<?= trim($child_a_class_name); ?>">
                                                <?= array_value($child_item, 'name'); ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        </li>
                    <?php } ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
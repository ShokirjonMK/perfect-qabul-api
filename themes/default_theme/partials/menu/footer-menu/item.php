<?php
$li_class = "menu-item menu-item-{$id}";
$li_class .= $class_name ? " {$class_name}" : "";

$li_attributes = 'class="'. $li_class .'"';
$li_attributes .= $link_target ? ' target="' . $link_target . '"' : '';
$li_attributes .= $attrs ? ' ' . $attrs : '';

$icon_url = '';
$icon_class = '';

if ($icon && is_url($icon)) {
    $icon_url = $icon;
} elseif ($icon && is_string($icon)) {
    $icon_class = $icon;
} elseif ($image) {
    $icon_url = $icon;
} ?>
<li <?= $li_attributes; ?>>
    <a href="<?= $link; ?>">
        <?php
        if ($icon_class) {
            echo '<i class="' . $icon . ' mr-2"></i>';
        } elseif ($icon_url) {
            echo '<img src="' . $icon_url . '" alt="' . $name . '" height="20">';
        }

        echo $name;

        if ($has_childs) {
            echo '<i class="ri-arrow-down-s-line icon-arow-down ml-1"></i>';
        } ?>
    </a>
    <?= $wrapper; ?>
</li>
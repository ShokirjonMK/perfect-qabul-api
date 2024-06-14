<?php
$li_attributes = $class_name ? ' class="' . $class_name . '"' : '';
$li_attributes .= $link_target ? ' target="' . $link_target . '"' : '';
$li_attributes .= $attrs ? ' ' . $attrs : ''; ?>

<li <?= $li_attributes; ?>>
    <a href="<?= $link; ?>">
        <?php
        if ($icon) {
            echo '<i class="' . $icon . '"></i>';
        } elseif ($image) {
            echo '<img src="' . $image . '" alt="' . $name . '" height="20">';
        }

        echo $name; ?>
    </a>
    <?= $wrapper; ?>
</li>
<?php
/*
 * Available variables:
 * 
 * Element: $element_array
 * Element key: $key
 * Element type: $type
 * 
 * Image URL: $url
 * Image data: $data
 * Image attributes: $attributes
 * Figcaption: $figcaption
 */ ?>
<figure class="image-block">
    <a href="<?= $url; ?>" data-fancybox>
        <img src="<?= $url; ?>" <?= isset($attributes) ? $attributes : 'alt="image"'; ?>>
    </a>

    <?php
    if (isset($figcaption) && $figcaption) {
        echo '<figcaption>' . $figcaption . '</figcaption>';
    } ?>
</figure>
<?php
/*
 * Available variables:
 * 
 * Element: $element_array
 * Element key: $key
 * Element type: $type
 * Element items: $items
 */

if ($items) : ?>
    <div class="gallery-block">
        <?php foreach ($items as $item) : ?>
            <div class="gallery-block-item">
                <a href="<?= array_value($item, 'url'); ?>" data-fancybox="gallery">
                    <img src="<?= array_value($item, 'url'); ?>" alt="image">
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
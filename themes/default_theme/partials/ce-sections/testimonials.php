<?php
/*
 * Available variables:
 * 
 * Section: $section
 * Section elements: $elements
 */

if ($elements) : ?>
    <section <?= $section->section_attrs; ?>>
        <?php
        foreach ($elements as $element) {
            echo content_editor_elements($element);
        } ?>
    </section>
<?php endif; ?>
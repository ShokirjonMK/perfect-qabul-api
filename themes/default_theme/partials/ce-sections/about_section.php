<?php
/*
 * Available variables:
 * 
 * Section: $section
 * Section elements: $elements
 */

$section_items = content_editor_section_items($elements);

$image = array_value($section_items, 'image');
$heading = array_value($section_items, 'heading');
$description = array_value($section_items, 'description');

if ($section_items) : ?>
    <section <?= $section->section_attrs; ?>>
        <div class="container pt-4 pb-4">
            <div class="row">
                <div class="col-md-6">
                    <img src="<?= array_value($image, 'url'); ?>" class="img-fluid" alt="Image">
                </div>

                <div class="col-md-6">
                    <div class="pl-4">
                        <?= array_value($heading, 'html'); ?>
                        <?= array_value($description, 'html'); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>
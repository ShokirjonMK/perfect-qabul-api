<?php

use backend\models\Content;
use backend\models\Segment;

$attrs = array();
$segments = array();
$form_class = 'form-group';

$input = array_value($values, 'input');
$label = array_value($values, 'label');
$required = array_value($values, 'required');

if ($required) {
    $attrs['required'] = 'required';
    $form_class .= ' required-field';
} ?>

<div class="<?= trim($form_class); ?>">
    <?php
    if ($input == 'select') {
        $attrs['class'] = 'form-control select2';
        $segments = Segment::getListParent('content', $info, 0, $segment_key);
        $model->segment_relations[$segment_key] = Content::getRelationsArray($model, true);
        echo html_entity_decode($form->field($model, 'segment_relations[' . $segment_key . ']')->dropDownList($segments, $attrs)->label($label));
    } elseif ($input == 'select-multiple') {
        $attrs['class'] = 'form-control select2';
        $attrs['multiple'] = 'multiple';
        $attrs['data-allow-clear'] = 'true';

        $segments = Segment::getListParent('content', $info, 0, $segment_key);
        $model->segment_relations[$segment_key] = Content::getRelationsArray($model);

        if (isset($segments[''])) {
            unset($segments['']);
        }

        echo html_entity_decode($form->field($model, 'segment_relations[' . $segment_key . ']')->dropDownList($segments, $attrs)->label($label));
    } ?>
</div>
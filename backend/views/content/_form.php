<?php

use yii\helpers\Html;
use backend\models\Content;
use backend\widgets\ContentEditorWidget;
use backend\widgets\ContentWidget;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use backend\widgets\PageSeoWidget;
use backend\widgets\StorageWidget;

$controller = Yii::$app->controller;
$content_key = array_value($controller->settings, 'key');
$content_lexicon = array_value($controller->settings, 'lexicon');
$content_segments = array_value($controller->settings, 'segments');
$content_image_fields = array_value($controller->settings, 'image_fields');
$items_with_parent = array_value($controller->settings, 'items_with_parent');

$theme_fields = content_editor_fields();
$languages = admin_active_langs();
$langs_array = ArrayHelper::map($languages, 'lang_code', 'name');

if ($model->isNewRecord) {
    $model->sort = 0;
    $model->searchable = true;
    $model->cacheable = true;
    $model->products_per_page = 0;
    $model->created_by = Yii::$app->user->id;
    $model->updated_by = Yii::$app->user->id;
    $info->language = admin_content_lexicon('lang_code');
} else {
    $info = init_content_meta($info);
    $model = init_content_settings($model);
}

// Languages
$lang = input_get('lang');

if (is_string($lang) && $lang) {
    $info->language = $lang;
}

// Parent id
$parent_id = input_get('parent');

if (is_numeric($parent_id) && $parent_id > 0) {
    $model->parent_id = $parent_id;
}

// Begin form
$form = ActiveForm::begin([
    'options' => [
        'class' => 'full-form',
    ],
]); ?>

<!-- Nav tabs -->
<ul class="nav nav-tabs nav-tabs-custom nav-justified mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#general" role="tab">
            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
            <span class="d-none d-sm-block"><?= _e('General'); ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#content" role="tab">
            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
            <span class="d-none d-sm-block"><?= _e('Content'); ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#settings" role="tab">
            <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
            <span class="d-none d-sm-block"><?= _e('Settings'); ?></span>
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- Tab item -->
    <div class="tab-pane active" id="general" role="tabpanel">
        <div class="row">
            <!-- Left column -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-body">
                        <div class="form-group required-field">
                            <?= $form->field($info, 'title')->textInput(['required' => 'required', 'ep-bind-action' => 'title']) ?>
                        </div>

                        <div class="form-group">
                            <div class="d-none">
                                <?= $form->field($info, 'description')->textarea(['id' => 'info-description-textarea']) ?>
                            </div>

                            <label><?= $info->getAttributeLabel('description'); ?></label>
                            <div class="content-inline-editor" id="content-description-html" data-tinymce="inline" data-save-to="#info-description-textarea"><?= (!is_null($info->description) && !empty($info->description)) ? $info->description : ''; ?></div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="form-group">
                            <?= $form->field($info, 'meta_title')->textInput() ?>
                        </div>

                        <div class="form-group">
                            <?= $form->field($info, 'focus_keywords')->textInput() ?>
                        </div>

                        <div class="form-group">
                            <?= $form->field($info, 'meta_description')->textarea() ?>
                        </div>
                    </div>
                </div>

                <?= PageSeoWidget::widget(); ?>
            </div>
            <!-- /Left column -->

            <!-- Right column -->
            <div class="col-md-3">
                <?php if ($languages && count($languages) > 1) : ?>
                    <div class="card">
                        <div class="card-body full-form-translations">
                            <div class="form-group required-field">
                                <?= $form->field($info, 'language')->dropDownList($langs_array, ['class' => 'form-control custom-select c-translation-select', 'required' => 'required']) ?>
                            </div>

                            <?php if (isset($translations) && $translations) : ?>
                                <div class="form-group">
                                    <label for="brand_language"><?= _e('Translations'); ?></label>
                                    <?php foreach ($languages as $language) : ?>
                                        <?php
                                        $translations_array = array();
                                        $language_code = $language['lang_code'];

                                        foreach ($translations as $translation_item) {
                                            $translations_array[$translation_item->language] = $translation_item->title;
                                        } ?>
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" data-toggle="tooltip" data-placement="bottom" title="<?= $language['name']; ?>">
                                                    <img src="<?= $language['flag']; ?>" alt="<?= $language_code; ?>" height="10" width="17">
                                                </span>
                                            </div>
                                            <?php if (isset($translations_array[$language_code])) : ?>
                                                <input type="text" class="form-control" placeholder="<?= $translations_array[$language_code]; ?>" disabled>
                                                <div class="input-group-append">
                                                    <a href="<?= set_query_var('lang', $language_code); ?>" class="input-group-text">
                                                        <i class="ri-edit-2-fill"></i>
                                                    </a>
                                                </div>
                                            <?php else : ?>
                                                <input type="text" class="form-control" placeholder="<?= _e('No translation'); ?>" disabled>
                                                <div class="input-group-append">
                                                    <a href="<?= set_query_var('lang', $language_code); ?>" class="input-group-text">
                                                        <i class="ri-add-fill"></i>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else : ?>
                                <div class="form-group">
                                    <label for="brand_language"><?= _e('Translations'); ?></label>
                                    <?php foreach ($languages as $language) : ?>
                                        <?php $language_code = $language['lang_code']; ?>
                                        <div class="input-group mt-2">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" data-toggle="tooltip" data-placement="bottom" title="<?= $language['name']; ?>">
                                                    <img src="<?= $language['flag']; ?>" alt="<?= $language_code; ?>" height="10" width="17">
                                                </span>
                                            </div>
                                            <input type="text" class="form-control" name="translations_title[<?= $language_code; ?>]" data-translation-code="<?= $language_code; ?>" placeholder="Title" required>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <?php if ($items_with_parent) : ?>
                            <div class="form-group required-field">
                                <?= html_entity_decode($form->field($model, 'parent_id')->dropDownList(
                                    Content::getListParent($model, $info),
                                    [
                                        'class' => 'form-control select2',
                                        'required' => 'required',
                                    ]
                                )) ?>
                            </div>
                        <?php endif; ?>

                        <div class="form-group required-field">
                            <?= $form->field($model, 'status')->dropDownList($model->statusArray(), ['class' => 'form-control custom-select', 'required' => 'required']) ?>
                        </div>

                        <div class="form-group">
                            <?php
                            if (!$model->isNewRecord) {
                                $url = get_content_url($info, $info->language);
                                $slug_template = field_slug_input(null, $url);
                            } else {
                                $slug_template = field_slug_input();
                            }

                            echo $form->field($info, 'slug', ['template' => $slug_template])->textInput(['readonly' => 'readonly']); ?>
                        </div>

                        <div class="form-group required-field">
                            <?= $form->field($model, 'sort')->textInput(['type' => 'number', 'required' => 'required']) ?>
                        </div>
                    </div>
                </div>

                <?php if ($content_image_fields) : ?>
                    <div class="card">
                        <div class="card-body">
                            <?php foreach ($content_image_fields as $content_image_field_key) : ?>
                                <div class="form-group position-relative">
                                    <?php
                                    $field_template = StorageWidget::widget([
                                        'format' => 'form',
                                        'select_type' => 'single',
                                        'action' => 'info_' . $content_image_field_key,
                                    ]);

                                    echo $form->field($info, $content_image_field_key, ['template' => $field_template])
                                        ->textInput(['type' => 'hidden', 'storage-browser-value' => 'image']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($content_segments) : ?>
                    <div class="card">
                        <div class="card-body">
                            <?php foreach ($content_segments as $content_segment_key => $content_segment_item) : ?>
                                <?= ContentWidget::widget([
                                    'type' => 'segment',
                                    'key' => $content_segment_key,
                                    'values' => $content_segment_item,
                                    'form' => $form,
                                    'model' => $model,
                                    'info' => $info,
                                ]); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            <!-- /Right column -->
        </div>
    </div> <!-- /Tab item -->

    <!-- Tab item -->
    <div class="tab-pane" id="content" role="tabpanel">
        <?= ContentEditorWidget::widget(['fields' => $theme_fields, 'save_input' => '#content_blocks_save_input_01']); ?>

        <div class="d-none">
            <?php
            if (is_array($info->content_blocks) && $info->content_blocks) {
                $info->content_blocks = json_encode($info->content_blocks);
            } else {
                $info->content_blocks = null;
            }

            echo $form->field($info, 'content_blocks')->textarea(['id' => 'content_blocks_save_input_01', 'class' => 'd-none']); ?>
        </div>
    </div> <!-- /Tab item -->

    <!-- Tab item -->
    <div class="tab-pane" id="settings" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 form-group">
                        <label><?= _e('Created on'); ?></label>
                        <input type="text" value="<?= $model->created_on ? $model->created_on : '-' ?>" class="form-control" disabled>
                    </div>

                    <div class="col-md-6 form-group">
                        <label><?= _e('Updated on'); ?></label>
                        <input type="text" value="<?= $model->updated_on ? $model->updated_on : '-' ?>" class="form-control" disabled>
                    </div>

                    <div class="col-md-6 form-group">
                        <?php
                        $resourceTypes = Content::resourceTypes($controller->content_type);
                        $resourceTypeDropDownAttrs = ['class' => 'form-control custom-select'];

                        if (count($resourceTypes) === 1) {
                            $resourceTypeDropDownAttrs['disabled'] = 'disabled';
                        }

                        echo $form->field($model, 'resource_type')->dropDownList(
                            $resourceTypes,
                            $resourceTypeDropDownAttrs
                        ); ?>
                    </div>

                    <div class="col-md-6 form-group">
                        <?= $form->field($model, 'template')->textInput() ?>
                    </div>

                    <div class="col-md-6 form-group">
                        <?= $form->field($model, 'layout')->textInput() ?>
                    </div>

                    <div class="col-md-6 form-group">
                        <?= $form->field($model, 'view')->textInput() ?>
                    </div>

                    <div class="col-md-12 form-group">
                        <label><?= _e('Properties'); ?></label>
                        <br>

                        <div class="d-inline-block custom-control custom-checkbox mr-2">
                            <?php
                            $checkbox_template = '{input}';
                            $checkbox_template .= '<label class="custom-control-label" for="content-searchable">Searchable</label>';
                            $checkbox_attrs = ['class' => 'custom-control-input', 'type' => 'checkbox', 'value' => 1];

                            if ($model->searchable) {
                                $checkbox_attrs['checked'] = true;
                            }

                            echo $form->field(
                                $model,
                                'searchable',
                                ['template' => $checkbox_template]
                            )->textInput($checkbox_attrs); ?>
                        </div>

                        <div class="d-inline-block custom-control custom-checkbox mr-2">
                            <?php
                            $checkbox_template = '{input}';
                            $checkbox_template .= '<label class="custom-control-label" for="content-cacheable">Cacheable</label>';
                            $checkbox_attrs = ['class' => 'custom-control-input', 'type' => 'checkbox', 'value' => 1];

                            if ($model->cacheable) {
                                $checkbox_attrs['checked'] = true;
                            }

                            echo $form->field(
                                $model,
                                'cacheable',
                                ['template' => $checkbox_template]
                            )->textInput($checkbox_attrs); ?>
                        </div>

                        <div class="d-inline-block custom-control custom-checkbox mr-2">
                            <?php
                            $checkbox_template = '{input}';
                            $checkbox_template .= '<label class="custom-control-label" for="content-deleted">Deleted</label>';
                            $checkbox_attrs = ['class' => 'custom-control-input', 'type' => 'checkbox', 'value' => 1];

                            if ($model->deleted) {
                                $checkbox_attrs['checked'] = true;
                            }

                            echo $form->field(
                                $model,
                                'deleted',
                                ['template' => $checkbox_template]
                            )->textInput($checkbox_attrs); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- /Tab item -->
</div>

<div class="mb-3 full-form-btns">
    <?php
    if ($model->isNewRecord) {
        echo Html::submitButton('<i class="ri-check-line mr-1"></i> ' . _e('Create & open'), ['class' => 'btn btn-success waves-effect btn-with-icon']);
        echo Html::submitButton('<i class="ri-add-circle-line mr-1"></i> ' . _e('Create & add another'), ['class' => 'btn btn-primary waves-effect btn-with-icon', 'name' => 'submit_button', 'value' => 'create_and_add_new']);
    } else {
        echo Html::submitButton('<i class="ri-check-line mr-1"></i> ' . _e('Save'), ['class' => 'btn btn-success waves-effect btn-with-icon']);
        echo Html::submitButton('<i class="ri-add-fill mr-1"></i> ' . _e('Save & add another'), ['class' => 'btn btn-primary waves-effect btn-with-icon', 'name' => 'submit_button', 'value' => 'create_and_add_new']);
    } ?>
</div>
<?php ActiveForm::end(); ?>
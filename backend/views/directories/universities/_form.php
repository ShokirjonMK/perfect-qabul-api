<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

$languages = admin_active_langs();
$langs_array = ArrayHelper::map($languages, 'lang_code', 'name');
$content_language = admin_content_lexicon('lang_code');

if ($model->isNewRecord) {
    $model->sort = 0;
    $model->created_by = Yii::$app->user->id;
    $model->updated_by = Yii::$app->user->id;
    $info->language = $content_language;
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

// dd($info);

// Begin form
$form = ActiveForm::begin([
    'options' => [
        'class' => 'full-form',
    ],
]); ?>



<div class="row">
    <!-- Left column -->
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                
                <!-- Nav tabs -->
                <ul class="nav nav-tabs nav-tabs-custom nav-justified mb-3" role="tablist">
                    <?php foreach($languages as $lang){?>
                        <li class="nav-item">
                            <a class="nav-link <?php if($content_language == $lang['lang_code']) echo "active"?>" data-toggle="tab" href="#tab_<?=$lang['lang_code']?>" role="tab">
                                <span class="d-none d-sm-block">
                                    <img src="<?= $lang['flag']; ?>" alt="<?= $lang['lang_code']; ?>" height="10" width="17">
                                    <?=$lang['name']?>
                                </span>
                            </a>
                        </li>
                    <?php } ?>
                </ul>
                <!-- /Nav tabs -->

                <div class="tab-content">
                    <?php foreach($languages as $lang){?>
                        <!-- Tab item -->
                        <div class="tab-pane <?php if($content_language == $lang['lang_code']) echo "active"?>" id="tab_<?=$lang['lang_code']?>" role="tabpanel">
                            
                            <div class="form-group required-field">
                                <?= $form->field($info, 'name['.$lang['lang_code'].']')->textInput(['required' => 'required', 'ep-bind-action' => 'title']) ?>
                            </div>

                            <div class="form-group">
                                <?= $form->field($info, 'description['.$lang['lang_code'].']')->textarea() ?>
                            </div>

                            <div class="form-group">
                                <?= $form->field($info, 'address['.$lang['lang_code'].']')->textInput() ?>
                            </div>

                            <div class="form-group">
                                <?= $form->field($info, 'parent['.$lang['lang_code'].']')->textInput() ?>
                            </div>
                        
                        </div>
                        <!-- /Tab item -->
                    <?php } ?>
                
                </div>
                
                
                

            </div>
        </div>



    </div>
    <!-- /Left column -->

    <!-- Right column -->
    <div class="col-md-3">

        <div class="card">
            <div class="card-body">

                <div class="form-group required-field">
                    <?= $form->field($model, 'country_id')->dropDownList(
                        $countries,
                        [
                            'prompt' => _e('...'),
                            'class' => 'form-control select2',
                        ]
                    ) ?>
                </div>
                
                <div class="form-group">
                    <?= $form->field($model, 'region_id')->dropDownList(
                        $regions,
                        [
                            'prompt' => _e('...'),
                            'class' => 'form-control select2',
                        ]
                    ) ?>
                </div>

                <div class="form-group required-field">
                    <?= $form->field($model, 'status')->dropDownList($model->statusArray(), ['class' => 'form-control custom-select', 'required' => 'required']) ?>
                </div>

                <div class="form-group required-field">
                    <?= $form->field($model, 'sort')->textInput(['type' => 'number', 'required' => 'required']) ?>
                </div>
            </div>
        </div>

    </div>
    <!-- /Right column -->
</div>

<div class="mb-3 full-form-btns">
    <?php
    if ($model->isNewRecord) {
        echo Html::submitButton('<i class="ri-check-line mr-1"></i> ' . _e('Create'), ['class' => 'btn btn-success waves-effect btn-with-icon']);
        echo Html::submitButton('<i class="ri-add-circle-line mr-1"></i> ' . _e('Create & add another'), ['class' => 'btn btn-primary waves-effect btn-with-icon', 'name' => 'submit_button', 'value' => 'create_and_add_new']);
    } else {
        echo Html::submitButton('<i class="ri-check-line mr-1"></i> ' . _e('Save'), ['class' => 'btn btn-success waves-effect btn-with-icon']);
        echo Html::submitButton('<i class="ri-add-fill mr-1"></i> ' . _e('Save & add another'), ['class' => 'btn btn-primary waves-effect btn-with-icon', 'name' => 'submit_button', 'value' => 'create_and_add_new']);
    } ?>
</div>
<?php ActiveForm::end(); ?>
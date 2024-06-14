<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use common\models\Countries;
use common\models\Regions;
use yii\helpers\Url;

$data = Countries::find()->asArray()->all();
// $data1 = Regions::find()->where(['type' => Regions::TYPE_CITY])->asArray()->all();
$countries = ArrayHelper::map($data, 'id', 'name');
// $cities = ArrayHelper::map($data1, 'id', 'name');

$cities = (!$model->isNewRecord) ? Regions::listRegions($model->country_id) : [];

$languages = admin_active_langs();

// Begin form
$form = ActiveForm::begin([
    'options' => [
        'class' => 'full-form'
    ]
]); ?>

<div class="card">
    <div class="card-body">
        <span id="url-regions" data-url="<?= Url::to(['json/get-regions']) ?>"></span>
        <div class="row">
            <div class="col-md-8">
                <div class="form-group required-field">
                    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'required' => 'required']) ?>
                </div>
                <div class="row">
                    <div class="col-md-6 form-group required-field">
                        <?= $form->field($model, 'country_id')->dropDownList(
                            $countries,
                            [
                                'prompt' => '...',
                                'class' => 'form-control select2',
                                'required' => 'required'
                            ]
                        ) ?>
                    </div>
                    <div class="col-md-6 form-group required-field">
                        <?= $form->field($model, 'parent_id')->dropDownList(
                            $cities,
                            [
                                'prompt' => '...',
                                'class' => 'form-control select2',
                                'required' => 'required'
                            ]
                        ) ?>
                    </div>
                    <div class="col-md-6 form-group">
                        <?= $form->field($model, 'postcode')->textInput() ?>
                    </div>
                    <div class="col-md-6 form-group">
                        <?= $form->field($model, 'lat')->textInput() ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group required-field">
                    <?= $form->field($model, 'status')->dropDownList([1 => _e('Active'), 0 => _e('Disabled')], ['required' => 'required']) ?>
                </div>

                <div class="form-group required-field">
                    <?= $form->field($model, 'sort')->textInput(['type' => 'number', 'required' => 'required', 'value' => 0]) ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'long')->textInput() ?>
                </div>
            </div>
        </div>
    </div>
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
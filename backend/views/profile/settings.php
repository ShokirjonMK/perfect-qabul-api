<?php

use common\models\Countries;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = _e('Profile settings');
$this->breadcrumb_title = _e('Profile settings');
$this->breadcrumbs[] = ['label' => _e('Profile'), 'url' => $main_url];

// Begin form
$form = ActiveForm::begin([
    'options' => [
        'class' => 'full-form'
    ]
]) ?>

<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 form-group required-field">
                <?= $form->field($profile, 'firstname')->textInput(['required' => 'required']) ?>
            </div>

            <div class="col-md-6 form-group required-field">
                <?= $form->field($profile, 'lastname')->textInput(['required' => 'required']) ?>
            </div>

            <div class="col-md-6 form-group">
                <?= $form->field($profile, 'middlename')->textInput(['class' => 'form-control']) ?>
            </div>

            <div class="col-md-6 form-group">
                <?= $form->field($profile, 'gender')->dropDownList([
                    0 => '-',
                    1 => _e('Male'),
                    2 => _e('Female'),
                ]) ?>
            </div>

            <div class="col-md-6 form-group">
                <?= $form->field($profile, 'phone')->textInput(['class' => 'form-control']) ?>
            </div>

           
        </div>
    </div>
</div>

<div class="mb-3 full-form-btns">
    <?php
    echo Html::submitButton('<i class="ri-check-line mr-1"></i> ' . _e('Save changes'), ['class' => 'btn btn-primary waves-effect btn-with-icon']); ?>

    <a href="<?= $main_url; ?>" class="btn btn-secondary waves-effect btn-with-icon">
        <i class="ri-arrow-left-line mr-1"></i> <?= _e('Back to profile'); ?>
    </a>
</div>
<?php ActiveForm::end(); ?>
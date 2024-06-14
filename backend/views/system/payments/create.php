<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = _e('New currency');
$this->breadcrumbs[] = ['label' => _e('Currency'), 'url' => $main_url];

$languages = admin_active_langs();

if ($model->isNewRecord) {
    $model->sort = 0;
} ?>

<?php $form = ActiveForm::begin(); ?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <?= $form->field($model, 'currency_name')->textInput(['maxlength' => true, 'required' => 'required', 'placeholder' => _e('Currency name (ex: US dollar)')]); ?>
                </div>
                <div class="form-group">
                    <?= $form->field($model, 'currency_code')->textInput(['maxlength' => true, 'required' => 'required', 'placeholder' => _e('Currency code (ex: USD)')]); ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="form-group">
                    <?= $form->field($model, 'status')->dropDownList([
                        1 => _e('Active'),
                        0 => _e('Disabled')
                    ], ['class' => 'custom-select']); ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'sort')->textInput(['maxlength' => true, 'placeholder' => _e('Sort order')])->input('number'); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mb-3 full-form-btns">
    <?= Html::submitButton('<i class="ri-check-line mr-1"></i> ' . _e('Create currency'), ['class' => 'btn btn-success waves-effect btn-with-icon']) ?>

    <a href="<?= get_previous_url($main_url); ?>" class="btn btn-secondary waves-effect btn-with-icon">
        <i class="ri-arrow-left-line mr-1"></i>
        <?= _e('Back to currencies'); ?>
    </a>
</div>
<?php ActiveForm::end(); ?>
<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Reset password';
$this->breadcrumbs[] = $this->title; ?>
<div class="container-fluid p-0">
    <div class="row no-gutters">
        <div class="col-lg-4">
            <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
                <div class="w-100">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <div>
                                <div class="text-center">
                                    <div>
                                        <a href="<?= Yii::$app->homeUrl; ?>" class="logo">
                                            <img src="<?= $this->imagesUrl('images/logo-dark.png') ?>" height="35" alt="logo">
                                        </a>
                                    </div>

                                    <h4 class="font-size-18 mt-4">Reset Password</h4>
                                    <p>Please choose your new password:</p>
                                </div>

                                <div class="p-2 mt-5">
                                    <?php $form = ActiveForm::begin(['id' => 'reset-password-form']); ?>

                                    <div class="form-group auth-form-group-custom mb-4">
                                        <i class="ri-mail-line auti-custom-input-icon"></i>
                                        <label for="useremail">Email</label>
                                        <?= $form->field($model, 'password')->textInput(['autofocus' => true, 'placeholder' => 'Enter new password']) ?>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <?= Html::submitButton('Send', ['class' => 'btn btn-primary w-md waves-effect waves-light']) ?>
                                    </div>
                                    <?php ActiveForm::end(); ?>

                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="authentication-bg">
                <div class="bg-overlay"></div>
            </div>
        </div>
    </div>
</div>
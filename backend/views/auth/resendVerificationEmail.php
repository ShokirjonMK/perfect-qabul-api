<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Resend verification email';
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

                                    <h4 class="font-size-18 mt-4">Resend verification email</h4>
                                </div>

                                <div class="p-2 mt-5">
                                    <?php $form = ActiveForm::begin(['id' => 'resend-verification-email-form']); ?>

                                    <div class="form-group auth-form-group-custom mb-4">
                                        <i class="ri-mail-line auti-custom-input-icon"></i>
                                        <label for="useremail">Email</label>
                                        <?= $form->field($model, 'email')->textInput(['autofocus' => true, 'placeholder' => 'Enter Email']) ?>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <?= Html::submitButton('Send', ['class' => 'btn btn-primary w-md waves-effect waves-light']) ?>
                                    </div>
                                    <?php ActiveForm::end(); ?>

                                </div>

                                <div class="mt-5 text-center">
                                    <p>Don't have an account ? <a href="<?= \yii\helpers\Url::to('/auth/login') ?>" class="font-weight-medium text-primary"> Log in </a> </p>
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
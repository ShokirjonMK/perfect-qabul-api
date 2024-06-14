<?php

use yii\bootstrap\ActiveForm;

$this->title = _e('Login'); ?>

<div class="container-fluid p-0">
    <div class="row no-gutters">
        <div class="col-lg-4">
            <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
                <div class="w-100">
                    <div class="row justify-content-center">
                        <div class="col-lg-9">
                            <div class="text-center">
                                <div>
                                    <a href="<?= Yii::$app->homeUrl; ?>" class="logo">
                                        <img src="<?= theme_logo_image() ?>" height="100" alt="logo">
                                    </a>
                                </div>
                                <h4 class="font-size-18 mt-4"><?= _e('Welcome to Digital University Portal'); ?></h4>
                                <p class="text-muted"><?= _e('Sign in to continue to Dashboard.'); ?></p>
                            </div>

                            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                            <div class="p-2 mt-5">
                                <form class="form-horizontal">

                                    <div class="form-group auth-form-group-custom mb-4">
                                        <i class="ri-user-2-line auti-custom-input-icon"></i>
                                        <label for="username"><?= _e('Username'); ?></label>
                                        <?= $form->field($model, 'username', ['template' => '{input} {error} {hint}'])
                                            ->textInput([
                                                'autofocus' => true,
                                                'class' => 'input-lg form-control',
                                                'placeholder' => 'Enter username',
                                            ])->label(false); ?>
                                    </div>

                                    <div class="form-group auth-form-group-custom mb-4">
                                        <i class="ri-lock-2-line auti-custom-input-icon"></i>
                                        <label for="userpassword"><?= _e('Password'); ?></label>
                                        <?= $form->field($model, 'password', ['template' => '{input} {error} {hint}'])
                                            ->passwordInput([
                                                'autofocus' => true,
                                                'class' => 'input-lg input-password form-control',
                                                'placeholder' => 'Enter password',
                                            ])->label(false); ?>

                                        <div class="password-eye-icon">
                                            <img src="<?= images_url('eye-icon-2.svg'); ?>" data-show="<?= images_url('eye-icon-2.svg'); ?>" data-hide="<?= images_url('eye-icon-1.svg'); ?>" alt="eye icon">
                                        </div>
                                    </div>

                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" name="LoginForm[rememberMe]" class="custom-control-input" value="1" id="loginform-rememberme">
                                        <label class="custom-control-label" for="loginform-rememberme"><?= _e('Remember me'); ?></label>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <button class="btn btn-primary w-md waves-effect waves-light" type="submit">
                                            <?= _e('Sign in'); ?>
                                        </button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="<?= \yii\helpers\Url::to('/auth/request-password-reset') ?>" class="text-muted">
                                            <i class="mdi mdi-lock mr-1"></i> <?= _e('Forgot your password?'); ?>
                                        </a>
                                    </div>
                                    <div class="mt-4 text-center">
                                        <a href="<?= \yii\helpers\Url::to('/auth/resend-verification-email') ?>" class="text-muted">
                                            <?= _e('Need new verification email?'); ?>
                                        </a>
                                    </div>

                                </form>
                            </div>
                            <?php ActiveForm::end(); ?>

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

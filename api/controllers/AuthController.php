<?php

namespace api\controllers;

use api\components\HttpBearerAuth;
use api\forms\Password;
use api\forms\PasswordReset;
use api\forms\ResendSms;
use api\forms\SignIn;
use Yii;
use base\ResponseStatus;
use api\forms\Login;
use common\models\LoginHistory;
use common\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class AuthController extends ApiController
{

    public function behaviors()
    {
        return  [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['is-phone','sms-number','password' , 'resend-sms','login' , 'new-password' , 'sms-confirm' , 'pass-reset'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'is-phone' => ['post'],
                    'sms-number' => ['post'],
                    'password' => ['post'],
                    'resend-sms' => ['post'],
                    'login' => ['post'],
                ],
            ],
        ];
    }

    public function actionIsPhone() {
        $post = Yii::$app->request->post();
        $model = new SignIn();
        $result = SignIn::signUp($model, $post);
        if ($result['is_ok']) {
            return $this->response(1, _e('SMS notification sent.'), $result['data'], null);
        }
        return $this->response(0, _e('Something went wrong!'), null, $result['errors'], ResponseStatus::OK);
    }

    public function actionSmsNumber() {
        $post = Yii::$app->request->post();
        $result = SignIn::smsNumber($post);
        if ($result['is_ok']) {
            return $this->response(1, _e('SMS code is confirmed.'), $result['data'], null);
        }
        return $this->response(0, _e('Something went wrong!'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
    }

    public function actionPassword() {
        $post = Yii::$app->request->post();
        $model = new Password();
        $result = Password::password($model , $post);
        if ($result['is_ok']) {
            return $this->response(1, _e('SMS code is confirmed.'), $result['data'], null);
        }
        return $this->response(0, _e('Something went wrong!'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
    }

    public function actionResendSms() {
        $post = Yii::$app->request->post();
        $model = new ResendSms();
        $result = ResendSms::resendSms($model , $post);
        if ($result['is_ok']) {
            return $this->response(1, _e('SMS code is confirmed.'), $result['data'], null , ResponseStatus::OK);
        }
        return $this->response(0, _e('Something went wrong!'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
    }

    public function actionLogin() {
        $post = Yii::$app->request->post();
        if (isset($post['is_main'])) {
            if ($post['is_main'] == 1) {
                $result = Login::loginMain(new Login(), $post);
                if ($result['is_ok']) {
                    if (empty($result['data']['role'])) {
                        Login::logout();
                        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
                    }
                    $res = LoginHistory::createItemLogin($result['data']['user_id']);
                    if (!is_array($res)) {
                        return $this->response(1, _e('User successfully logged in.'), $result['data'], null);
                    }
                    return $this->response(1, _e('User successfully logged in.'), $result['data'], _e('Login not saved'));
                } else {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UNAUTHORIZED);
                }
            } elseif ($post['is_main'] == 0) {
                $result = Login::loginStd(new Login(), $post);
                if ($result['is_ok']) {
                    if (empty($result['data']['role'])) {
                        Login::logout();
                        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
                    }
                    return $this->response(1, _e('User successfully logged in.'), $result['data'], null);
                } else {
                    return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UNAUTHORIZED);
                }
            } else {
                return $this->response(0, _e('Something went wrong!'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            return $this->response(0, _e('Something went wrong!'), null, null, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }




    public function actionPassReset()
    {
        $post = Yii::$app->request->post();
        $model = new PasswordReset();
        $result = PasswordReset::resetPass($model , $post);
        if ($result['is_ok']) {
            return $this->response(1, _e('The operation is successful.'), $result['data'], null, ResponseStatus::OK);
        }
        return $this->response(0, _e('Something went wrong!'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
    }

    public function actionSmsConfirm()
    {
        $post = Yii::$app->request->post();
        $result = PasswordReset::smsConfirm($post);
        if ($result['is_ok']) {
            $smsInfo = $result['data'];
            $data = [
                'current_time' => time(),
                'key' => $smsInfo->reset_token,
                'message' => 'SMS code is confirmed.',
            ];
            return $this->response(1, _e('SMS code is confirmed.'), $data, null , ResponseStatus::OK);
        }
        return $this->response(0, _e('Something went wrong!'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
    }

    public function actionNewPassword()
    {
        $post = Yii::$app->request->post();
        $result = PasswordReset::newPassword($post);
        if ($result['is_ok']) {
            return $this->response(1, _e('SMS code is confirmed.'), $result['data'], null , ResponseStatus::OK);
        }
        return $this->response(0, _e('Something went wrong!'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
    }
}
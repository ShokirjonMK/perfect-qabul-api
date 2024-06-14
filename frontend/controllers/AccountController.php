<?php

namespace frontend\controllers;

use base\Container;
use base\FrontendController;
use base\Url;
use frontend\models\AccountModel;

/**
 * Account controller
 */
class AccountController extends FrontendController
{
    /**
     * Before action
     *
     * @param $action
     * @return void
     */
    public function beforeAction($action)
    {
        $this->viewPath = rtrim($this->theme_alias, '/') . '/views/account';
        Container::push('analytics-js', "web_analytics.init('account');");

        return parent::beforeAction($action);
    }

    /**
     * Sign in page
     *
     * @return mixed
     */
    public function actionSignin()
    {
        if (AccountModel::isUserLoggedIn()) {
            $url = Url::profile();
            return $this->redirect($url);
        }

        $this->meta['title'] = _e('Sign in');
        $this->body_class = ['module-page-account-signin'];

        $form_data = array();
        $post_action = input_post('post_action');
        $redirect = input_post('redirect');

        if ($post_action == 'signin') {
            $email = input_post('email');
            $password = input_post('password');
            $remember = input_post('remember');

            $form_data = AccountModel::loginWithEmail($email, $password, $remember);
            $success = array_value($form_data, 'success');

            if ($success && $redirect) {
                return $this->redirect($redirect);
            }
        }

        return $this->render('signin', array(
            'form_data' => $form_data,
        ));
    }

    /**
     * Sign up page
     *
     * @return mixed
     */
    public function actionSignup()
    {
        if (AccountModel::isUserLoggedIn()) {
            $url = Url::profile();
            return $this->redirect($url);
        }

        $this->meta['title'] = _e('Sign up');
        $this->body_class = ['module-page-account-signin'];

        $form_data = array();
        $post_action = input_post('post_action');

        if ($post_action == 'signup') {
            $user = input_post('user');
            $profile = input_post('profile');

            $form_data = AccountModel::register($user, $profile);
        }

        return $this->render('signup', array(
            'form_data' => $form_data,
        ));
    }

    /**
     * Password revocery page
     *
     * @return mixed
     */
    public function actionActivation()
    {
        $this->meta['title'] = _e('Account activation');
        $this->body_class = ['module-page-account-signin'];

        $form_data = array();
        $token = input_get('token');
        $email = input_get('email');
        $post_action = input_post('post_action');

        if ($token) {
            $token_response = AccountModel::activateAccountByToken($email, $token);
            $is_valid_token = array_value($token_response, 'success');

            return $this->render('activation', array(
                'token' => $token,
                'email' => $email,
                'is_valid_token' => $is_valid_token,
                'token_response' => $token_response,
            ));
        }

        if ($post_action == 'request_verification') {
            $email = input_post('email');
            $form_data = AccountModel::requestVerification($email);
        }

        return $this->render('activation-from', array(
            'form_data' => $form_data,
        ));
    }

    /**
     * Password revocery page
     *
     * @return mixed
     */
    public function actionRecovery()
    {
        $this->meta['title'] = _e('Password recovery');
        $this->body_class = ['module-page-account-signin'];

        $form_data = array();
        $token = input_get('token');
        $email = input_get('email');
        $post_action = input_post('post_action');

        if ($token) {
            $token_response = AccountModel::checkPasswordToken($email, $token);
            $is_valid_token = array_value($token_response, 'success');

            if ($post_action == 'password_change') {
                $email = input_post('email');
                $token = input_post('token');
                $password = input_post('password');
                $confirm_password = input_post('confirm_password');

                $form_data = AccountModel::changePasswordByToken($email, $token, $password, $confirm_password);
            }

            return $this->render('password-change', array(
                'token' => $token,
                'email' => $email,
                'is_valid_token' => $is_valid_token,
                'token_response' => $token_response,
                'form_data' => $form_data,
            ));
        }

        if ($post_action == 'password_recovery') {
            $email = input_post('email');
            $form_data = AccountModel::resetPassword($email);
        }

        return $this->render('password-recovery', array(
            'form_data' => $form_data,
        ));
    }
}

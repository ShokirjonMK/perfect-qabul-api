<?php

namespace backend\controllers;

use backend\models\PasswordResetRequestForm;
use backend\models\ResendVerificationEmailForm;
use backend\models\ResetPasswordForm;
use backend\models\VerifyEmailForm;
use common\models\LoginForm;
use common\models\UsersSession;
use Yii;
use yii\base\InvalidArgumentException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

/**
 * Auth controller
 */
class AuthController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $app = Yii::$app;

        if (!$app->user->isGuest) {
            return $this->goHome();
        }

        $app->user->loginUrl = ['/auth/login'];
        return $this->redirect($app->user->loginUrl)->send();
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $app = Yii::$app;
        $session = $app->session;
        $this->layout = 'login';

        if (!$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();

        if ($model->load($app->request->post()) && $model->adminLogin()) {
            $session->set('underconstruction_check', 'pass');
            $redirect = $app->request->get('redirect');
            UsersSession::setLog();

            if ($redirect) {
                return $this->redirect($app->homeUrl . $redirect)->send();
            }

            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'login';
        $model = new PasswordResetRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', _e('Check your email for further instructions.'));
                return $this->redirect('/auth/login');
            } else {
                Yii::$app->session->setFlash('error', _e('Sorry, we are unable to reset password for the provided email address.'));
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', _e('New password saved.'));

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }

    /**
     * Verify email address
     *
     * @param string $token
     * @throws BadRequestHttpException
     * @return yii\web\Response
     */
    public function actionVerifyEmail($token)
    {
        try {
            $model = new VerifyEmailForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($user = $model->verifyEmail()) {
            if (Yii::$app->user->login($user)) {
                Yii::$app->session->setFlash('success', _e('Your email has been confirmed!'));
                return $this->goHome();
            }
        }

        Yii::$app->session->setFlash('error', _e('Sorry, we are unable to verify your account with provided token.'));
        return $this->goHome();
    }

    /**
     * Resend verification email
     *
     * @return mixed
     */
    public function actionResendVerificationEmail()
    {
        $this->layout = 'login';
        $model = new ResendVerificationEmailForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', _e('Check your email for further instructions.'));
                return $this->goHome();
            }
            Yii::$app->session->setFlash('error', _e('Sorry, we are unable to resend verification email for the provided email address.'));
        }

        return $this->render('resendVerificationEmail', [
            'model' => $model,
        ]);
    }
}

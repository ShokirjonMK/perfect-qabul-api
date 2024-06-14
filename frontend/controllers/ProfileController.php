<?php

namespace frontend\controllers;

use base\Container;
use base\FrontendController;
use base\libs\Utils;
use base\Url;
use common\models\CompanyInfo;
use frontend\models\AccountModel;
use frontend\models\Company;
use frontend\models\OrderModel;
use frontend\models\ProfileModel;
use Yii;

/**
 * Profile controller
 */
class ProfileController extends FrontendController
{

    /**
     * Before action
     *
     * @param $action
     * @return void
     */

    public function beforeAction($action)
    {
        $this->viewPath = rtrim($this->theme_alias, '/') . '/views/profile';
        Container::push('analytics-js', "web_analytics.init('profile');");

        if (AccountModel::isUserLoggedIn()) {
            return parent::beforeAction($action);
        }

        return $this->redirect(site_url());
    }

    /**
     * Profile page
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $this->meta['title'] = _e('Profile');
        $this->body_class = ['module-page-profile'];

        $user_id = current_user_id();
        $current_user = current_user();
        $current_user_profile = current_user_profile();
        $company = Company::getMyCompany();

        return $this->render('index', array(
            'company' => $company,
            'current_user' => $current_user,
            'current_user_id' => $user_id,
            'current_user_profile' => $current_user_profile,
        ));
    }

    /**
     * Settings page
     *
     * @return mixed
     */
    public function actionSettings()
    {
        $this->meta['title'] = _e('Profile Settings');
        $this->body_class = ['module-page-profile-settings'];

        $user_id = current_user_id();
        $current_user = current_user();
        $current_user_profile = current_user_profile();

        $form_data = array();
        $post_action = input_post('post_action');

        if ($post_action == 'profile_update') {
            $form_data = ProfileModel::updateProfile();
        }

        return $this->render('settings', array(
            'form_data' => $form_data,
            'current_user' => $current_user,
            'current_user_id' => $user_id,
            'current_user_profile' => $current_user_profile,
        ));
    }

    /**
     * Password page
     *
     * @return mixed
     */
    public function actionPassword()
    {
        $this->meta['title'] = _e('Change password');
        $this->body_class = ['module-page-profile-password-change'];

        $user_id = current_user_id();
        $current_user = current_user();
        $current_user_profile = current_user_profile();

        $form_data = array();
        $post_action = input_post('post_action');

        if ($post_action == 'password_change') {
            $form_data = ProfileModel::passwordChange();
        }

        return $this->render('password-change', array(
            'form_data' => $form_data,
            'current_user' => $current_user,
            'current_user_id' => $user_id,
            'current_user_profile' => $current_user_profile,
        ));
    }

    /**
     * Settings page
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->redirect(Url::account('signin'));
    }
}

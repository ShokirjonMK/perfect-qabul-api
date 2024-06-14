<?php

namespace backend\controllers;

use backend\models\User;
use base\BackendController;
use base\Container;
use common\models\Password;
use common\models\Profile;
use Yii;
use yii\helpers\Url;

/**
 * Profile controller
 */
class ProfileController extends BackendController
{
    public $url = '/profile';

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $user = current_user();
        $profile = current_user_profile();

        $users_url = Url::to('/users');
        $main_url = Url::to([$this->url]);

        $tabs = array(
            ['link' => 'profile', 'name' => _e('Profile'), 'icon' => 'ri-information-line'],
            ['link' => 'activity', 'name' => _e('Activity'), 'icon' => 'ri-file-paper-line'],
            ['link' => 'sessions', 'name' => _e('Sessions'), 'icon' => 'ri-bar-chart-horizontal-line'],
        );

        $this->registerCss(array(
            'dist/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
            'dist/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css',
            'dist/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css',
            'dist/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css',
        ));

        $this->registerJs(array(
            'dist/libs/datatables.net/js/jquery.dataTables.min.js',
            'dist/libs/datatables.net-buttons/js/dataTables.buttons.min.js',
            'dist/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
            'dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
            'dist/js/pages/datatables.init.js',
        ));

        return $this->render('index', array(
            'main_url' => $main_url,
            'users_url' => $users_url,
            'user' => $user,
            'profile' => $profile,
            'tabs' => $tabs,
        ));
    }

    /**
     * Displays settings
     *
     * @return string
     */
    public function actionSettings()
    {
        $post_item = Yii::$app->request->post();
        $user_id = current_user_id();

        $user = User::findOne($user_id);
        $profile = current_user_profile();

        $users_url = Url::to('/users');
        $main_url = Url::to([$this->url]);

        if ($post_item && $profile->load($post_item)) {
            $user->updateProfile($user, $profile, $post_item);

            Yii::$app->session->setFlash('success-alert', _e('Profile has been successfully updated!'));
            return $this->refresh();
        }

        return $this->render('settings', array(
            'main_url' => $main_url,
            'users_url' => $users_url,
            'user' => $user,
            'profile' => $profile,
        ));
    }

    /**
     * Displays change password
     *
     * @return string
     */
    public function actionPassword()
    {
        $user = current_user();
        $user_id = current_user_id();
        $profile = current_user_profile();

        $users_url = Url::to('/users');
        $main_url = Url::to([$this->url]);

        $model = new Password();
        $post_item = Yii::$app->request->post();

        if ($post_item && $model->load($post_item)) {
            $set_pass = $model->setPassword($user_id);

            if ($set_pass['success']) {
                // Set log
                set_log('admin', [
                    'res_id' => $user->id,
                    'type' => 'password',
                    'action' => 'update',
                    'data' => '',
                ]);

                Yii::$app->session->setFlash('success-alert', $set_pass['message']);
                return $this->refresh();
            } else {
                $model->old_password = '';
                Yii::$app->session->setFlash('error-alert', $set_pass['message']);
            }
        }

        return $this->render('password', array(
            'main_url' => $main_url,
            'users_url' => $users_url,
            'user' => $user,
            'profile' => $profile,
            'model' => $model,
        ));
    }
}

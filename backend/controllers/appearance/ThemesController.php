<?php

namespace backend\controllers\appearance;

use backend\models\System;
use base\BackendController;
use Yii;

/**
 * Themes controller
 */
class ThemesController extends BackendController
{
    /**
     * Displays main page
     *
     * @return string
     */
    public function actionIndex()
    {
        $themes = System::getThemes();
        $ajax_action = Yii::$app->request->post('ajax_action');

        if ($ajax_action) {
            $output = json_output();
            $theme_key = Yii::$app->request->post('theme_key');

            if (is_string($theme_key) && $theme_key) {
                $check_theme = false;

                if (isset($themes['current']['theme_key']) && $themes['current']['theme_key'] == $theme_key) {
                    $check_theme = true;
                } elseif (isset($themes['themes'][$theme_key])) {
                    $check_theme = true;
                } else {
                    $output['message'] = _e('Theme is not exists!');
                }

                if ($check_theme) {
                    $output = json_output('success');
                    $output['message'] = '';

                    System::updateSetting('site_theme', $theme_key, true);
                    create_temp_for('settings');
                }
            }

            echo json_encode($output);
            exit();
        }

        return $this->render('index', array(
            'themes' => $themes,
        ));
    }
}

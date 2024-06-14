<?php

namespace backend\controllers\system;

use backend\models\System;
use base\BackendController;
use Yii;
use yii\helpers\Url;

/**
 * Settings controller
 */
class SettingsController extends BackendController
{
    public $url = '/system/settings';

    /**
     * Settings group
     *
     * @param array
     */
    public function settings_group()
    {
        $array = array(
            'site' => array(
                'name' => _e('Site'),
                'active' => false,
            ),
            'contacts' => array(
                'name' => _e('Contacts'),
                'active' => false,
            ),
            'general' => array(
                'name' => _e('General'),
                'active' => false,
            ),
            'social' => array(
                'name' => _e('Social media'),
                'active' => false,
            ),
            'seo' => array(
                'name' => _e('SEO'),
                'active' => false,
            ),
        );

        return $array;
    }

    /**
     * Displays main page
     *
     * @return string
     */
    public function actionIndex()
    {
        $main_url = Url::to([$this->url]);
        $active_lang = input_get('lang');
        $sgroup_key = input_get('group', 'site');

        $settings_group = $this->settings_group();
        $settings_form = input_post('settings_form');

        if ($settings_form == 'save') {
            $log_data = array();
            $settings_post = Yii::$app->request->post('settings');
            $settings_translation = Yii::$app->request->post('settings_translation');

            // Update settings
            if ($settings_post) {
                foreach ($settings_post as $key => $value) {
                    $action = System::updateSetting($key, $value);

                    if ($action['updated']) {
                        $log_data['global'][$key]['value'] = $action['new_value'];
                        $log_data['global'][$key]['old_value'] = $action['old_value'];
                    }
                }
            }

            // Update translations
            if ($settings_translation) {
                foreach ($settings_translation as $lang => $array) {
                    if ($array) {
                        foreach ($array as $key => $value) {
                            $action = System::updateSettingsTranslation($key, $lang, $value);

                            if ($action['updated']) {
                                $log_data[$lang][$key]['value'] = $action['new_value'];
                                $log_data[$lang][$key]['old_value'] = $action['old_value'];
                            }
                        }
                    }
                }
            }

            // Set log
            if ($log_data) {
                set_log('admin', [
                    'type' => 'settings',
                    'action' => 'update',
                    'data' => json_encode($log_data),
                ]);
            }

            // Create temp
            create_temp_for('settings');
        }

        if (isset($settings_group[$sgroup_key])) {
            $settings_group[$sgroup_key]['active'] = true;
        }

        $model = System::getSettings($sgroup_key, ['language' => $active_lang])->all();

        return $this->render('index', array(
            'main_url' => $main_url,
            'model' => $model,
            'settings_group' => $settings_group,
        ));
    }
}

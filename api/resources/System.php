<?php

namespace api\resources;

use backend\models\System as BackendSystem;
use common\models\Languages;
use common\models\Settings;
use Yii;

class System
{
    /**
     * Get one language
     *
     * @return array
     */
    public static function getOneLanguage()
    {
        $where = array();
        $output = api_json_output();
        $output['message'] = _e('Results not found.');

        $id = (int) input_get('id');
        $lang = input_get('lang');
        $locale = input_get('locale');

        if (is_numeric($id) && $id > 0) {
            $where = array('id' => $id);
        } elseif (is_string($locale) && $locale) {
            $where = array('locale' => $locale);
        } elseif (is_string($lang) && $lang) {
            $where = array('lang_code' => $lang);
        }

        if ($where) {
            $data = Languages::find()
                ->where(['status' => 1])
                ->andWhere($where)
                ->asArray()
                ->one();

            if ($data) {
                $output = api_json_output('success');
                $output['data'] = $data;
            }
        }

        return $output;
    }

    /**
     * Get default language
     *
     * @return array
     */
    public static function getDefaultLanguage()
    {
        $output = api_json_output();
        $output['message'] = _e('Results not found.');
        $site_language = BackendSystem::getSetting('site_language');

        if ($site_language) {
            $data = Languages::find()
                ->where(['status' => 1])
                ->andWhere(['lang_code' => $site_language])
                ->asArray()
                ->one();

            if ($data) {
                $output = api_json_output('success');
                $output['data'] = $data;
            }
        }

        return $output;
    }

    /**
     * Get all languages
     *
     * @return array
     */
    public static function getAllLanguages()
    {
        $status = input_get('status', null);
        $default = input_get('default', null);
        $sort = input_get('sort');
        $sortby = input_get('sortby');

        // Make query
        $query = Languages::find();

        if (!is_null($status) && is_numeric($status)) {
            $query->where(['status' => $status]);
        } else {
            $query->where(['status' => 1]);
        }

        if (is_numeric($default)) {
            $query->andWhere(['default' => $default]);
        }

        if ($sortby && $sort) {
            $query->orderBy(api_sortby($sortby,  $sort, 'settings'));
        }

        // Get data
        $data = $query->asArray()->all();

        if ($data) {
            $output = api_json_output('success');
            $output['data'] = $data;
        } else {
            $output = api_json_output();
            $output['message'] = _e('Results not found.');
        }

        return $output;
    }

    /**
     * Get one setting
     *
     * @return array
     */
    public static function getOneSetting()
    {
        $where = array();
        $output = api_json_output();
        $output['message'] = _e('Results not found.');

        $settings_id = (int) input_get('id');
        $lang = input_get('lang');
        $settings_key = input_get('key');

        if (is_numeric($settings_id) && $settings_id > 0) {
            $where = array('id' => $settings_id);
        } elseif (is_numeric($settings_key) && $settings_key > 0) {
            $where = array('settings_key' => $settings_key);
        } elseif (is_string($settings_key) && $settings_key) {
            $where = array('settings_key' => $settings_key);
        }

        if ($where) {
            $data = BackendSystem::getSetting($where, $lang);

            if ($data) {
                $output = api_json_output('success');
                $output['data'] = $data;
            }
        }

        return $output;
    }

    /**
     * Get all settings
     *
     * @return array
     */
    public static function getAllSettings()
    {
        $group = input_get('group');
        $lang = input_get('lang');
        $sort = input_get('sort');
        $sortby = input_get('sortby');

        // Make query
        if ($lang) {
            $query = Settings::find()
                ->select(['settings.*', 'translation.settings_value'])
                ->join(
                    'LEFT JOIN',
                    'settings_translation translation',
                    'translation.settings_key = settings.settings_key AND translation.language = "' . $lang . '"'
                );
        } else {
            $query = Settings::find()->where(['status' => 1]);
        }

        if (is_numeric($group) && $group > 0) {
            $query->andWhere(['settings.settings_group' => $group]);
        } elseif (is_string($group) && $group) {
            $query->andWhere(['settings.settings_group' => $group]);
        }

        if ($sortby && $sort) {
            $query->orderBy(api_sortby($sortby,  $sort, 'settings'));
        }

        // Get data
        $data = $query->asArray()->all();

        if ($data) {
            $output = api_json_output('success');
            $output['data'] = $data;
        } else {
            $output = api_json_output();
            $output['message'] = _e('Results not found.');
        }

        return $output;
    }
}
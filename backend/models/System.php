<?php

namespace backend\models;

use common\models\CurrencyList;
use common\models\Settings;
use common\models\SettingsTranslation;
use Yii;

class System
{
    /**
     * Get settings
     *
     * @param string $group
     * @param array $args
     * @return object
     */
    public static function getSettings($group = '', $args = array())
    {
        $where = array_value($args, 'where');
        $orderBy = array_value($args, 'order_by', ['settings.sort' => SORT_ASC]);
        $language = array_value($args, 'language', '');
        $lang_key = clean_str($language);

        $query = Settings::find()
            ->select(['settings.*', 'translation.settings_value as translation'])
            ->join(
                'LEFT JOIN',
                'settings_translation translation',
                'translation.settings_key = settings.settings_key AND translation.language = "' . $lang_key . '"'
            );

        if (is_string($group) && !empty($group)) {
            $query->where(['settings.settings_group' => $group]);
        }

        if (is_array($where) && $where) {
            $query->andWhere($where);
        }

        if (is_array($orderBy) && $orderBy) {
            $query->orderBy($orderBy);
        }

        return $query;
    }

    /**
     * Get setting
     *
     * @param array|string $where
     * @param string $language
     * @return object
     */
    public static function getSetting($where = array(), $language = false)
    {
        $where_query = array();
        $where_translation = array();
        $lang_key = clean_str($language);

        $query = Settings::find();

        if (is_string($where) && $where) {
            $where_query = ['settings_key' => $where];
        } elseif (is_array($where) && $where) {
            $where_query = $where;
        }

        $output = $query->where($where_query)->asArray()->one();

        if ($lang_key) {
            $where_translation = $where_query;

            $queryTranslation = SettingsTranslation::find();
            $queryTranslation->where($where_translation);
            $queryTranslation->andWhere(['language' => $lang_key]);
            $translation = $queryTranslation->asArray()->one();

            if ($translation && $output) {
                $output['settings_value'] = $translation['settings_value'];
            }
        }

        return $output;
    }

    /**
     * Get setting value
     *
     * @param array|string $where
     * @param mixed $default
     * @param string $language
     * @return object
     */
    public static function getSettingValue($where = array(), $default = '', $language = '')
    {
        $output = self::getSetting($where, $language);
        return $output ? $output['settings_value'] : $default;
    }

    /**
     * Update settings item
     *
     * @param string $key
     * @param mixed $value
     * @param boolean $create_item
     * @return void
     */
    public static function updateSetting($key, $value, $create_item = false)
    {
        $output['updated'] = false;
        $output['new_value'] = false;
        $output['old_value'] = false;

        $model = Settings::find()->where(['settings_key' => $key])->one();

        if ($model && $model->settings_value != $value) {
            $output['updated'] = true;
            $output['new_value'] = $value;
            $output['old_value'] = $model->settings_value;

            $model->settings_value = $value;
            $model->updated_on = date('Y-m-d H:i:s');
            $model->save();
        } elseif ($create_item && !$model) {
            $model = new Settings();

            $output['updated'] = true;
            $output['new_value'] = $value;
            $output['old_value'] = $model->settings_value;

            $model->settings_key = $key;
            $model->settings_value = $value;
            $model->updated_on = date('Y-m-d H:i:s');
            $model->save();
        }

        return $output;
    }

    /**
     * Update settings translation
     *
     * @param string $key
     * @param string $language
     * @param mixed $value
     * @return void
     */
    public static function updateSettingsTranslation($key, $language, $value)
    {
        $output['updated'] = false;
        $output['new_value'] = false;
        $output['old_value'] = false;

        $update = true;
        $setting = Settings::find()->where(['settings_key' => $key])->one();
        $model = SettingsTranslation::find()->where(['settings_key' => $key, 'language' => $language])->one();

        if (!$model && $setting && $setting->settings_value == $value) {
            $update = false;
        }

        if ($update) {
            $model = SettingsTranslation::find()->where(['settings_key' => $key, 'language' => $language])->one();
            $output['updated'] = true;
            $output['new_value'] = $value;

            if ($model) {
                $output['old_value'] = $model->settings_value;

                $model->settings_value = $value;
                $model->updated_on = date('Y-m-d H:i:s');
                $model->save();
            } else {
                $output['old_value'] = '';

                $model = new SettingsTranslation();
                $model->language = $language;
                $model->settings_key = $key;
                $model->settings_value = $value;
                $model->updated_on = date('Y-m-d H:i:s');
                $model->save();
            }
        }

        return $output;
    }

    /**
     * Update currency status
     *
     * @param [type] $id
     * @param integer $status
     * @return boolean
     */
    public function updateCurrencyStatus($id, $status = 0)
    {
        $item = CurrencyList::findOne(['id' => $id]);

        if ($item && is_numeric($status)) {
            $item->status = $status;
            $item->update(false);

            return true;
        }

        return false;
    }

    /**
     * Get themes
     *
     * @return void
     */
    public static function getThemes()
    {
        $output['current'] = array();
        $output['themes'] = array();

        $theme_path = THEMES_PATH;
        $current_theme = get_site_theme();

        if (is_dir($theme_path)) {
            $dirs = glob($theme_path . '*', GLOB_ONLYDIR);

            if ($dirs) {
                foreach ($dirs as $dir) {
                    $set = array();
                    $theme_dir = $dir . DS;
                    $theme_config = $theme_dir . 'theme-config.php';
                    $screenshots = array(
                        'screenshot.jpg',
                        'screenshot.JPG',
                        'screenshot.png',
                        'screenshot.PNG',
                    );

                    if (is_file($theme_config)) {
                        $config = include $theme_config;
                        $screenshot_file = BACKEND_PATH . 'assets/images/theme-screenshot.jpg';

                        if (is_array($config) && $config) {
                            $theme_key = basename($theme_dir);
                            $config['dir'] = $theme_dir;
                            $config['name'] = array_value($config, 'name', _e('No theme name'));
                            $config['description'] = array_value($config, 'description', '');
                            $config['version'] = array_value($config, 'version', '1.0.0');
                            $config['author'] = array_value($config, 'author', 'SokirjonMK');
                            $config['author_url'] = array_value($config, 'author_url', 'https://t.me/SokirjonMK');

                            $set = $config;
                            $set['theme_key'] = $theme_key;
                            $set['screenshot'] = '';

                            foreach ($screenshots as $screenshot) {
                                if (is_file($theme_dir . $screenshot)) {
                                    $screenshot_file = $theme_dir . $screenshot;
                                }
                            }

                            if (is_file($screenshot_file)) {
                                $screenshot_type = pathinfo($screenshot_file, PATHINFO_EXTENSION);
                                $screenshot_data = file_get_contents($screenshot_file);
                                $base64 = 'data:image/' . $screenshot_type . ';base64,' . base64_encode($screenshot_data);
                                $set['screenshot'] = $base64;
                            }

                            if ($theme_key == $current_theme) {
                                $output['current'] = $set;
                            } else {
                                $output['themes'][$theme_key] = $set;
                            }
                        }
                    }
                }
            }
        }

        return $output;
    }
}

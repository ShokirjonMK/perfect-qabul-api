<?php

namespace backend\models;

use base\Container;
use common\models\Languages;
use Yii;

class Language extends Languages
{
    public $defaultLang = 'en';
    public $defaultLocale = 'en_GB';

    /**
     * Get languages list
     *
     * @param array $where
     * @param string $order_by
     * @return array
     */
    public function getLanguagesList($where = array(), $order_by = 'name')
    {
        $array = array();
        $results = $this->getAll($where, $order_by);

        if ($results) {
            foreach ($results as $result) {
                $array[$result['lang_code']] = $result;
            }
        }

        // Set to container
        Container::push('languages_list', $array);

        return $results;
    }

    /**
     * Get current language
     *
     * @return array
     */
    public function getCurrentLanguage()
    {
        $request = Yii::$app->request;
        $cookies = $request->cookies;

        $lang_key = $request->get('language');
        $cookie_key = $cookies->getValue('panel_language');

        if ($lang_key) {
            $lang_code = $lang_key;
        } elseif ($cookie_key) {
            $lang_code = $cookie_key;
        } else {
            $lang_code = $this->defaultLang;
        }

        $language = array();
        $result = $this->getOne(array(
            'lang_code' => $lang_code,
        ));

        if ($result) {
            $language = $result;
        } else {
            $language = $this->getOne(array(
                'lang_code' => 'en',
            ));
        }

        // Set to container
        Container::$language = $language;

        return $language;
    }

    /**
     * Get content language
     *
     * @return array
     */
    public function getContentLanguage()
    {
        $request = Yii::$app->request;
        $cookies = $request->cookies;

        $lang_key = $request->get('cl');
        $cookie_key = $cookies->getValue('panel_content_lang');

        if ($lang_key) {
            $lang_code = $lang_key;
        } elseif ($cookie_key) {
            $lang_code = $cookie_key;
        } else {
            $lang_code = $this->defaultLang;
        }

        $where = array(
            'lang_code' => $lang_code,
            'status' => 1,
        );

        $language = array();
        $result = $this->getOne($where);

        if ($result) {
            $language = $result;
        } else {
            $language = $this->getOne(array(
                'status' => 1
            ));
        }

        // Set to container
        Container::push('content_language', $language);

        return $language;
    }

    /**
     * Check and set language key
     *
     * @return void
     */
    public function checkAndSet()
    {
        $language = array();
        $cookies = Yii::$app->response->cookies;

        $current_lang_set_cookie = false;
        $current_lang = Container::$language;
        $current_lang_cookie = $cookies->getValue('panel_language');

        $content_lang_set_cookie = true;
        $content_lang_key = $this->defaultLang;

        $content_lang = Container::get('content_language');
        $content_lang_cookie = $cookies->getValue('panel_content_lang');

        if ($current_lang) {
            $language = $current_lang;
            $current_lang_set_cookie = true;
        } elseif ($current_lang_cookie) {
            $language = $current_lang_cookie;
            $current_lang_set_cookie = false;
        }

        if ($language) {
            if ($current_lang_set_cookie) {
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'panel_language',
                    'value' => $language['lang_code'],
                    'expire' => time() + (60 * 60 * 24 * 365),
                ]));

                $cookies->add(new \yii\web\Cookie([
                    'name' => 'panel_lang_locale',
                    'value' => $language['locale'],
                    'expire' => time() + (60 * 60 * 24 * 365),
                ]));
            }

            Yii::$app->language = $language['locale'];
        } else {
            Yii::$app->language = $this->defaultLocale;
        }

        if ($content_lang) {
            $content_lang_set_cookie = true;
            $content_lang_key = $content_lang;
        } elseif ($content_lang_cookie) {
            $content_lang_set_cookie = false;
            $content_lang_key = $content_lang_cookie;
        }

        if ($content_lang_set_cookie) {
            $cookies->add(new \yii\web\Cookie([
                'name' => 'panel_content_lang',
                'value' => $content_lang_key,
                'expire' => time() + (60 * 60 * 24 * 365),
            ]));
        }
    }

    /**
     * Update language item status
     *
     * @param [type] $id
     * @param integer $status
     * @return mixed
     */
    public function updateStatus($id, $status = 0)
    {
        $item = Language::findOne(['id' => $id]);

        if ($item && is_numeric($status)) {
            $item->status = $status;
            $item->update(false);

            return $item;
        }

        return false;
    }
}

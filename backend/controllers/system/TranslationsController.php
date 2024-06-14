<?php

namespace backend\controllers\system;

use base\BackendController;
use common\models\Languages;
use common\models\Translations;
use Yii;
use yii\helpers\Url;

class TranslationsController extends BackendController
{
    public $url = '/system/translations';

    /**
     * Display index
     *
     * @return void
     */
    public function actionIndex()
    {
        $main_url = Url::to([$this->url]);
        $post_action = input_post('action');

        $translations = Translations::listPath();
        $models = Translations::find()->where(['lang_key' => 'core'])->all();
        
        if ($post_action == 'scan-translations') {
            $path = input_post('path');

            $translationClass = new Translations();
            $output = $translationClass->scanMessages($path);

            echo json_encode($output);
            exit();
        }

        $this->registerCss(array(
            'theme/components/translations/style.css',
        ));

        $this->registerJs(array(
            'theme/components/translations/init.js',
        ));

        return $this->render('index', array(
            'main_url' => $main_url,
            'models' => $models,
            'translations' => $translations,
        ));
    }

    /**
     * Display load messages
     *
     * @return void
     */
    public function actionScanTranslations()
    {
        $path_key = input_get('id');
        $main_url = Url::to([$this->url]);

        $translations = Translations::getPathData($path_key);

        if (!$translations) {
            return $this->render('error', array(
                'main_url' => $main_url,
            ));
        }

        $this->registerCss(array(
            'theme/components/translations/style.css',
        ));

        $this->registerJs(array(
            'theme/components/translations/init.js',
        ));

        return $this->render('scan-translations', array(
            'main_url' => $main_url,
            'path_key' => $path_key,
            'translations' => $translations,
        ));
    }

    /**
     * Display list translation languages
     *
     * @return void
     */
    public function actionListLanguages()
    {
        $path_key = input_get('id');
        $main_url = Url::to([$this->url]);

        $translations = Translations::getPathData($path_key);

        if (!$translations) {
            return $this->render('error', array(
                'main_url' => $main_url,
            ));
        }

        $model = Translations::find()->where(['path_key' => $path_key, 'lang_key' => 'core'])->one();
        $languages = Languages::find()->where(['!=', 'lang_code', 'en'])->orderBy(['name' => SORT_ASC])->all();
        $translations_list = Translations::find()->where(['path_key' => $path_key])->andWhere(['!=', 'lang_key', 'core'])->all();

        if (!$model) {
            return $this->redirect($main_url . '/scan-translations?id=' . $path_key);
        }

        $this->registerCss(array(
            'theme/components/translations/style.css',
        ));

        $this->registerJs(array(
            'theme/components/translations/init.js',
        ));

        return $this->render('list-languages', array(
            'main_url' => $main_url,
            'path_key' => $path_key,
            'model' => $model,
            'languages' => $languages,
            'translations_list' => $translations_list,
            'translations' => $translations,
        ));
    }

    /**
     * Display edit translations
     *
     * @return void
     */
    public function actionEdit()
    {
        $path_key = input_get('id');
        $lang_key = input_get('lang');
        $main_url = Url::to([$this->url]);
        
        $language = Languages::find()->where(['lang_code' => $lang_key])->one();
        $system_translations = Translations::getPathData($path_key);

        if (!$system_translations || !$language) {
            return $this->render('error', array(
                'main_url' => $main_url,
            ));
        }

        $model = Translations::find()->where(['path_key' => $path_key, 'lang_key' => 'core'])->one();
        $lang_model = Translations::find()->where(['path_key' => $path_key, 'lang_key' => $lang_key])->one();

        if (!$model) {
            return $this->redirect($main_url . '/scan-translations?id=' . $path_key);
        }

        $post_item = Yii::$app->request->post();

        if ($post_item) {
            $_translations = Yii::$app->request->post('translation');
            $output = Translations::saveAction($model, $_translations, $path_key, $language);

            $output_error = array_value($output, 'error');
            $flash_message = array_value($output, 'message');

            if ($output_error) {
                $flash_type = 'error-alert';
            } else {
                $flash_type = 'success-alert';
            }

            Yii::$app->session->setFlash($flash_type, $flash_message);
            return $this->refresh();
        }

        $this->registerCss(array(
            'theme/components/translations/style.css',
            'dist/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
            'dist/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css',
            'dist/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css',
            'dist/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css',
        ));

        $this->registerJs(array(
            'theme/components/translations/init.js',
            'dist/libs/datatables.net/js/jquery.dataTables.min.js',
            'dist/libs/datatables.net-buttons/js/dataTables.buttons.min.js',
            'dist/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
            'dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
            'dist/js/pages/datatables.init.js',
        ));

        return $this->render('edit', array(
            'main_url' => $main_url,
            'lang_key' => $lang_key,
            'path_key' => $path_key,
            'model' => $model,
            'lang_model' => $lang_model,
            'language' => $language,
            'system_translations' => $system_translations,
        ));
    }
}

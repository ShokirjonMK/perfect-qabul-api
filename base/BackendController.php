<?php

namespace base;

use backend\models\Language;
use Yii;
use yii\web\Controller;

/**
 * Backend controller
 */
class BackendController extends Controller
{
    private $login_url = '/auth/login';
    public $selected_language = 'en';

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        // Init container
        $container = new Container();
        $container::$prefix = 'cp';
        $container::$context = 'backend';

        // Init language
        $language = new Language();
        $language->defaultLang = 'ru';
        $language->defaultLocale = 'ru_RU';

        $language->getLanguagesList(['status' => 1]);
        $language->getCurrentLanguage();
        $language->getContentLanguage();
        $language->checkAndSet();

        // Set admin current lang
        $this->selected_language = array_value(admin_current_lang(), 'lang_code', 'en');
    }

    /**
     * Before action
     *
     * @param $action
     * @return void
     */
    public function beforeAction($action)
    {
        $app = Yii::$app;
        $user = $app->user;
        $redirect_to_login = true;

        if (!$user->isGuest) {
            $roles = current_user_roles();

            if ($roles && isset($roles['admin'])) {
                $redirect_to_login = false;
            } else {
                $user->logout(true);
                return $this->redirect($app->params['site_url'])->send();
            }
        }

        if ($redirect_to_login) {
            $current_url = trim($app->request->url, '/');

            if (!empty($current_url)) {
                $user->loginUrl = [$this->login_url, 'redirect' => $current_url];
            } else {
                $user->loginUrl = [$this->login_url];
            }

            return $this->redirect($user->loginUrl)->send();
        } else {
            return parent::beforeAction($action);
        }
    }

    /**
     * Register JS file or files
     *
     * @param string $file
     * @return void
     */
    public function registerJs($file)
    {
        if ($file) {
            $ver = APP_VERSION;
            $bundle = \backend\assets\AppAsset::register(\Yii::$app->view);

            if (is_array($file)) {
                foreach ($file as $fi) {
                    if (strpos($fi, '.js?') !== false) {
                        $bundle->js[] = $fi;
                    } else {
                        $bundle->js[] = $fi . "?ver={$ver}";
                    }
                }
            } else {
                if (strpos($file, '.js?') !== false) {
                    $bundle->js[] = $file . "?ver={$ver}";
                } else {
                    $bundle->js[] = $file . "?ver={$ver}";
                }
            }
        }
    }

    /**
     * Register CSS file or files
     *
     * @param string $file
     * @return void
     */
    public function registerCss($file)
    {
        if ($file) {
            $ver = APP_VERSION;
            $bundle = \backend\assets\AppAsset::register(\Yii::$app->view);

            if (is_array($file)) {
                foreach ($file as $fi) {
                    if (strpos($fi, '.css?') !== false) {
                        $bundle->css[] = $fi;
                    } else {
                        $bundle->css[] = $fi . "?ver={$ver}";
                    }
                }
            } else {
                if (strpos($file, '.css?') !== false) {
                    $bundle->css[] = $file . "?ver={$ver}";
                } else {
                    $bundle->css[] = $file . "?ver={$ver}";
                }
            }
        }
    }

    /**
     * Display error page
     *
     * @return string
     */
    public function errorPage()
    {
        $this->viewPath = '@backend/views/site';
        return $this->render('error404');
    }
}

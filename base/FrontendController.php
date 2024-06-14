<?php

namespace base;

use common\models\Analytics;
use Yii;
use yii\web\Controller;
use yii\web\Cookie;

/**
 * Frontend controller
 */
class FrontendController extends Controller
{
    public $data;
    public $theme_alias;
    public $theme_path;
    public $theme_path_name;
    public $themeClass;

    public $user;
    public $user_id;
    public $user_roles;
    public $user_profile;

    public $meta = array();
    public $body_class = array();

    /**
     * Init
     *
     * @return void
     */
    public function init()
    {
        parent::init();

        // Init meta
        $this->meta = [
            'title' => '',
            'keywords' => '',
            'description' => '',
        ];

        // Init container
        $container = new Container();
        $container::$prefix = 'front';
        $container::$context = 'frontend';

        // Parse URL
        $this->parseURL();

        // Set user card hash
        $this->setUserCardHash();

        // Init theme
        $this->initTheme();

        // Init theme class
        if (method_exists($this->themeClass, 'init')) {
            $this->themeClass->init();
        }

        // Set analytics hash
        Analytics::setUserHash();
        Analytics::setUserSession();
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

        if (!$user->isGuest) {
            $this->body_class[] = 'user-logged-in';
        }

        return parent::beforeAction($action);
    }

    /**
     * Set card hash to user
     *
     * @return void
     */
    private function setUserCardHash()
    {
        $cookie_hash = false;
        $cookies = Yii::$app->request->cookies;
        $cookie_item = $cookies->getValue('card_hash');

        if ($cookie_item != null) {
            $cookie_hash = $cookie_item;
        }

        if (!$cookie_hash || empty($cookie_hash)) {
            $str = _random_string('alnum', 30);
            $str .= '-' . date('Y-m-d-H:i:s', strtotime('+1 year'));
            $str .= '-' . _random_string('alpha', 5);
            $str .= '-' . strtotime('now');
            $hash = md5($str) . '_' . rand(100000000, 999999999);

            $cookie = new Cookie([
                'name' => 'card_hash',
                'value' => $hash,
                'expire' => time() + (60 * 60 * 24 * 365),
            ]);

            Yii::$app->response->cookies->add($cookie);
        }
    }

    /**
     * Init theme
     *
     * @return void
     */
    private function initTheme()
    {
        // Get theme
        $site_theme = get_site_theme();

        // Theme path
        $this->theme_path = THEMES_PATH . $site_theme . DS;
        $this->theme_alias = '@themes/' . $site_theme . '/';

        if (is_dir($this->theme_path)) {
            Yii::$app->layoutPath = $this->theme_alias . 'layouts';
            Yii::$app->viewPath = $this->theme_alias . 'views';

            $theme_class_file = $this->theme_path . 'app/StoreTheme.php';

            if (is_file($theme_class_file)) {
                require_once $theme_class_file;
                $this->themeClass = new \StoreTheme();
            } else {
                $this->viewPath = '@frontend/views/system';

                echo $this->renderPartial('theme-error', array(
                    'message' => 'Theme class not found!'
                ));

                exit();
            }
        } else {
            $this->viewPath = '@frontend/views/system';

            echo $this->renderPartial('theme-error', array(
                'message' => 'Theme directory not found!'
            ));

            exit();
        }
    }

    /**
     * Register JS file or files
     *
     * @param [type] $file
     * @return void
     */
    public function registerJs($file)
    {
        if ($file) {
            $this->themeClass->registerJs($file);
        }
    }

    /**
     * Register CSS file or files
     *
     * @param [type] $file
     * @return void
     */
    public function registerCss($file)
    {
        if ($file) {
            $this->themeClass->registerCss($file);
        }
    }

    /**
     * Error page
     *
     * @return void
     */
    public function errorPage()
    {
        Yii::$app->response->statusCode = 404;
        $this->meta['title'] = _e('Page not found');
        return $this->render('error');
    }

    /**
     * Parse URL to controller
     *
     * @return array
     */
    private function parseURL()
    {
        $parsed_url = '';
        $lang_code = '';
        $lang_array = array();
        $languages = get_languages();

        $url = Yii::$app->request->pathInfo;
        $url = trim($url, '/');
        $url_array = $url ? explode('/', $url) : array();

        if ($url_array) {
            $lang_code = $url_array[0];
            unset($url_array[0]);

            $parsed_url = array_values($url_array);
        } else {
            $setting = get_setting('site_language', false);
            $lang_code = $setting ? $setting['settings_value'] : '';
        }

        if ($languages) {
            foreach ($languages as $item) {
                if ($item['lang_code'] == $lang_code) {
                    $lang_array = $item;
                    $lang_array['flag'] = images_url('flags/svg/' . $lang_code . '.svg');
                }
            }
        }

        if ($lang_array) {
            Yii::$app->language = $lang_array['locale'];
        }

        Container::$language = $lang_array;
        Container::push('parsed_url', $parsed_url);
    }
}

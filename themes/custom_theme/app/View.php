<?php

namespace themes\custom_theme\app;

class View extends \yii\web\View
{
    public $page_title = '';
    public $breadcrumbs = array();
    public $breadcrumb_title = '';

    /**
     * Get theme assets URL
     *
     * @param string $name
     * @return void
     */
    public function getAssetsUrl($name = '')
    {
        $path = \Yii::$app->controller->theme_alias;
        $path = substr($path, 1);
        $path = str_replace('/', '\\', $path);

        return $this->getAssetManager()->getBundle($path . 'app\AppAsset')->baseUrl . '/' . $name;
    }

    /**
     * Get theme partial
     *
     * @param string $name
     * @param array $data
     * @param boolean $view_as_string
     * @return mixed
     */
    public function getPartial($name = '', $data = array(), $view_as_string = false)
    {
        $file_name = trim($name, '/');
        $file_name = str_replace('.php', '', $file_name);
        $partials_path = \Yii::$app->controller->theme_alias . 'partials/';
        $render_file = \Yii::$app->view->renderFile($partials_path . $file_name . '.php', $data);

        if ($view_as_string) {
            return $render_file;
        } else {
            echo $render_file;
        }
    }

    /**
     * Get template
     *
     * @param string $name
     * @param array $data
     * @param boolean $view_as_string
     * @return mixed
     */
    public function getTemplate($name = '', $data = array(), $view_as_string = false)
    {
        $file_name = trim($name, '/');
        $file_name = str_replace('.php', '', $file_name);
        $partials_path = \Yii::$app->controller->theme_alias . 'templates/';
        $render_file = \Yii::$app->view->renderFile($partials_path . $file_name . '.php', $data);

        if ($view_as_string) {
            return $render_file;
        } else {
            echo $render_file;
        }
    }

    /**
     * Set meta tags
     *
     * @return string
     */
    public function metaTags()
    {
        echo init_meta_tags($this);
    }

    /**
     * Get body class
     *
     * @param string $default
     * @return void
     */
    public function getBodyClass($default = 'page-body')
    {
        $string = $default;
        $array = \Yii::$app->controller->body_class;

        if ($array && is_array($array)) {
            foreach ($array as $item) {
                if (is_string($item)) {
                    $string .= ' ' . $item;
                }
            }
        }

        return $string ? 'class="' . trim($string) . '"' : '';
    }

    /**
     * Init scripts
     *
     * @return string
     */
    public function initScripts()
    {
        echo \common\models\Analytics::setJS($this);
    }
}

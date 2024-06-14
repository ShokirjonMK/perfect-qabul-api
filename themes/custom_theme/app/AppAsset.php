<?php

namespace themes\custom_theme\app;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@themes/custom_theme/assets/';

    public $default_items = [];

    public $publishOptions = [
        'forceCopy' => THEME_FORCE_COPY,
        'except' => ['src'],
    ];

    public $css = [
        'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&display=swap',
        'https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800,900&display=swap',
        'https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css',
    ];

    public $js = [];

    public $depends = [
        'yii\web\YiiAsset',
    ];

    public function init()
    {
        parent::init();
        
        $version_name = '?ver=1.0.0';

        if (YII_DEBUG) {
            $this->css[] = 'dist/dev.dist.css' . $version_name;
            $this->css[] = 'dist/dev.theme.css' . $version_name;

            $this->js[] = 'dist/dev.dist.js' . $version_name;
            $this->js[] = 'dist/dev.theme.js' . $version_name;
        } else {
            $this->css[] = 'dist/app.dist.css' . $version_name;
            $this->css[] = 'dist/app.theme.css' . $version_name;

            $this->js[] = 'dist/app.dist.js' . $version_name;
            $this->js[] = 'dist/app.theme.js' . $version_name;
        }
    }
}

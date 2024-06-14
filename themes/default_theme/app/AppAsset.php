<?php

namespace themes\default_theme\app;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@themes/default_theme/assets/';

    public $default_items = [];

    public $publishOptions = [
        'forceCopy' => THEME_FORCE_COPY,
        'except' => [],
    ];

    public $css = [
        'https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css',
        'libs/bootstrap/css/bootstrap.min.css',
        'libs/fancybox/jquery.fancybox.min.css',
        'libs/nprogress/style.min.css',
        'libs/owl-carousel/assets/owl.carousel.min.css',
        'libs/owl-carousel/assets/owl.theme.default.min.css',
    ];

    public $js = [
        'libs/js/popper.min.js',
        'libs/js/page-load.min.js?ver=1.0.7',
        'libs/bootstrap/js/bootstrap.min.js',
        'libs/fancybox/jquery.fancybox.min.js',
        'libs/nprogress/init.min.js',
        'libs/owl-carousel/owl.carousel.min.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];

    public function init()
    {
        parent::init();
        $version_name = '?ver=1.0.2';

        $this->css[] = 'theme/css/style.css' . $version_name;
        $this->css[] = 'theme/css/responsive.css' . $version_name;
        $this->js[] = 'theme/js/custom.js' . $version_name;
    }
}

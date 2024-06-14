<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = '@frontend/assets/';

    public $css = [
        'https://fonts.googleapis.com/css?family=Roboto:100,300,400,500,700,900&display=swap',
        'https://fonts.googleapis.com/css?family=Poppins:200,300,400,500,600,700,800,900&display=swap',
        'theme/css/animate.css',
        'theme/bootstrap/css/bootstrap.min.css',
        'theme/css/all.min.css',
        'theme/css/ionicons.min.css',
        'theme/css/themify-icons.css',
        'theme/css/linearicons.css',
        'theme/css/flaticon.css',
        'theme/css/simple-line-icons.css',
        'theme/owlcarousel/css/owl.carousel.min.css',
        'theme/owlcarousel/css/owl.theme.css',
        'theme/owlcarousel/css/owl.theme.default.min.css',
        'theme/css/magnific-popup.css',
        'theme/css/slick.css',
        'theme/css/slick-theme.css',
    ];

    public $js = [
        'theme/js/popper.min.js',
        'theme/bootstrap/js/bootstrap.min.js',
        'theme/owlcarousel/js/owl.carousel.min.js',
        'theme/js/magnific-popup.min.js',
        'theme/js/parallax.js',
        'theme/js/jquery.countdown.min.js',
        'theme/js/imagesloaded.pkgd.min.js',
        'theme/js/isotope.min.js',
        'theme/js/jquery.dd.min.js',
        'theme/js/slick.min.js',
        'theme/js/jquery.elevatezoom.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];

    public function init()
    {
        parent::init();

        $this->js[] = 'theme/js/scripts.js';
        $this->css[] = 'theme/css/style.css';
        $this->css[] = 'theme/css/responsive.css';
    }
}

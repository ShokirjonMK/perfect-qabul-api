<?php

namespace backend\assets;

use yii\web\AssetBundle;

/**
 * Login page asset bundle.
 */
class LoginAsset extends AssetBundle
{
    public $sourcePath = '@backend/assets/';

    public $css = [
        'dist/css/bootstrap.min.css',
        'dist/css/icons.min.css',
        'dist/css/sweetalert2.min.css',
        'dist/css/app.min.css',
    ];

    public $js = [
        'dist/libs/bootstrap/js/bootstrap.bundle.min.js',
        'dist/libs/metismenu/metisMenu.min.js',
        'dist/libs/simplebar/simplebar.min.js',
        'dist/libs/node-waves/waves.min.js',
        'dist/js/sweetalert2.min.js',
        'dist/js/app.js',
        'dist/js/scripts.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}

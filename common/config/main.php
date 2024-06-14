<?php
$app_config = array();
$app_config_file = dirname(dirname(__DIR__)) . '/config.inc.php';

// Check config
if (is_file($app_config_file)) {
    $app_config = include $app_config_file;
}

// Theme force copy define
$theme_force_copy = array_value($app_config, 'theme_force_copy', false);
define('THEME_FORCE_COPY', $theme_force_copy);

// Set configs
$main_config = array(
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'timeZone' => 'Asia/Tashkent',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
          //  'defaultRoles' => ['customer'],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'mailer' => array_value($app_config, 'mailer'),
    ],
);

return check_app_config_files($main_config, 'common');

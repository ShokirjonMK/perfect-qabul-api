<?php
if (YII_ENV_DEV) {
    error_reporting(E_ALL);
    ini_set("display_startup_errors", "1");
    ini_set("display_errors", "1");
}

Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@base', dirname(dirname(__DIR__)) . '/base');
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@themes', dirname(dirname(__DIR__)) . '/themes');

require dirname(dirname(__DIR__)) . '/config.boot.php';
require dirname(dirname(__DIR__)) . '/base/helpers/base.php';
require dirname(dirname(__DIR__)) . '/base/helpers/global.php';
require dirname(dirname(__DIR__)) . '/base/helpers/types.php';
require dirname(dirname(__DIR__)) . '/base/helpers/strings.php';
require dirname(dirname(__DIR__)) . '/base/helpers/url.php';
require dirname(dirname(__DIR__)) . '/base/helpers/security.php';
require dirname(dirname(__DIR__)) . '/base/helpers/theme.php';
require dirname(dirname(__DIR__)) . '/base/helpers/tools.php';
require dirname(dirname(__DIR__)) . '/base/helpers/user.php';
require dirname(dirname(__DIR__)) . '/base/helpers/role.php';

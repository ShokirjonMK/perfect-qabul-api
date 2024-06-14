<?php
$params = array();

$config_file = dirname(dirname(__DIR__)) . '/config.inc.php';
$config_theme = dirname(dirname(__DIR__)) . '/config.inc.php';

if (is_file($config_file) && is_file($config_theme)) {
    $_config_file = include dirname(dirname(__DIR__)) . '/config.inc.php';
    $_config_theme = include dirname(dirname(__DIR__)) . '/config.theme.php';

    if (is_array($_config_file) && $_config_file) {
        $params = array_merge($params, $_config_file);
    }

    if (is_array($_config_theme) && $_config_theme) {
        $params = array_merge($params, $_config_theme);
    }
}

return $params;

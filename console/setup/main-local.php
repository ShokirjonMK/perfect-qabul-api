<?php
$config_file = dirname(dirname(__DIR__)) . '/config.inc.php';

if (is_file($config_file)) {
    $config = require $config_file;

    return [
        'components' => $config['database'],
    ];
}

return [];

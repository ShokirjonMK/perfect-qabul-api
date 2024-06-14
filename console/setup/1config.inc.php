<?php
return array(
    'app_id' => '',
    'site_master_pass' => '2372390123',
    'domain_name' => 'localhost.loc',
    'api_url' => 'http://api.localhost.loc/',
    'assets_url' => 'http://assets.localhost.loc/',
    'admin_url' => 'http://admin.localhost.loc/',
    'site_url' => 'http://localhost.loc/',
    'local_cache' => false,
    'theme_force_copy' => false,
    'redis' => [
        'active' => false,
        'prefix' => 'mywebsite',
        'password' => '',
        'secret_key' => '',
        'secret_iv' => '',
        'config' => [
            'host' => '127.0.0.1',
            'port' => '6379',
            'scheme' => 'tcp',
        ]
    ],
    'database' => array(
        'db' => array(
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=db_name_here',
            'username' => 'user_name_here',
            'password' => 'password_here',
            'charset' => 'utf8mb4',
            'attributes' => [
                '{PDO_ATTR}' => "SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));",
            ],
        )
    ),
    'mailer' => array(
        'class' => 'yii\swiftmailer\Mailer',
        'useFileTransport' => true,
    ),
    'adminEmail' => 'mkshokirjon@gmail.com',
    'infoEmail' => 'info@domain.com',
    'supportEmail' => 'support@domain.com',
    'senderEmail' => 'noreply@domain.com',
    'senderName' => 'MY WEBSITE',
);

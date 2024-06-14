<?php

namespace base\libs;

use Yii;

class RedisApp
{
    public static function config($key, $default = false)
    {
        $array = Yii::$app->params['redis'];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $default;
    }

    public static function prefix($key = false, $append = false)
    {
        $prefix = '';
        $uniqkey = self::config('prefix');

        if ($key && $append) {
            $prefix = $uniqkey ? $uniqkey . '_' . $key : $key;
        } elseif ($key) {
            $prefix = $key;
        }

        return $prefix;
    }

    public static function is_active()
    {
        return self::config('active', false);
    }

    public static function connect($args = array())
    {
        if (self::is_active()) {
            $redis_client = self::config('config');

            if (is_array($args) && $args) {
                $client = new \Predis\Client($args);
            } elseif ($redis_client) {
                $client = new \Predis\Client($redis_client);
            } else {
                $client = new \Predis\Client(['scheme' => 'tcp', 'host' => '127.0.0.1']);
            }

            $redis_password = self::config('password');

            if ($redis_password) {
                $client->auth($redis_password);
            }

            return $client;
        }
    }

    public static function client()
    {
        if (self::is_active()) {
            return self::connect();
        }
    }

    public static function keys($key)
    {
        if (self::is_active()) {
            $redis_key = self::prefix($key, true);

            $client = self::connect();
            return $client->keys($redis_key);
        }
    }

    public static function get($key)
    {
        if (self::is_active()) {
            $redis_key = self::prefix($key, true);

            $client = self::connect();
            return $client->get($redis_key);
        }
    }

    public static function set($key, $value, $expire = 0)
    {
        if (self::is_active()) {
            $redis_key = self::prefix($key, true);

            $client = self::connect();
            $client->set($redis_key, $value);

            if (is_numeric($expire) && $expire > 0) {
                $client->expire($redis_key, $expire);
            }
        }
    }

    public static function rpush($key, $value, $expire = 0)
    {
        if (self::is_active()) {
            $redis_key = self::prefix($key, true);

            $client = self::connect();
            $client->rpush($redis_key, $value);

            if (is_numeric($expire) && $expire > 0) {
                $client->expire($redis_key, $expire);
            }
        }
    }

    public static function lpush($key, $value, $expire = 0)
    {
        if (self::is_active()) {
            $redis_key = self::prefix($key, true);

            $client = self::connect();
            $client->rpush($redis_key, $value);

            if (is_numeric($expire) && $expire > 0) {
                $client->expire($redis_key, $expire);
            }
        }
    }

    public static function lrange($key, $x = 0, $y = -1)
    {
        if (self::is_active()) {
            $redis_key = self::prefix($key, true);

            $client = self::connect();
            return $client->lrange($redis_key, $x, $y);
        }
    }

    public static function delete($key, $prefix = true)
    {
        if (self::is_active()) {
            $redis_key = $prefix ? self::prefix($key, true) : $key;

            $client = self::connect();
            return $client->del($redis_key);
        }
    }

    public static function crypt($action, $string)
    {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = self::config('secret_key', '5oz2I8BCaogpJiABy4UuEy7gnkFdS11Gei3y832d');
        $secret_iv = self::config('secret_iv', 'Hii1poVffWggJQRiVh4LPG3I7J7xdOJd73o73hds83');
        $key = hash('sha256', $secret_key);
        $iv = substr(hash('sha256', $secret_iv), 0, 16);

        if ($action == 'encrypt') {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } elseif ($action == 'decrypt') {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }

        return $output;
    }
}

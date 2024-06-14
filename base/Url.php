<?php

namespace base;

/**
 * URL class
 */
class Url
{
    /**
     * Account url
     *
     * @param string $path
     * @return string
     */
    public static function account($path = null, $args = null)
    {
        $url = site_url('account/', true);
        $array = ['activation', 'recovery', 'signin', 'signup'];

        if (!is_null($path)) {
            $path = trim($path);
            $path = trim($path, '/');

            if (in_array($path, $array)) {
                $url = $url . $path . '/';
            }
        }

        if (!is_null($args) && $args) {
            return $url . self::args($args);
        }

        return $url;
    }

    /**
     * Profile url
     *
     * @param string $path
     * @return string
     */
    public static function profile($path = null, $args = null)
    {
        $url = site_url('profile/', true);
        $array = ['company', 'orders', 'order-view', 'favorite-products', 'addresses', 'password', 'settings', 'logout'];

        if (!is_null($path)) {
            $path = trim($path);
            $path = trim($path, '/');

            if (in_array($path, $array)) {
                $url = $url . $path . '/';
            }
        }

        if (!is_null($args) && $args) {
            return $url . self::args($args);
        }

        return $url;
    }

    /**
     * Args
     *
     * @param array $args
     * @return string
     */
    private static function args($args)
    {
        $output = '';

        if (!is_null($args) && $args) {
            if (is_array($args)) {
                $array = array();

                foreach ($args as $key => $item) {
                    if ($key && $item) {
                        $key = trim($key);
                        $item = trim($item);

                        $array[] = "{$key}={$item}";
                    }
                }

                if ($array) {
                    $str = implode('&', $array);
                    $output = '?' . $str;
                }
            } else {
                $args = trim($args);
                $args = trim($args, '?');

                $output = '?' . $args;
            }
        }

        return $output;
    }
}

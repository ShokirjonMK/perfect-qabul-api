<?php
namespace base;

class Container
{
    public static $prefix = '';
    public static $context = '';
    public static $language = '';
    public static $array = array();

    /**
     * Add value to array
     *
     * @param [type] $key
     * @param [type] $value
     * @return mixed
     */
    public static function push($key, $value = '')
    {
        if ($key) {
            self::$array[self::_key($key)] = $value;
        }
    }

    /**
     * Add array to container
     *
     * @param array $array
     * @return mixed
     */
    public static function push_array($array)
    {
        if (is_array($array) && $array) {
            foreach ($array as $key => $value) {
                self::$array[self::_key($key)] = $value;
            }
        }
    }

    /**
     * Get array value
     *
     * @param [type] $key
     * @param [type] $default
     * @return mixed
     */
    public static function get($key, $default = false)
    {
        if ($key && isset(self::$array[self::_key($key)])) {
            return self::$array[self::_key($key)];
        }

        return $default;
    }

    /**
     * Update array value
     *
     * @param [type] $key
     * @param [type] $value
     * @return mixed
     */
    public static function update($key, $value = false)
    {
        if ($key && isset(self::$array[self::_key($key)])) {
            self::$array[self::_key($key)] = $value;
        }
    }

    /**
     * Delete array value
     *
     * @param [type] $key
     * @return mixed
     */
    public static function delete($key)
    {
        if ($key && isset(self::$array[self::_key($key)])) {
            unset(self::$array[self::_key($key)]);
        }
    }

    /**
     * List array
     *
     * @return array
     */
    public static function list_array()
    {
        return self::$array;
    }

    /**
     * Prefix and key
     *
     * @param [type] $key
     * @return mixed
     */
    private static function _key($key)
    {
        $prefix = self::$prefix;

        if ($prefix) {
            return $prefix .'_'. $key;
        }

        return $key;
    }
}

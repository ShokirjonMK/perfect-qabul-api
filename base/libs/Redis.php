<?php

namespace base\libs;

class Redis extends RedisApp
{
    public static function cachedUrl($table_name, $id, $language, $action, $results = null)
    {
        if ($table_name && self::is_active()) {
            $tableName = 'table_' . $table_name;
            $redis_key = "{$tableName}_url_string_id_{$language}_{$id}";

            if ($action == 'get') {
                $cached_item = self::get($redis_key);

                if (!is_null($cached_item) && is_string($cached_item)) {
                    $hash = self::crypt('decrypt', $cached_item);
                    return unserialize($hash);
                } elseif ($action == 'set') {
                    $serialize = serialize($results);
                    $hash = self::crypt('encrypt', $serialize);
                    self::set($redis_key, $hash);
                }
            }
        }
    }
}

<?php
namespace base;

class Backend
{
    /**
     * Get language
     *
     * @param [type] $type
     * @return mixed
     */
    public static function language($type)
    {
        if ($type == 'current') {
            return Container::$language;
        } elseif ($type == 'content') {
            return Container::get('content_language');
        } elseif ($type == 'list') {
            return Container::get('languages_list');
        }
    }
}

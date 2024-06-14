<?php

namespace common\models\enums;

class Gender
{

    use BaseEnum;

    const MALE = 1;
    const FEMALE = 0;

    public static function list()
    {
        return [
            self::MALE => _e('Male'),
            self::FEMALE => _e('Female'),
        ];
    }
}

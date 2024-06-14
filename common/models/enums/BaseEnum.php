<?php

namespace common\models\enums;

trait BaseEnum {

    public static function one($value){
        return self::list()[$value] ?? '';
    }

}
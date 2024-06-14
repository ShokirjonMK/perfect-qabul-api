<?php

namespace common\models\enums;

class Education {

    use BaseEnum;
    
    const GENERAL = 1;
    const SPECIAL = 2;
    const HIGHER = 3;

    public static function list(){
        return [
            self::GENERAL => _e('Secondary general education'),
            self::SPECIAL => _e('Secondary special education'),
            self::HIGHER => _e('Higher education'),
        ];
    }

}
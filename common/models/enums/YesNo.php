<?php

namespace common\models\enums;

class YesNo {

    use BaseEnum;
    
    const YES = 1;
    const NO = 0;

    public static function list(){
        
        $data = [
            self::YES => _e('Yes'),
            self::NO => _e('No'),
        ];



        return $data;
    }

}
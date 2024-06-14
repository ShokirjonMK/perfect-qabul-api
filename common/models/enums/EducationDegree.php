<?php

namespace common\models\enums;

class EducationDegree {

    use BaseEnum;
    
    const BACHELOR = 1;
    const MASTERS = 2;

    public static function list(){
        return [
            self::BACHELOR => _e('Bachelor degree'),
            self::MASTERS => _e('Masters degree'),
        ];
    }

}
<?php

namespace common\models\enums;

class FamilyStatus {

    use BaseEnum;
    
    const SINGLE = 0;
    const MARRIED = 1;

    public static function list(){
        return [
            self::SINGLE => _e('Single'),
            self::MARRIED => _e('Merried'),
        ];
    }

}
<?php

namespace common\models\enums;

class TopicType {

    use BaseEnum;
    
    const PRACTICAL = 1;
    const THEORETICAL = 2;

    public static function list(){
        return [
            self::PRACTICAL => _e('Practical'),
            self::THEORETICAL => _e('Theoretical'),
        ];
    }

}
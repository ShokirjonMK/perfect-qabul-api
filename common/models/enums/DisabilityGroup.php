<?php

namespace common\models\enums;

class DisabilityGroup {

    use BaseEnum;
    
    const GROUP_I = 1;
    const GROUP_II = 2;
    const GROUP_III = 3;

    public static function list(){
        return [
            self::GROUP_I => _e('I - disability group'),
            self::GROUP_II => _e('II - disability group'),
            self::GROUP_III => _e('III - disability group'),
        ];
    }

}
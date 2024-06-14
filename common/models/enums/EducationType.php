<?php

namespace common\models\enums;

class EducationType {

    use BaseEnum;
    
    const FULLTIME = 1;
    const EXTRAMURAL = 2;
    const EVENING = 3;
    const ONLINE = 4;

    public static function list(){
        return [
            self::FULLTIME => _e('Full-time education'),
            self::EXTRAMURAL => _e('Extramural education'),
            self::EVENING => _e('Evening education'),
            self::ONLINE => _e('Online education'),
        ];
    }

}
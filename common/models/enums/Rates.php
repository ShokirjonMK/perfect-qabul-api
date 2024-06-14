<?php

namespace common\models\enums;

class Rates {

    use BaseEnum;

    const RATE_025 = 0.25;
    const RATE_03 = 0.3;
    const RATE_050 = 0.5;
    const RATE_100 = 1;
    const RATE_125 = 1.25;
    const RATE_150 = 1.5;

    public static function list(){
        return [
            "'" . self::RATE_025 . "'"  => _e('Rate 0.25'),
            "'" . self::RATE_03 . "'"  => _e('Rate 0.3'),
            "'" . self::RATE_050 . "'" => _e('Rate 0.5'),
            "'" . self::RATE_100 . "'" => _e('Rate 1'),
            "'" . self::RATE_125 . "'" => _e('Rate 1.25'),
            "'" . self::RATE_150 . "'" => _e('Rate 1.5'),
        ];
    }

}
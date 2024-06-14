<?php

namespace base\libs;

use yii\helpers\Inflector;

/**
 * Translit helper class.
 * Make shure that you set locale for using iconv.
 */
class Translit
{
    /**
     * Char list
     */
    private static $_chars = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ғ' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'қ' => 'q', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ў' => 'o', 'ф' => 'f', 'х' => 'x', 'ҳ' => 'h', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'i', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',

        'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Ғ' => 'G', 'Д' => 'D',
        'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
        'Й' => 'Y', 'К' => 'K', 'Қ' => 'Q', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
        'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
        'У' => 'U', 'Ў' => 'O', 'Ф' => 'F', 'Х' => 'X', 'Ҳ' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch',
        'Ш' => 'Sh', 'Щ' => 'Sch', 'Ъ' => '', 'Ы' => 'I', 'Ь' => '',
        'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',

        'ü' => 'u', 'ğ' => 'g', 'ş' => 's', 'ö' => 'o', 'ç' => 'c',
        'Ü' => 'U', 'Ğ' => 'G', 'Ş' => 'S', 'Ö' => 'o', 'Ç' => 'C', 'İ' => 'I',

        '-' => '-', ' ' => '-', '.' => '-', ',' => '-', '&' => 'and',
    ];

    /**
     * Tranlit text
     *
     * @param string $text
     * @param string $replacement
     * @param boolean $lowercase
     * @return string
     */
    public static function text($text)
    {
        $text = trim($text);

        $replace = self::$_chars;
        $string = '';

        for ($i = 0; $i < mb_strlen($text); $i++) {
            $c = mb_substr($text, $i, 1);

            if (array_key_exists($c, $replace)) {
                $string .= $replace[$c];
            } else {
                $string .= $c;
            }
        }

        return $string;
    }

    /**
     * Tranlit slug
     *
     * @param string $text
     * @param string $replacement
     * @param boolean $lowercase
     * @return string
     */
    public static function slug($text, $replacement = '-', $lowercase = true)
    {
        $text = trim($text);

        if ($lowercase) {
            $text = mb_strtolower($text, 'UTF-8');
        }

        $replace = self::$_chars;
        $string = '';

        for ($i = 0; $i < mb_strlen($text); $i++) {
            $c = mb_substr($text, $i, 1);
            if (array_key_exists($c, $replace)) {
                $string .= $replace[$c];
            } else {
                $string .= $c;
            }
        }

        // Make sure that you set locale for using iconv
        $string = iconv('UTF-8', 'UTF-8//TRANSLIT', $string);

        // Remove symbols
        $string = preg_replace('/[^\-0-9a-z]+/i', '', $string);

        // Double spaces
        $string = preg_replace('/\-+/', '-', $string);

        // Check by inflector
        $output = Inflector::slug($string, $replacement, $lowercase);

        return $output;
    }
}

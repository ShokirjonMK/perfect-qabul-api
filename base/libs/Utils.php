<?php

namespace base\libs;

use common\models\Languages;

class Utils
{
    /**
     * Locale list
     *
     * @return array
     */
    public static function getLocaleList()
    {
        $output = array();

        $results = Languages::find()
            ->orderBy(['name' => SORT_ASC])
            ->all();

        if ($results) {
            foreach ($results as $item) {
                $output[$item->locale] = $item->name;
            }
        }

        return $output;
    }

    /**
     * Timezone list
     *
     * @return array
     */
    public static function getTimezoneList()
    {
        $output = array();

        $results = timezone_identifiers_list();

        if ($results) {
            foreach ($results as $item) {
                $output[$item] = $item;
            }
        }

        return $output;
    }

    /**
     * Get price format types
     *
     * @return object
     */
    public static function priceFormatTypes()
    {
        $array = array(
            '1' => '1.230,00',
            '2' => '1,230.00',
            '3' => '1 230,00',
            '4' => '1 230.00',
        );

        return $array;
    }

    /**
     * Get price format type
     *
     * @return object
     */
    public static function getPriceFormatType()
    {
        $output = array(',', '.');
        $price_format_type = get_setting_value('price_format');

        switch ($price_format_type) {
            case 1:
                $output = array(',', '.');
                break;
            case 2:
                $output = array('.', ',');
                break;
            case 3:
                $output = array(',', ' ');
                break;

            default:
                $output = array('.', ' ');
                break;
        }

        return $output;
    }

    /**
     * Price format
     *
     * @param integer $price
     * @return mixed
     */
    public static function priceFormat($price, $is_numeric = false)
    {
        $output = '0.00';

        if ($is_numeric === true && is_numeric($price)) {
            $output = (float) $price;
        } elseif (is_numeric($price)) {
            $pf = self::getPriceFormatType();
            $output = number_format($price, 2, $pf[0], $pf[1]);
            $output = str_replace($pf[0] . '00', '', $output);
        }

        return $output;
    }

    /**
     * Currency format
     *
     * @param integer $price
     * @param string $currency
     * @param string $output
     * @return mixed
     */
    public static function currencyFormat($price, $currency, $output = null)
    {
        if (is_null($output)) {
            $output = get_setting_value('currency_format', '{{price} {{currency}}');
        }

        return str_replace(['{{price}}', '{{currency}}'], [$price, $currency], $output);
    }

    /**
     * Get company by TIN
     *
     * @param int $tin
     * @return array
     */
    public static function getCompanyByTIN($tin)
    {
        $output = array();

        if (is_numeric($tin)) {
            $url = 'https://devapi.goodsign.biz/v1/profile/' . $tin;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url);
            $result = curl_exec($ch);
            curl_close($ch);

            $company_data = json_decode($result, true);

            if (isset($company_data['name']) && !empty($company_data['name'])) {
                $output = $company_data;
            }
        }

        return $output;
    }
}

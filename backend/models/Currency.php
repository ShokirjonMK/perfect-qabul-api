<?php

namespace backend\models;

use common\models\CurrencyList;
use common\models\CurrencyRates;

class Currency
{
    /**
     * Update currency status
     *
     * @param integer $id
     * @param integer $status
     * @return boolean
     */
    public static function setItemStatus($id, $status = 0)
    {
        $item = CurrencyList::findOne(['id' => $id]);

        if ($item && is_numeric($status)) {
            $item->status = $status;
            $item->update(false);

            return true;
        }

        return false;
    }

    /**
     * Price value format
     *
     * @param integer $number
     * @return int
     */
    public static function valueFormat($number)
    {
        $sum = number_format(round($number, 4), 4);
        $exp = explode('.', $sum);

        if ($sum > 0 && isset($exp[1])) {
            $zero = explode('0', $exp[1]);

            if (count($zero) > 2) {
                $sum = number_format(round($number, 2), 2);
            }
        }

        $output = $sum > 0 ? $sum : number_format(round($number, 6), 6);

        return $output > 0 ? $output : '0.00';
    }

    /**
     * Rate comparsion
     *
     * @param integer $currentValue
     * @param integer $oldValue
     * @return int
     */
    public static function rateComparison($currentValue, $oldValue)
    {
        if ($currentValue > 0 and $oldValue > 0) {
            $percentage = $currentValue / $oldValue * 100 - 100;
            $percentage = floatval($percentage);
        } else {
            $percentage = 0;
        }

        return $percentage;
    }

    /**
     * Get rates
     *
     * @return object
     */
    public static function getRates()
    {
        $query = CurrencyRates::find()->all();
        return $query;
    }

    /**
     * Get list to settings
     *
     * @return object
     */
    public static function getListToSettings()
    {
        $array = array();
        $query = CurrencyList::find()->all();

        if ($query) {
            foreach ($query as $key => $value) {
                $array[$value->currency_code] = $value->currency_name;
            }
        }

        return $array;
    }
}

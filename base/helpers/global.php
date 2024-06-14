<?php
// Get current lang
function get_current_lang($field = 'lang_code')
{
    $array = \base\Container::$language;

    if ($field == 'array') {
        return $array;
    }

    return array_value($array, $field);
}

// Get languages
function get_languages($fields = null)
{
    $output = array();

    $temp = new \base\libs\Temp();
    $temp_file = $temp->getArray('system', 'languages');

    if ($temp_file) {
        $output = $temp_file;
    }

    if (!$output) {
        $output = \common\models\Languages::find()
            ->where(['status' => 1])
            ->asArray()
            ->all();
    }

    if ($output) {
        foreach ($output as &$item) {
            $lang_code = $item['lang_code'];
            $item['flag'] = images_url('flags/svg/' . $lang_code . '.svg');
        }
    }

    if (!is_null($fields) && $output) {
        return filter_array_fields($output, $fields);
    }

    return $output;
}

// Get translations
function get_translations()
{
    $current_lang = get_current_lang('array');
    $current_lang_code = get_current_lang('lang_code');
    $languages = get_languages();

    $parsed_url = '';
    $parsed_url_array = \base\Container::get('parsed_url');
    $translation_links = \base\Container::get('translation_links');

    if ($parsed_url_array) {
        $parsed_url = implode('/', $parsed_url_array);
    }

    if ($languages) {
        foreach ($languages as &$language) {
            $lang_code = $language['lang_code'];

            if ($lang_code == $current_lang_code) {
                $language['current_language'] = true;
            } else {
                $language['current_language'] = false;
            }

            if (isset($translation_links[$lang_code]) && $translation_links[$lang_code]) {
                $language['link'] = $translation_links[$lang_code];
            } else {
                $language['link'] = site_url("{$lang_code}/{$parsed_url}");
            }
        }
    }

    $output = array(
        'current_lang_code' => $current_lang_code,
        'current_language' => $current_lang,
        'languages' => $languages,
    );

    return $output;
}

// Get currencies
function get_currencies($fields = null)
{
    $output = array();

    $temp = new \base\libs\Temp();
    $temp_file = $temp->getArray('system', 'currency_list');

    if ($temp_file) {
        $output = $temp_file;
    }

    if (!$output) {
        $output = \common\models\CurrencyList::find()
            ->where(['status' => 1])
            ->asArray()
            ->all();
    }

    if (!is_null($fields) && $output) {
        return filter_array_fields($output, $fields);
    }

    return $output;
}

// Get currency rates
function get_currency_rates($where = array(), $fields = null)
{
    $array = false;
    $output = array();
    $currencies = get_currencies('currency_code');

    $temp = new \base\libs\Temp();
    $temp_file = $temp->getArray('system', 'currency_rates');

    if ($temp_file) {
        $array = filter_array($temp_file, $where);
    }

    if (!$array) {
        $array = \common\models\CurrencyRates::find()
            ->where($where)
            ->asArray()
            ->all();
    }

    if ($array && $currencies) {
        foreach ($array as $item) {
            $from = $item['cfrom'];
            $to = $item['cto'];

            if (in_array($from, $currencies) && in_array($to, $currencies)) {
                $output[] = $item;
            }
        }
    }

    if (!is_null($fields) && $output) {
        return filter_array_fields($output, $fields);
    }

    return $output;
}

// Get currency rates as json
function get_currency_rates_js()
{
    $currency_rates = array();
    $currency_rates_list = array();
    $price_format_type = \base\libs\Utils::getPriceFormatType();
    $currency_format = get_setting_value('currency_format', '%price %currency');
    ;
    $currency_rates_array = get_currency_rates();

    if ($currency_rates_array) {
        foreach ($currency_rates_array as $item) {
            $ckey = $item['ckey'];
            $cto = $item['cto'];
            $cfrom = $item['cfrom'];
            $cvalue = $item['cvalue'];

            $currency_rates[$ckey] = $cvalue;
            $currency_rates_list[$ckey] = array($cto, $cfrom, $cvalue);
        }
    }

    echo '<script>';
    echo 'var $_currency_format = "' . $currency_format . '";';
    echo 'var $_price_format_type = ' . json_encode($price_format_type) . ';';
    echo 'var $_currency_rates = ' . json_encode($currency_rates) . ';';
    echo 'var $_currency_rates_list = ' . json_encode($currency_rates_list) . ';';
    echo '</script>';
}

// Get currency rate
function get_currency_rate($where)
{
    $where_cond = array();

    if (is_array($where)) {
        $where_cond = $where;
    } elseif (is_string($where)) {
        $where_cond['ckey'] = $where;
    }

    $rates = get_currency_rates($where_cond);

    return $rates ? $rates[0] : array();
}

// Get current currency
function get_current_currency($field = 'currency_code')
{
    $array = array();
    $list = get_currencies();
    $site_currency = get_setting_value('site_currency');

    if ($list && $site_currency) {
        foreach ($list as $item) {
            if ($site_currency == $item['currency_code']) {
                $array = $item;
            }
        }
    }

    if ($field == 'array') {
        return $array;
    }

    return array_value($array, $field);
}

// Calculate currency
function calculate_currency($from, $to, $price, $is_numeric = false)
{
    $ckey = $from . $to;
    $rate = get_currency_rate($ckey);
    $output = '0.00';

    if ($rate) {
        $value = array_value($rate, 'cvalue');
        $price_calc = $price / $value;

        if ($is_numeric) {
            $output = (float) $price_calc;
        } else {
            $output = \base\libs\Utils::priceFormat($price_calc);
        }
    }

    return $output;
}

// Get countries list
function get_countries($where = array())
{
    $output = array();

    $temp = new \base\libs\Temp();
    $temp_file = $temp->getArray('system', 'countries');

    if ($temp_file) {
        $output = filter_array($temp_file, $where);
    }

    if (!$output) {
        $output = \common\models\Countries::find()
            ->where($where)
            ->asArray()
            ->all();
    }

    return $output;
}

// Get countries list or single country
function get_country($param)
{
    $where = (is_array($param)) ? $param : ['ISO' => $param];
    $output = get_countries($where);
    return (count($output) == 1) ? $output[0] : $output;
}

// Get regions list
function get_regions($where = array())
{
    $output = array();

    $temp = new \base\libs\Temp();
    $temp_file = $temp->getArray('system', 'regions');

    if ($temp_file) {
        $output = filter_array($temp_file, $where);
    }

    if (!$output) {
        $output = \common\models\Regions::find()
            ->where($where)
            ->asArray()
            ->all();
    }

    return $output;
}

// Get regions list or single region
function get_region($param)
{
    $where = (is_array($param)) ? $param : ['id' => $param];
    $output = get_regions($where);
    return (count($output) == 1) ? $output[0] : $output;
}

// Returns cities list
function get_cities($country_id, $where = array())
{
    $output = array();
    $where_cond = $where;

    if (is_numeric($country_id) && $country_id > 0) {
        $where_cond['country_id'] = $country_id;
    }

    $where_cond['type'] = \common\models\Regions::TYPE_CITY;
    $output = get_regions($where_cond);

    return $output;
}

// Returns city
function get_city($city_id, $where = array())
{
    $output = array();
    $where_cond = $where;

    if (is_numeric($city_id) && $city_id > 0) {
        $where_cond['id'] = $city_id;
    }

    $where_cond['type'] = \common\models\Regions::TYPE_CITY;
    $output = get_regions($where_cond);

    return $output;
}

// Get setting
function get_setting($key, $language = null)
{
    $output = array();

    if (is_null($language) || !$language) {
        $lang = get_current_lang();
    } else {
        $lang = clean_str($language);
    }

    $temp = new \base\libs\Temp();
    $temp_file = $temp->getArray('system', 'settings');

    if ($temp_file) {
        foreach ($temp_file as $temp_value) {
            if ($temp_value['settings_key'] == $key) {
                $output = $temp_value;
                break;
            }
        }
    }

    if ($lang) {
        $temp_file = $temp->getArray('system', 'settings_translations');

        if (isset($temp_file[$lang]) && $temp_file[$lang]) {
            foreach ($temp_file[$lang] as $temp_value) {
                if ($temp_value['settings_key'] == $key) {
                    $output['settings_value'] = $temp_value['settings_value'];
                    break;
                }
            }
        }
    }

    if (!$output) {
        $where = ['settings_key' => $key];
        $output = \backend\models\System::getSetting($where, $lang);
    }

    return $output;
}

// Get setting value
function get_setting_value($key, $default = false, $language = null)
{
    $output = array();
    $setting = get_setting($key, $language);

    if ($setting) {
        $output = $setting['settings_value'];
    }

    return $output ? $output : $default;
}

// Get product image
function get_product_image($info)
{
    $output = images_url('default-image.png');

    if ($info && $info->image) {
        $output = $info->image;
    }

    return $output;
}

// String to price
function string_to_price($price)
{
    $output = '0.00';

    if (is_string($price)) {
        $output = preg_replace("/[^0-9.,]/", "", $price);
        $output = (float) $output;
    } elseif (is_numeric($price)) {
        $output = $price;
    }

    return $output;
}

// Convert price
function convert_price($price, $currency, $output = null)
{
    $price_number = string_to_price($price);
    $current_currency = get_current_currency();
    $is_numeric = false;

    if (!is_null($output) && $output == 'numeric') {
        $is_numeric = true;
    }

    if ($currency != $current_currency) {
        $price_converted = calculate_currency($current_currency, $currency, $price_number, $is_numeric);
    } else {
        $price_converted = \base\libs\Utils::priceFormat($price_number, $is_numeric);
    }

    return $price_converted;
}

// Convert price to
function convert_price_to($currency, $price, $price_currency, $output = null)
{
    $price_number = string_to_price($price);
    $is_numeric = false;

    if (!is_null($output) && $output == 'numeric') {
        $is_numeric = true;
    }

    if ($price_currency != $currency) {
        $price_converted = calculate_currency($currency, $price_currency, $price_number, $is_numeric);
    } else {
        $price_converted = \base\libs\Utils::priceFormat($price_number, $is_numeric);
    }

    return $price_converted;
}

// Get price
function format_price($price, $currency, $output = null)
{
    $price_number = string_to_price($price);
    $current_currency = get_current_currency();

    if ($currency != $current_currency) {
        $price_formatted = calculate_currency($current_currency, $currency, $price_number);
    } else {
        $price_formatted = \base\libs\Utils::priceFormat($price_number);
    }

    return \base\libs\Utils::currencyFormat($price_formatted, $current_currency, $output);
}

// Get product price
function get_product_price($price, $currency, $output = null)
{
    return format_price($price, $currency, $output);
}

// Get sale tax price (TODO)
function get_sale_tax_price()
{
    $tax = 0;
    return $tax;
}

// Get shipping price (TODO)
function get_shipping_price()
{
    $price = 0;
    return $price;
}

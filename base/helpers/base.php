<?php
// Check cli mode
function is_cli()
{
    if (php_sapi_name() == "cli") {
        return true;
    }

    return false;
}

// Check url
function is_url($url)
{
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return false;
    }

    return true;
}

// Check email address
function is_email($string)
{
    if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
        return true;
    }

    return false;
}

// Check phone number
function is_phone($string, $country = null)
{
    $strlen = 12;
    $phone = preg_replace("/[^0-9]/", '', $string);
    $fist_3 = substr($phone, 0, 3);

    if (strlen($phone) == $strlen && $fist_3 == 998) {
        return true;
    }

    return false;
}

// Get param
function get_param($key, $default = false)
{
    $params = \Yii::$app->params;

    if (isset($params[$key])) {
        return $params[$key];
    }

    return $default;
}

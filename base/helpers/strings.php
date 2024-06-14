<?php
// Generate random string
function _random_string($type = 'alnum', $len = 8)
{
    switch ($type) {
        case 'alpha':
            $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'alpha-lower':
            $pool = 'abcdefghijklmnopqrstuvwxyz';
            break;
        case 'alnum':
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'alnum-lower':
            $pool = '0123456789abcdefghijklmnopqrstuvwxyz';
            break;
        case 'numeric':
            $pool = '0123456789';
            break;
        case 'nozero':
            $pool = '123456789';
            break;
    }

    return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
}

function _passwordMK($len = 8)
{
    $pool = '123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

    return substr(str_shuffle(str_repeat($pool, ceil($len / strlen($pool)))), 0, $len);
}

// Remove all chars from string
function _remove_all($string, $except = false)
{
    if ($string && $except == 'letters') {
        return preg_replace('/[^a-zA-Z]/', '', $string);
    }

    if ($string && $except == 'numbers') {
        return preg_replace('/[^0-9.]/', '', $string);
    }

    if ($string && $except == 'letters-numbers') {
        return preg_replace('/[^a-zA-Z0-9.]/', '', $string);
    }

    return false;
}

// Remove all spaces from string
function _remove_spaces($string)
{
    $stripped = preg_replace('/\s+/', ' ', $string);

    return preg_replace('/\s/', '', $stripped);
}

// Trim slashes
function _trim_slashes($str)
{
    return trim($str, '/');
}

// String to lower
function _strtolower($str)
{
    return mb_strtolower($str, 'UTF-8');
}

// String to lower
function _strtoupper($str)
{
    return mb_strtoupper($str, 'UTF-8');
}

// String to lower
function _strtotitle($str)
{
    return mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
}

// Obfuscate email address
function _obfuscate_email($email_address = false)
{
    $output = $email_address;
    $explode = explode("@", $email_address);
    $name = array_value($explode, 0);
    $name_fchars = substr($name, 0, 2);
    $name_length = strlen($name);
    $email = array_value($explode, 1);
    $email_fchars = substr($email, 0, 3);
    $email_lchars = substr($email, -3);
    $email_length = strlen($email);

    if ($name_length > 0 && $email_length > 0) {
        $output = $name_fchars . str_repeat('*', ($name_length - 2));
        $output .= '@';
        $output .= $email_fchars . str_repeat('*', ($email_length - 6)) . $email_lchars;
    }

    return $output;
}

// Create slug
function create_slug($string, $replacement = '-', $lowercase = true)
{
    $output = \base\libs\Translit::slug($string, $replacement, $lowercase);
    return $output;
}

// Generate product UPC
function generate_product_upc($length = 10)
{
    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '0123456789';
    $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $numbersLength = strlen($numbers);
    $lettersLength = strlen($letters);
    $randomString = '';
    $exclude = ['YOQ', 'BOR', 'BOQ', 'KOT', 'NUB'];

    if ($length > 6) {
        $string = '';

        for ($i = 0; $i < 3; $i++) {
            $string .= $letters[rand(0, $lettersLength - 1)];
        }

        if (in_array($string, $exclude)) {
            $i = 0;

            do {
                $i++;
                $string = '';

                for ($i = 0; $i < 3; $i++) {
                    $string .= $letters[rand(0, $lettersLength - 1)];
                }
            } while (in_array($string, $exclude) && $i < 100);
        }

        $randomString = $string . "-";

        for ($i = 0; $i < ($length - 3); $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
    } else {
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
    }

    return $randomString;
}


function qrCodeMK($txt, $size = 120, $margin = 5)
{
    $qrCode = (new Da\QrCode\QrCode($txt))
        ->setSize($size)
        ->setMargin($margin);
    return $qrCode->writeDataUri();
}

function fullName($profile = null) {
    if ($profile == null) {
        $profile = current_user_profile();
    }
    $name = getFirstName($profile->first_name);
    $mid_name = getFirstName($profile->middle_name);
    return $name.".".$mid_name.".".$profile->last_name;
}

function profileFullName($profile = null)
{
    if ($profile == null) {
        $profile = current_user_profile();
    }
    $postition = $profile->user->position;
    $name = getFirstName($profile->first_name);
    $mid_name = getFirstName($profile->middle_name);
    return $name.".".$mid_name.".".$profile->last_name.PHP_EOL.$postition.PHP_EOL.date("Y-m-d H:i:s");
}

function getFirstName($name)
{
    if ($name != "") {
        $one = substr($name,0,1);
        if ($one == "s" || $one == "S") {
            $two = substr($name,1,1);
            if ($two == "h" || $two == "H") {
                return substr($name,0,2);
            }
        } elseif ($one == "c" || $one == "C") {
            $two = substr($name,1,1);
            if ($two == "h" || $two == "H") {
                return substr($name,0,2);
            }
        }
        return $one;
    }
}

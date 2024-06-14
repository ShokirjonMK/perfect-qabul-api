<?php

function rawsql($query)
{
    echo '<pre>';
    print_r($query->createCommand()->rawsql);
    echo '</pre>';
    die;
}

function custom_shuffle($my_array = array()) {
    $copy = array();
    while (count($my_array)) {
        // takes a rand array elements by its key
        $element = array_rand($my_array);
        // assign the array and its value to an another array
        $copy[$element] = $my_array[$element];
        //delete the element from source array
        unset($my_array[$element]);
    }
    return $copy;
}

function dd($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
    die;
}

function searchInArray($element, $array) {
    foreach ($array as $item) {
        if ($item == $element) {
            return true;
        }
    }
    return false;
}

// Code debug
function vd($array)
{
    echo '<pre>';
    var_dump($array);
    echo '</pre>';
}

// Code debug
function vdd($array)
{
    echo '<pre>';
    var_dump($array);
    echo '</pre>';
    die;
}
// Code debug
function debug($array)
{
    echo '<pre>';
    print_r($array);
    echo '</pre>';
}

// Get array value
function array_value($array, $key, $default = false, $check_value = false)
{
    if (is_array($array) && isset($array[$key])) {
        $data = $array[$key];

        if ($data) {
            return $data;
        } else {
            return $check_value ? $default : $data;
        }
    }

    return $default;
}

// Filters array by given condition
function filter_array($data, $where, $single_item = false)
{
    $result = [];

    foreach ($data as $row) {
        $condition = true;

        foreach ($where as $field => $value) {
            if ($row[$field] != $value) {
                $condition  = false;
                break;
            }
        }

        if ($condition) {
            $result[] = $row;
        }
    }

    if ($result && $single_item) {
        return array_values($result)[0];
    }

    return $result;
}

// Filters array by given fields
function filter_array_fields($array, $fields)
{
    $result = [];

    foreach ($array as $item) {
        if (is_string($fields)) {
            $result[] = array_value($item, $fields);
        } elseif (is_array($fields)) {
            $field_item = array();

            foreach ($fields as $field) {
                if (isset($item[$field])) {
                    $field_item[$field] = $item[$field];
                }
            }

            $result[] = $field_item;
        }
    }

    return $result;
}

// Array to sql query IN
function array_to_sql_query_in($array)
{
    $results = array();

    if (is_array($array) && $array) {
        foreach ($array as $item) {
            $results[] = "'" . trim($item) . "'";
        }
    }

    return implode(', ', $results);
}

// Item json decode from array by key
function item_json_decode($array, $key, $as_array = true)
{
    $result = array();

    if (is_array($array) && isset($array[$key])) {
        $data = $array[$key];

        if (!is_null($data) && !empty($data)) {
            $result = json_decode($data, $as_array);
        }
    }

    return $result;
}

// Item unserialize from array by key
function item_unserialize($array, $key)
{
    $result = array();

    if (is_array($array) && isset($array[$key])) {
        $data = $array[$key];

        if (!is_null($data) && !empty($data)) {
            $result = unserialize($data);
        }
    }

    return $result;
}

// Get HOST
function get_host()
{
    $host = '';
    $possibleHostSources = array('HTTP_X_FORWARDED_HOST', 'HTTP_HOST', 'SERVER_NAME', 'SERVER_ADDR');
    $sourceTransformations = array(
        "HTTP_X_FORWARDED_HOST" => function ($value) {
            $elements = explode(',', $value);
            return trim(end($elements));
        }
    );

    foreach ($possibleHostSources as $source) {
        if (!empty($host)) {
            break;
        }
        if (empty($_SERVER[$source])) {
            continue;
        }
        $host = $_SERVER[$source];
        if (array_key_exists($source, $sourceTransformations)) {
            $host = $sourceTransformations[$source]($host);
        }
    }

    // Remove port number from host
    $host = preg_replace('/:\d+$/', '', $host);

    return trim($host);
}

// Get OS
function getOS($user_agent = null)
{
    if (!isset($user_agent) && isset($_SERVER['HTTP_USER_AGENT'])) {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
    }

    $os_array = [
        'windows nt 10' => 'Windows 10',
        'windows nt 6.3' => 'Windows 8.1',
        'windows nt 6.2' => 'Windows 8',
        'windows nt 6.1|windows nt 7.0' => 'Windows 7',
        'windows nt 6.0' => 'Windows Vista',
        'windows nt 5.2' => 'Windows Server 2003/XP x64',
        'windows nt 5.1' => 'Windows XP',
        'windows xp' => 'Windows XP',
        'windows nt 5.0|windows nt5.1|windows 2000' => 'Windows 2000',
        'windows me' => 'Windows ME',
        'windows nt 4.0|winnt4.0' => 'Windows NT',
        'windows ce' => 'Windows CE',
        'windows 98|win98' => 'Windows 98',
        'windows 95|win95' => 'Windows 95',
        'win16' => 'Windows 3.11',
        'mac os x 10.1[^0-9]' => 'Mac OS X Puma',
        'macintosh|mac os x' => 'Mac OS X',
        'mac_powerpc' => 'Mac OS 9',
        'linux' => 'Linux',
        'ubuntu' => 'Linux - Ubuntu',
        'iphone' => 'iPhone',
        'ipod' => 'iPod',
        'ipad' => 'iPad',
        'android' => 'Android',
        'blackberry' => 'BlackBerry',
        'webos' => 'Mobile',
        '(media center pc).([0-9]{1,2}\.[0-9]{1,2})' => 'Windows Media Center',
        '(win)([0-9]{1,2}\.[0-9x]{1,2})' => 'Windows',
        '(win)([0-9]{2})' => 'Windows',
        '(windows)([0-9x]{2})' => 'Windows',
        'Win 9x 4.90' => 'Windows ME',
        '(windows)([0-9]{1,2}\.[0-9]{1,2})' => 'Windows',
        'win32' => 'Windows',
        '(java)([0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2})' => 'Java',
        '(Solaris)([0-9]{1,2}\.[0-9x]{1,2}){0,1}' => 'Solaris',
        'dos x86' => 'DOS',
        'Mac OS X' => 'Mac OS X',
        'Mac_PowerPC' => 'Macintosh PowerPC',
        '(mac|Macintosh)' => 'Mac OS',
        '(sunos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'SunOS',
        '(beos)([0-9]{1,2}\.[0-9]{1,2}){0,1}' => 'BeOS',
        '(risc os)([0-9]{1,2}\.[0-9]{1,2})' => 'RISC OS',
        'unix' => 'Unix',
        'os/2' => 'OS/2',
        'freebsd' => 'FreeBSD',
        'openbsd' => 'OpenBSD',
        'netbsd' => 'NetBSD',
        'irix' => 'IRIX',
        'plan9' => 'Plan9',
        'osf' => 'OSF',
        'aix' => 'AIX',
        'GNU Hurd' => 'GNU Hurd',
        '(fedora)' => 'Linux - Fedora',
        '(kubuntu)' => 'Linux - Kubuntu',
        '(ubuntu)' => 'Linux - Ubuntu',
        '(debian)' => 'Linux - Debian',
        '(CentOS)' => 'Linux - CentOS',
        '(Mandriva).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - Mandriva',
        '(SUSE).([0-9]{1,3}(\.[0-9]{1,3})?(\.[0-9]{1,3})?)' => 'Linux - SUSE',
        '(Dropline)' => 'Linux - Slackware (Dropline GNOME)',
        '(ASPLinux)' => 'Linux - ASPLinux',
        '(Red Hat)' => 'Linux - Red Hat',
        'X11' => 'Unix',
        '(linux)' => 'Linux',
        '(amigaos)([0-9]{1,2}\.[0-9]{1,2})' => 'AmigaOS',
        'amiga-aweb' => 'AmigaOS',
        'amiga' => 'Amiga',
        'AvantGo' => 'PalmOS',
        '[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,3})' => 'Linux',
        '(webtv)/([0-9]{1,2}\.[0-9]{1,2})' => 'WebTV',
        'Dreamcast' => 'Dreamcast OS',
        'GetRight' => 'Windows',
        'go!zilla' => 'Windows',
        'gozilla' => 'Windows',
        'gulliver' => 'Windows',
        'ia archiver' => 'Windows',
        'NetPositive' => 'Windows',
        'mass downloader' => 'Windows',
        'microsoft' => 'Windows',
        'offline explorer' => 'Windows',
        'teleport' => 'Windows',
        'web downloader' => 'Windows',
        'webcapture' => 'Windows',
        'webcollage' => 'Windows',
        'webcopier' => 'Windows',
        'webstripper' => 'Windows',
        'webzip' => 'Windows',
        'wget' => 'Windows',
        'Java' => 'Unknown',
        'flashget' => 'Windows',
        'MS FrontPage' => 'Windows',
        '(msproxy)/([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
        '(msie)([0-9]{1,2}.[0-9]{1,2})' => 'Windows',
        'libwww-perl' => 'Unix',
        'UP.Browser' => 'Windows CE',
        'NetAnts' => 'Windows',
    ];

    $arch_regex = '/\b(x86_64|x86-64|Win64|WOW64|x64|ia64|amd64|ppc64|sparc64|IRIX64)\b/ix';
    $arch = preg_match($arch_regex, $user_agent) ? '64' : '32';

    foreach ($os_array as $regex => $value) {
        if (preg_match('{\b(' . $regex . ')\b}i', $user_agent)) {
            return $value . ' x' . $arch;
        }
    }

    return 'Unknown';
}

// Get browser
function getBrowser()
{
    $mob_detect = new \base\libs\MobileDetect();
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browser_name = 'Unknown Browser';
    $platform = 'Unknown OS';
    $version = "";
    $ub = "";

    // First get the platform
    $os_array = array(
        '/windows nt 10/i' => 'Windows 10',
        '/windows nt 6.3/i' => 'Windows 8.1',
        '/windows nt 6.2/i' => 'Windows 8',
        '/windows nt 6.1/i' => 'Windows 7',
        '/windows nt 6.0/i' => 'Windows Vista',
        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
        '/windows nt 5.1/i' => 'Windows XP',
        '/windows xp/i' => 'Windows XP',
        '/windows nt 5.0/i' => 'Windows 2000',
        '/windows me/i' => 'Windows ME',
        '/win98/i' => 'Windows 98',
        '/win95/i' => 'Windows 95',
        '/win16/i' => 'Windows 3.11',
        '/macintosh|mac os x/i' => 'Mac OS X',
        '/mac_powerpc/i' => 'Mac OS 9',
        '/linux/i' => 'Linux',
        '/ubuntu/i' => 'Ubuntu',
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile',
    );

    foreach ($os_array as $regex => $value) {
        if (preg_match($regex, $user_agent)) {
            $platform = $value;
        }
    }

    // Next get the name of the useragent yes seperately and for good reason
    if (preg_match('/MSIE/i', $user_agent) && !preg_match('/Opera/i', $user_agent)) {
        $browser_name = 'Internet Explorer';
        $ub = "MSIE";
    } elseif (preg_match('/Firefox/i', $user_agent)) {
        $browser_name = 'Mozilla Firefox';
        $ub = "Firefox";
    } elseif (preg_match('/OPR/i', $user_agent)) {
        $browser_name = 'Opera';
        $ub = "Opera";
    } elseif (preg_match('/Chrome/i', $user_agent) && !preg_match('/Edge/i', $user_agent)) {
        $browser_name = 'Google Chrome';
        $ub = "Chrome";
    } elseif (preg_match('/Safari/i', $user_agent) && !preg_match('/Edge/i', $user_agent)) {
        $browser_name = 'Apple Safari';
        $ub = "Safari";
    } elseif (preg_match('/Netscape/i', $user_agent)) {
        $browser_name = 'Netscape';
        $ub = "Netscape";
    } elseif (preg_match('/Edge/i', $user_agent)) {
        $browser_name = 'Edge';
        $ub = "Edge";
    } elseif (preg_match('/Trident/i', $user_agent)) {
        $browser_name = 'Internet Explorer';
        $ub = "MSIE";
    }

    // Finally get the correct browser version number
    $known = array('Version', $ub, 'other');
    $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
    preg_match_all($pattern, $user_agent, $matches);

    // see how many we have
    $i = count($matches['browser']);
    if ($i != 1) {
        if (strripos($user_agent, "Version") < strripos($user_agent, $ub)) {
            $version = $matches['version'][0];
        } else {
            $version = $matches['version'][1];
        }
    } else {
        $version = $matches['version'][0];
    }

    // check if we have a number
    if ($version == null || $version == "") {
        $version = "?";
    }

    // check device type
    $device = 'desktop';

    if ($mob_detect->isMobile($user_agent)) {
        $device = 'mobile';
    } elseif ($mob_detect->isTablet($user_agent)) {
        $device = 'tablet';
    }

    return array(
        'user_agent' => $user_agent,
        'browser_name' => $browser_name,
        'browser_version' => $version,
        'platform' => $platform,
        'device' => $device,
        'session' => "{$browser_name} {$version} / {$platform}",
    );
}

// Get IP address
function getIpAddress()
{
    return \Yii::$app->request->getUserIP();
}


// Get IP address
function getIpMK()
{
    $mainIp = '';
    if (getenv('HTTP_CLIENT_IP'))
        $mainIp = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $mainIp = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $mainIp = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_X_CLUSTER_CLIENT_IP'))
        $mainIp = getenv('HTTP_X_CLUSTER_CLIENT_IP');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $mainIp = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $mainIp = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $mainIp = getenv('REMOTE_ADDR');
    else
        $mainIp = 'UNKNOWN';
    return $mainIp;

    $mainIp = '';
    if (getenv('HTTP_CLIENT_IP'))
        $mainIp = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $mainIp = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $mainIp = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_X_CLUSTER_CLIENT_IP'))
        $mainIp = getenv('HTTP_X_CLUSTER_CLIENT_IP');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $mainIp = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $mainIp = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $mainIp = getenv('REMOTE_ADDR');
    else
        $mainIp = 'UNKNOWN';
    return $mainIp;
}

// Get IP address data
function getIpAddressData($ip_address = null)
{
    return null;
    if (is_null($ip_address)) {
        $ip_address = getIpMK();
    }

    if ($ip_address != '127.0.0.1') {
        $url = 'http://demo.ip-api.com/json/' . $ip_address . '?fields=66842623';
        $data = @file_get_contents($url);

        return json_decode($data, true);
    }
}

// Is IP in allowed  List
function checkAllowedIP()
{
    $userIp = getIpMK();

    $ado = '172.25';
    // $ado = '10.1.2';
    $allowedIps = [
        '195.158.3.204',
        // '195.158.24.189',
    ];

    if (in_array($userIp, $allowedIps)) {
        return true;
    } elseif (str_starts_with($userIp, $ado)) {
        return true;
    }

    return false;
}

// Delete files in dir
function delete_files_in_dir($dir)
{
    if (is_dir($dir)) {
        foreach (glob($dir . '/*') as $file) {
            if (is_dir($file)) {
                delete_files_in_dir($file);
            } else {
                unlink($file);
            }
        }

        rmdir($dir);
    }
}

// Select array with empty label
function select_array_with_empty_label($array, $label = '-', $key = '')
{
    $default = array($key => $label);

    if (is_array($array) && $array) {
        return array_merge($default, $array);
    }

    return $default;
}

// Send email
function send_mail($to, $subject, $view, $data = array())
{
    $senderEmail = get_param('senderEmail');
    $senderName = get_param('senderName');

    \Yii::$app->mailer
        ->compose($view, $data)
        ->setFrom([$senderEmail => $senderName])
        ->setTo($to)
        ->setSubject($subject)
        ->send();
}

// Translation
function _t($category, $message, $params = array(), $language = null)
{
    return \Yii::t($category, $message, $params, $language);
}

// Translation app
function _e($message, $params = array())
{
    $translation = \Yii::t('app', $message, $params);

    try {
        return ($translation != $message) ? $translation : \Yii::t('system', $message, $params);
    } catch (\Exception $e) {
        return $translation;
    }
}


// xmlParseToArrayMK
function xmlParseToArrayMK($xml)
{
    try {
        $xmlparser = xml_parser_create();
        xml_parse_into_struct($xmlparser, $xml, $values);
        xml_parser_free($xmlparser);
        return $values;
    } catch (\Exception $exception) {
        // ApiBug::create([
        //     'error' => $exception->getMessage(),
        //     'status_code' => $exception->getCode()
        // ]);
        return  array(
            'status_code' => $exception->getCode(),
            'error' => $exception->getMessage()
        );
    }
}

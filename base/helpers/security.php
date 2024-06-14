<?php
// Clean GET query
function input_get($key, $default = false)
{
    $request = Yii::$app->request;

    if (isset($_GET[$key])) {
        $query = $request->get($key);

        if (is_array($query) && $query) {
            $output = clean_array($query);
        } else {
            $output = clean_str($query);
        }

        return $output;
    }

    return $default;
}

// Clean POST query
function input_post($key, $default = false)
{
    $request = Yii::$app->request;

    if (isset($_POST[$key])) {
        $post = $request->post($key);

        if (is_array($post) && $post) {
            $output = clean_array($post);
        } else {
            $output = clean_str($post);
        }

        return $output;
    }

    return $default;
}

// CSRF input
function csrf_input($as_string = false)
{
    $request = Yii::$app->request;

    $input = '<input type="hidden" name="' . $request->csrfParam . '" value="' . $request->getCsrfToken() . '" />';

    if ($as_string) {
        return $input;
    }

    echo $input;
}

// Validate CSRF token
function validate_csrf()
{
    $request = Yii::$app->request;

    if ($request->validateCsrfToken()) {
        return true;
    }

    return false;
}

// Clean array
function clean_array($array = array())
{
    $output = array();

    if (is_array($array) && $array) {
        foreach ($array as $key => $value) {
            $clear_key = clean_str($key);

            if (is_array($value) && $value) {
                $clear_value = clean_array($value);
            } else {
                $clear_value = clean_str($value);
            }

            $output[$clear_key] = $clear_value;
        }
    }

    return $output;
}

// Strip string
function clean_str($string)
{
    $output = '';

    if ($string) {
        $output = \yii\helpers\Html::encode(strip_tags($string));
    }

    return $output;
}

// Check application config files
function check_app_config_files($config, $type = false)
{
    $app_id = false;
    $app_config_file = dirname(dirname(__DIR__)) . '/config.inc.php';

    if (is_file($app_config_file)) {
        $app_config = include $app_config_file;
        $app_id = array_value($app_config, 'app_id');
    }

    if ($type == 'common') {
        $_config = $config;

        if (isset($_config['components']['session']['cookieParams'])) {
            unset($_config['components']['session']['cookieParams']);
        }

        if (!$app_id) {
            $_config['on beforeRequest'] = function () {
                echo 'The app is still not configured! Please make "yii setup" to set up the application!';
                exit();
            };
        }
    } else {
        $_config = array(
            'id' => $config['id'],
            'basePath' => $config['basePath'],
            'controllerNamespace' => $config['controllerNamespace'],
            'params' => $config['params'],
        );
    }

    if ($app_id) {
        $_config = $config;
    }

    return $_config;
}

// Remove php tags from string
function remove_php_tags($string)
{
    if (is_string($string) && $string) {
        $new_string = trim($string);
        preg_match_all('/<\?(.*?)\?\>/', $new_string, $match);
    
        if ($match[0]) {
            foreach ($match[0] as $php_str) {
                $string = str_replace($php_str, '', $string);
            }
    
            $string = trim($string);
        }
    
        $string = str_replace('<?php', '', $string);
        $string = str_replace('<?', '', $string);
        $string = str_replace('?>', '', $string);
    }

    return $string;
}

// Remove php tags from array
function remove_php_tags_from_array($array)
{
    if (is_array($array) && $array) {
        foreach ($array as &$item) {
            if (is_array($item)) {
                $item = remove_php_tags_from_array($item);
            } else {
                $item = remove_php_tags($item);
            }
        }
    }

    return $array;
}

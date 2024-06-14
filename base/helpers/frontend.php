<?php
// Get parsed URL
function get_parsed_url()
{
    return \base\Container::get('parsed_url');
}

// Get meta value
function get_meta_value($key, $default = '')
{
    $array = \Yii::$app->controller->meta;
    return array_value($array, $key, $default);
}

// Init meta tags
function init_meta_tags($layout)
{
    $output = array();
    $current_url = get_current_url();

    $site_name = get_setting_value('site_name');
    $site_name = trim($site_name);

    $site_charset = get_setting_value('site_charset', \Yii::$app->charset);
    $site_charset = trim($site_charset);

    $seo_title = get_setting_value('seo_title');
    $seo_title = trim($seo_title);

    $seo_separator = get_setting_value('seo_separator', '|');
    $seo_separator = trim($seo_separator);

    $seo_description = get_setting_value('seo_description');
    $seo_description = trim($seo_description);

    // Check charset
    $output[] = '<meta charset="' . $site_charset . '">';
    $output[] = '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    $output[] = '<meta http-equiv="X-UA-Compatible" content="IE=edge">';

    // Check title
    $title = get_meta_value('title');

    if (is_string($layout->title) && $layout->title) {
        $title = trim($layout->title);
    }

    if ($seo_title) {
        $meta_title = str_replace(
            array('[[title]]', '[[separator]]', '[[site_name]]'),
            array($title, $seo_separator, $site_name),
            $seo_title
        );
    } else {
        $meta_title = "{$title} {$seo_separator} {$site_name}";
    }

    $output[] = '<title>' . trim($meta_title) . '</title>';

    // Check disable robots
    $seo_disable_robots = get_setting_value('seo_disable_robots');

    if ($seo_disable_robots == '1') {
        $output[] = '<meta name="robots" content="noindex">';
    } else {
        $output[] = '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">';
    }

    // Check description
    $meta_description = get_meta_value('description', $seo_description);

    if ($meta_description) {
        $output[] = '<meta name="description" content="' . trim($meta_description) . '">';
    }

    // Open Graph
    $output[] = '<meta property="og:locale" content="' . get_current_lang('locale') . '">';
    $output[] = '<meta property="og:type" content="website">';
    $output[] = '<meta property="og:title" content="' . trim($meta_title) . '">';
    if ($meta_description) {
        $output[] = '<meta property="og:description" content="' . trim($meta_description) . '">';
    }
    $output[] = '<meta property="og:url" content="' . site_url($current_url) . '">';
    $output[] = '<meta property="og:site_name" content="' . $site_name . '">';

    // Get canoncial
    $output[] = '<link rel="canonical" href="' . site_url($current_url) . '" />';

    return implode(PHP_EOL, $output);
}

// Get user card hash
function getUserCardHash()
{
    $cookie_hash = false;
    $cookies = Yii::$app->request->cookies;
    $cookie_item = $cookies->getValue('card_hash');

    if ($cookie_item != null) {
        $cookie_hash = $cookie_item;
    }

    return $cookie_hash;
}

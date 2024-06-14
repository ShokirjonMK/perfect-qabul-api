<?php
// Get site theme
function get_site_theme()
{
    $default_theme = \Yii::$app->params['default_theme'];
    $current_theme = get_setting_value('site_theme', $default_theme);

    if (empty($current_theme) || is_null($current_theme)) {
        return $default_theme;
    }

    return $current_theme;
}

// Get theme bundle
function theme_bundle()
{
    $site_theme = get_site_theme();

    if ($site_theme) {
        $theme_path = THEMES_PATH . $site_theme . DS;
        $theme_alias = '@themes/' . $site_theme . '/';

        $app_assets = $theme_alias;
        $app_assets = substr($app_assets, 1);
        $app_assets = str_replace('/', '\\', $app_assets);
        $app_assets = $app_assets . 'app\AppAsset';

        if (is_dir($theme_path)) {
            return Yii::$app->getAssetManager()->getBundle($app_assets);
        }
    }
}

// Get theme assets
function theme_assets($url)
{
    $bundle = theme_bundle();

    if ($bundle) {
        $baseUrl = substr($bundle->baseUrl, 1);
        $baseUrl = site_url($baseUrl);

        return $baseUrl . '/' . trim($url, '/');
    }
}

// Get theme partial
function theme_partial($name = '', $data = array(), $view_as_string = false)
{
    $file_name = trim($name, '/');
    $file_name = str_replace('.php', '', $file_name);
    $partials_path = \Yii::$app->controller->theme_path . 'partials/';
    $partials_alias = \Yii::$app->controller->theme_alias . 'partials/';
    $file = "{$partials_path}{$file_name}.php";

    if (is_file($file)) {
        $file = "{$partials_alias}{$file_name}.php";
        $render_file = \Yii::$app->view->renderFile($file, $data);

        if ($view_as_string) {
            return $render_file;
        } else {
            echo $render_file;
        }
    }
}

// Theme logo image
function theme_logo_image()
{
    $bundle = theme_bundle();
    $image = images_url('default-logo.png');

    if ($bundle) {
        $default_items = isset($bundle->default_items) ? $bundle->default_items : array();
        $default_image = array_value($default_items, 'logo');

        if ($default_image) {
            $image = theme_assets($default_image);
        }
    }

    return get_setting_value('site_logo', $image);
}

// Theme logo image
function theme_favicon_image()
{
    $bundle = theme_bundle();
    $image = images_url('favicon.ico');

    if ($bundle) {
        $default_items = isset($bundle->default_items) ? $bundle->default_items : array();
        $default_image = array_value($default_items, 'favicon');

        if ($default_image) {
            $image = theme_assets($default_image);
        }
    }

    return get_setting_value('site_favicon', $image);
}

// Get theme configs
function theme_configs($filename)
{
    $output = array();
    $site_theme = get_site_theme();

    if ($site_theme && $filename) {
        $filename = trim($filename);
        $filename = trim($filename, '/');
        $theme_path = THEMES_PATH . $site_theme . DS;
        $theme_alias = '@themes/' . $site_theme . '/';
        $theme_config = $theme_path . 'app/configs/' . $filename . '.php';

        if (is_file($theme_config)) {
            $output = include $theme_config;
        }
    }

    return $output;
}

// Get content editor fields
function content_editor_fields()
{
    $site_theme = get_site_theme();
    $output = array(
        'sections' => array(),
        'groups' => array(),
        'elements' => array(),
    );

    if ($site_theme) {
        $theme_path = THEMES_PATH . $site_theme . DS;
        $theme_alias = '@themes/' . $site_theme . '/';
        $theme_fields_path = $theme_path . 'app/content-editor/';
        $theme_fields_path = str_replace('/', DS, $theme_fields_path);

        if (is_file($theme_fields_path . 'sections.php')) {
            $sections = include $theme_fields_path . 'sections.php';

            if (is_array($sections) && $sections) {
                $output['sections'] = $sections;
            }
        }

        if (is_file($theme_fields_path . 'groups.php')) {
            $groups = include $theme_fields_path . 'groups.php';

            if (is_array($groups) && $groups) {
                $output['groups'] = $groups;
            }
        }

        if (is_file($theme_fields_path . 'elements.php')) {
            $elements = include $theme_fields_path . 'elements.php';

            if (is_array($elements) && $elements) {
                $output['elements'] = $elements;
            }
        }
    }

    return $output;
}

// Get content editor sections
function content_editor_sections($blocks)
{
    $content = '';
    $path_name = 'ce-sections';

    if (is_array($blocks) && $blocks) {
        foreach ($blocks as $block) {
            $block_key = array_value($block, 'section_key');

            $data['section'] = new \stdClass();
            $data['elements'] = array_value($block, 'elements');

            foreach ($block as $key => $value) {
                $data['section']->$key = $value;
            }

            $content .= theme_partial("{$path_name}/$block_key", $data, true);
        }
    }

    return $content;
}

// Get content editor elements
function content_editor_elements($element)
{
    $content = '';
    $path_name = 'ce-elements';

    if (is_array($element) && $element) {
        $html_types = ['tinymce', 'html'];
        $element_type = array_value($element, 'type');
        $element_items = array_value($element, 'items');

        if ($element_type == 'multi') {
            foreach ($element_items as $item) {
                $content .= content_editor_elements($item);
            }
        } else {
            $element_key = array_value($element, 'key');
            $element_url = array_value($element, 'url');
            $element_data = array_value($element, 'data');
            $element_embed = array_value($element, 'embed');
            $element_html = array_value($element, 'html');

            if (in_array($element_type, $html_types)) {
                $content = $element_html;
            } elseif ($element_key == 'html_code') {
                $content = array_value($element, 'value');
            } elseif ($element_type == 'audio') {
                if ($element_url) {
                    $content = '<div class="element-audio">';
                    $content .= '<audio controls>';
                    $content .= '<source src="' . $element_url . '" type="audio/mpeg">';
                    $content .= '</audio>';
                    $content .= '</div>';
                }
            } elseif ($element_type == 'video') {
                if ($element_url) {
                    $content = '<div class="element-video">';
                    $content .= '<video controls>';
                    $content .= '<source src="' . $element_url . '" type="video/mp4">';
                    $content .= '</video>';
                    $content .= '</div>';
                }
            } elseif ($element_embed) {
                $content = '<div class="element-embed">';
                $content .= $element_html;
                $content .= '</div>';
            } else {
                $element_data_array = array();

                if ($element_data) {
                    foreach ($element_data as $element_data_key => $element_data_item) {
                        $element_data_array[] = $element_data_key . '="' . $element_data_item . '"';
                    }

                    $element['attributes'] = implode(' ', $element_data_array);
                }

                $element['element_array'] = $element;
                $content = theme_partial("{$path_name}/{$element_type}", $element, true);
            }
        }
    }

    return $content;
}

//
function content_editor_section_items($array) 
{
    $output = array();

    if (is_array($array) && $array) {
        foreach ($array as $array_item) {
            $type = array_value($array_item, 'type');
            $items = array_value($array_item, 'items');

            if ($type == 'multi' && is_array($items) && $items) {
                foreach ($items as $item) {
                    $item_key = array_value($item, 'key');
                    $output[$item_key] = $item;
                }
            }
        }
    }

    return $output;
}
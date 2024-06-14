<?php
// Get active languages
function admin_active_langs($hide_active = false)
{
    $output = array();
    $array = \base\Backend::language('list');

    if ($array) {
        $hide_id = 0;

        if ($hide_active) {
            $current_lang = admin_current_lang();
            $current_lexicon = admin_content_lexicon();

            if ($current_lexicon && $hide_active == 'content_lexicon') {
                $hide_id = $current_lexicon['id'];
            } elseif ($current_lang) {
                $hide_id = $current_lang['id'];
            }

            foreach ($array as $key => $value) {
                if ($value['id'] != $hide_id) {
                    $output[$key] = $value;
                }
            }
        } else {
            $output = $array;
        }
    }

    return $output ? $output : array();
}

// Get admin page languages
function admin_area_langs()
{
    return array(
        'en' => [
            'name' => 'English',
            'lang_code' => 'en',
            'locale' => 'en_GB',
            'rtl' => '0',
            'flag' => images_url('flags/svg/en.svg'),
        ],
        'ru' => [
            'name' => 'Русский',
            'lang_code' => 'ru',
            'locale' => 'ru_RU',
            'rtl' => '0',
            'flag' => images_url('flags/svg/ru.svg'),
        ],
        'uz' => [
            'name' => 'O\'zbekcha',
            'lang_code' => 'uz',
            'locale' => 'uz_UZ',
            'rtl' => '0',
            'flag' => images_url('flags/svg/uz.svg'),
        ],
    );
}

// Get current language
function admin_current_lang($get = null)
{
    $array = \base\Backend::language('current');

    if (is_null($get)) {
        return $array;
    }

    return array_value($array, $get);
}

// Get current content language
function admin_content_lexicon($get = null)
{
    $array = \base\Backend::language('content');

    if (is_null($get)) {
        return $array;
    }

    return array_value($array, $get);
}

// Set log
function set_log($type, $args)
{
    if ($type == 'admin') {
        \common\models\Logs::setAdminLog($args);
    } elseif ($type == 'seller') {
        \common\models\Logs::setSellerLog($args);
    }
}

// Set trash
function set_trash($args)
{
    if (is_array($args) && $args) {
        \common\models\Trashbox::setItem($args);
    }
}

// Create temp for
function create_temp_for($type)
{
    $temp = new \base\libs\Temp();
    $temp->createFor($type);
}

// Field slug input
function field_slug_input($tooltip = null, $url = null)
{
    if (is_null($tooltip)) {
        $tooltip = _e('Clear');
    }

    $template = '{label}';
    $template .= '<div class="input-group">';
    $template .= '{input}';

    if (is_string($url) && $url) {
        $template .= '<div class="input-group-append c-pointer">';
        $template .= '<a href="' . $url . '" target="_blank" class="input-group-text" data-toggle="tooltip" data-placement="top" title="' . _e('Open on the site') . '">';
        $template .= '<i class="ri-external-link-line"></i>';
        $template .= '</a>';
        $template .= '</div>';
    }

    $template .= '<div class="input-group-append input-on-slug-eraser c-pointer">';
    $template .= '<span class="input-group-text" data-toggle="tooltip" data-placement="top" title="' . $tooltip . '">';
    $template .= '<i class="ri-eraser-fill"></i>';
    $template .= '</span>';
    $template .= '</div>';
    $template .= '</div>';

    return $template;
}

// Field with tooltip label
function field_with_tooltip_label($tooltip, $place = 'right')
{
    $template = '<div class="label">
        <span data-toggle="tooltip" data-placement="' . $place . '" title="' . $tooltip . '">
            {label} <i class="ri-question-line" style="font-size: 12px;"></i>
        </span>
    </div>
    {input}';

    return $template;
}

// Field with append group
function field_with_append_group($text)
{
    $template = '{label}';
    $template .= '<div class="input-group">';
    $template .= '{input}';
    $template .= '<div class="input-group-append">';
    $template .= '<div class="input-group-text">' . $text . '</div>';
    $template .= '</div>';
    $template .= '</div>';

    return $template;
}

// Settings item form input field
function settings_item_form_input_field($item, $args = array())
{
    $active_lang = array_value($args, 'active_lang');
    $active_lang_key = array_value($active_lang, 'lang_code');

    if ($item->required == 1) {
        $required = 'required="required"';
    } else {
        $required = '';
    }

    if ($active_lang_key) {
        $label = $item->title . ' [' . $active_lang_key . ']';
        $input_name = 'name="settings_translation[' . $active_lang_key . '][' . $item->settings_key . ']"';
        $input_value = $item->translation ? $item->translation : $item->settings_value;
    } else {
        $label = $item->title;
        $input_name = 'name="settings[' . $item->settings_key . ']"';
        $input_value = $item->settings_value;
    }

    $output = '<label for="' . $item->settings_key . '">' . $label . '</label>';
    $output .= '<input type="' . $item->settings_type . '" class="form-control" ' . $input_name . ' id="' . $item->settings_key . '" value="' . $input_value . '" ' . $required . '>';

    return $output;
}

// Settings item form textarea field
function settings_item_form_textarea_field($item, $args = array())
{
    $active_lang = array_value($args, 'active_lang');
    $active_lang_key = array_value($active_lang, 'lang_code');
    $rows = array_value($args, 'rows', '3');

    if ($item->required == 1) {
        $required = 'required="required"';
    } else {
        $required = '';
    }

    if ($active_lang_key) {
        $label = $item->title . ' [' . $active_lang_key . ']';
        $input_name = 'name="settings_translation[' . $active_lang_key . '][' . $item->settings_key . ']"';
        $input_value = $item->translation ? $item->translation : $item->settings_value;
    } else {
        $label = $item->title;
        $input_name = 'name="settings[' . $item->settings_key . ']"';
        $input_value = $item->settings_value;
    }

    $output = '<label for="' . $item->settings_key . '">' . $label . '</label>';
    $output .= '<textarea class="form-control" ' . $input_name . ' id="' . $item->settings_key . '" rows="' . $rows . '" ' . $required . '>' . $input_value . '</textarea>';


    return $output;
}

// Settings item form select field
function settings_item_form_select_field($item, $array, $args = array())
{
    $active_lang = array_value($args, 'active_lang');
    $active_lang_key = array_value($active_lang, 'lang_code');

    $value_as_key = array_value($args, 'value_as_key');
    $class = array_value($args, 'class', 'form-control custom-select');


    if ($item->required == 1) {
        $required = 'required="required"';
    } else {
        $required = '';
    }

    if ($active_lang_key) {
        $label = $item->title . ' [' . $active_lang_key . ']';
        $sname = 'name="settings_translation[' . $active_lang_key . '][' . $item->settings_key . ']"';
    } else {
        $label = $item->title;
        $sname = 'name="settings[' . $item->settings_key . ']"';
    }

    $output = '<label for="' . $item->settings_key . '">' . $label . '</label>';

    if ($array) {
        $output .= '<select class="' . $class . '" ' . $sname . '] id="' . $item->settings_key . '" ' . $required . '>';

        foreach ($array as $key => $value) {
            if ($value_as_key) {
                $item_key = $value;
                $item_value = $key;
            } else {
                $item_key = $key;
                $item_value = $value;
            }

            if ($item_key == $item->settings_value) {
                $output .= '<option value="' . $item_key . '" selected>' . $item_value . '</option>';
            } else {
                $output .= '<option value="' . $item_key . '">' . $item_value . '</option>';
            }
        }

        $output .= '</select>';
    }

    return $output;
}

// Settings item form select field
function settings_item_form_file_field($item, $args = array())
{
    $active_lang = array_value($args, 'active_lang');
    $active_lang_key = array_value($active_lang, 'lang_code');

    if ($item->required == 1) {
        $required = 'required="required"';
    } else {
        $required = '';
    }

    if ($active_lang_key) {
        $label = $item->title . ' [' . $active_lang_key . ']';
        $sname = 'settings_translation[' . $active_lang_key . '][' . $item->settings_key . ']';
    } else {
        $label = $item->title;
        $sname = 'settings[' . $item->settings_key . ']';
    }

    $output = \backend\widgets\StorageWidget::widget([
        'label' => $label,
        'format' => 'image',
        'select_type' => 'single',
        'action' => 'settings_' . $item->settings_key,
        'input' => array(
            'name' => $sname,
            'attrs' => $required,
            'value' => $item->settings_value,
        ),
    ]);

    return $output;
}

// Init content settings
function init_content_settings($model)
{
    $output = $model;

    if ($model) {
        $settings = $model->settings;

        if ($settings) {
            foreach ($settings as $key => $value) {
                $output->$key = $value;
            }
        }
    }

    return $output;
}

// Init content meta
function init_content_meta($info)
{
    $output = $info;

    if ($info) {
        $settings = $info->meta;

        if ($settings) {
            foreach ($settings as $key => $value) {
                $output->$key = $value;
            }
        }
    }

    return $output;
}

// Count content childs
function count_content_childs($model, $field_key)
{
    $count = \common\models\ContentFields::find()
        ->where(['content_id' => $model->id, 'field_key' => $field_key])
        ->one();

    if ($count && is_numeric($count->field_value)) {
        return $count->field_value;
    }

    return '0';
}

// Count segment childs
function count_segment_childs($model, $field_key)
{
    $count = \common\models\SegmentFields::find()
        ->where(['segment_id' => $model->id, 'field_key' => $field_key])
        ->one();

    if ($count && is_numeric($count->field_value)) {
        return $count->field_value;
    }

    return '0';
}

// Backend sidebar menu items
function backend_sidebar_menu_items()
{
    $array = array(
        'dashboard' => array(
            'url' => '/',
            'name' => _e('Dashboard'),
            'sort' => 0,
            'icon' => '<i class="ri-dashboard-line"></i>',
            'active_menu' => ['dashboard'],
            'childs' => array(),
        ),

        'menu_title_contet' => array(
            'url' => '#',
            'name' => _e('Content'),
            'menu_title' => true,
            'sort' => 200,
            'icon' => '',
            'active_menu' => [],
            'childs' => []
        ),
        'post' => array(
            'url' => '#',
            'name' => _e('Posts'),
            'sort' => 210,
            'icon' => '<i class="ri-pen-nib-line"></i>',
            'active_menu' => ['post', 'content/post', 'segment/post-tag', 'segment/post-category'],
            'childs' => array(
                array(
                    'url' => '/content/post/create',
                    'name' => _e('Add new'),
                ),
                array(
                    'url' => '/content/post/all',
                    'name' => _e('All posts'),
                ),
                array(
                    'url' => '/segment/post-tag/all',
                    'name' => _e('Tags'),
                ),
                array(
                    'url' => '/segment/post-category/all',
                    'name' => _e('Categories'),
                ),
            ),
        ),
        'page' => array(
            'url' => '#',
            'name' => _e('Pages'),
            'sort' => 220,
            'icon' => '<i class="ri-checkbox-multiple-blank-line"></i>',
            'active_menu' => ['page', 'content/page'],
            'childs' => array(
                array(
                    'url' => '/content/page/create',
                    'name' => _e('Add new'),
                ),
                array(
                    'url' => '/content/page/all',
                    'name' => _e('All pages'),
                ),
            )
        ),
        'design' => array(
            'url' => '#',
            'name' => _e('Design'),
            'sort' => 230,
            'icon' => '<i class="ri-brush-2-fill"></i>',
            'active_menu' => ['appearance'],
            'childs' => array(
                array(
                    'url' => '/appearance/menus',
                    'name' => _e('Menus'),
                ),
                array(
                    'url' => '/appearance/themes',
                    'name' => _e('Themes'),
                ),
                array(
                    'url' => '/appearance/widgets',
                    'name' => _e('Widgets'),
                ),
            )
        ),
        'storage' => array(
            'url' => '#',
            'name' => _e('Storage'),
            'sort' => 240,
            'icon' => '<i class="ri-folder-3-line"></i>',
            'active_menu' => ['storage'],
            'childs' => array(
                array(
                    'url' => '/storage/files',
                    'name' => _e('Files'),
                ),
                array(
                    'url' => '/storage/images?view=grid',
                    'name' => _e('Images'),
                ),
                array(
                    'url' => '/storage/uploads',
                    'name' => _e('Uploads'),
                ),
            )
        ),

        'system' => array(
            'url' => '#',
            'name' => _e('Administration'),
            'sort' => 10,
            'icon' => '<i class="ri-settings-3-line"></i>',
            'active_menu' => ['system'],
            'childs' => array(
                array(
                    'url' => '/users',
                    'name' => _e('User'),
                ),
                array(
                    'url' => '/buildings',
                    'name' => _e('Building'),
                ),
                array(
                    'url' => '/system/roles',
                    'name' => _e('Roles & permissions'),
                ),
                array(
                    'url' => '/system/settings',
                    'name' => _e('Settings'),
                ),
                array(
                    'url' => '/system/languages',
                    'name' => _e('Languages'),
                ),
                array(
                    'url' => '/system/translations',
                    'name' => _e('Translations'),
                ),

            )
        ),

        'menu_title_modules' => array(
            'url' => '#',
            'name' => _e('Modules'),
            'menu_title' => true,
            'sort' => 100,
            'icon' => '',
            'active_menu' => [],
            'childs' => []
        ),
        'directories' => array(
            'url' => '#',
            'name' => _e('Directories'),
            'sort' => 190,
            'icon' => '<i class="fas fa-database"></i>',
            'active_menu' => ['directories'],
            'childs' => array(
                array(
                    'url' => '/directories/universities',
                    'name' => _e('Universities'),
                ),
                array(
                    'url' => '/directories/specialities',
                    'name' => _e('Specialities'),
                ),
                array(
                    'url' => '/system/locations',
                    'name' => _e('Locations'),
                ),
                array(
                    'url' => '/directories/references/nationality/all',
                    'name' => _e('Nationalities'),
                ),
                array(
                    'url' => '/directories/references/residence-type/all',
                    'name' => _e('Residence types'),
                ),
                array(
                    'url' => '/directories/references/language/all',
                    'name' => _e('Languages'),
                ),
                array(
                    'url' => '/directories/references/science-degree/all',
                    'name' => _e('Science degrees'),
                ),
                array(
                    'url' => '/directories/references/scientific-title/all',
                    'name' => _e('Scientific titles'),
                ),
                array(
                    'url' => '/directories/references/special-title/all',
                    'name' => _e('Special titles'),
                ),
                array(
                    'url' => '/directories/references/basis-of-learning/all',
                    'name' => _e('Basis of learning'),
                ),
            )
        ),
        'hr' => array(
            'url' => '#',
            'name' => _e('HR'),
            'sort' => 120,
            'icon' => '<i class="fas fa-briefcase"></i>',
            'active_menu' => ['hr'],
            'childs' => array(
                array(
                    'url' => '/hr/employees',
                    'name' => _e('Employees'),
                ),
                array(
                    'url' => '/hr/departments',
                    'name' => _e('Departments'),
                ),
                array(
                    'url' => '/hr/jobs',
                    'name' => _e('Jobs'),
                ),
                array(
                    'url' => '/hr/ranks',
                    'name' => _e('Ranks'),
                ),
            )
        ),
        'education' => array(
            'url' => '#',
            'name' => _e('Educational process'),
            'sort' => 125,
            'icon' => '<i class="fas fa-book-open"></i>',
            'active_menu' => ['education'],
            'childs' => array(
                array(
                    'url' => '/education/students',
                    'name' => _e('Students'),
                ),
                array(
                    'url' => '/education/directions',
                    'name' => _e('Directions'),
                ),
                array(
                    'url' => '/education/subjects',
                    'name' => _e('Subjects'),
                ),
                array(
                    'url' => '/users/subjects',
                    'name' => _e('Teacher subjects'),
                ),
                array(
                    'url' => '/education/subject-topics',
                    'name' => _e('Topics of subjects'),
                ),
            )
        ),
        'questions_base' => array(
            'url' => '#',
            'name' => _e('...'),
            'sort' => 130,
            'icon' => '<i class="ri-checkbox-multiple-blank-line"></i>',
            'active_menu' => ['questions_base'],
            'childs' => []
        ),

    );

    return $array;
}

// Backend sidebar menu
function backend_sidebar_menu()
{
    $array = backend_sidebar_menu_items();
    $current_url = get_current_url();

    if (is_array($array) && $array) {
        $theme_admin_menu = theme_configs('admin_menu');

        if ($theme_admin_menu) {
            $array = array_merge($theme_admin_menu, $array);

            usort($array, function ($a, $b) {
                return $a['sort'] - $b['sort'];
            });
        }

        foreach ($array as &$item) {
            $item['active_link'] = false;
            $item_url = array_value($item, 'url');
            $active_menu = array_value($item, 'active_menu');
            $childs = array_value($item, 'childs');

            if (check_sidebar_menu_active_link($item_url, $current_url)) {
                $item['active_link'] = true;
            }

            if (check_sidebar_menu_active_link($active_menu, $current_url)) {
                $item['active_link'] = true;
            }

            if ($childs) {
                foreach ($childs as $key => $child_item) {
                    $child_item['active_link'] = false;
                    $child_item_url = array_value($child_item, 'url');

                    if (check_sidebar_menu_active_link($child_item_url, $current_url)) {
                        $item['active_link'] = true;
                        $child_item['active_link'] = true;
                    }

                    $item['childs'][$key] = $child_item;
                }
            }
        }
    }

    return $array;
}

// Check sidebar menu active link
function check_sidebar_menu_active_link($link, $current_url)
{
    $output = false;

    if ($link && $current_url) {
        $current_url = trim($current_url);
        $current_url = trim($current_url, '/');

        if (is_string($link) && $link != '/') {
            $link = trim($link);
            $link = trim($link, '/');
            $link_length = strlen($link);
            $current_url_str = substr($current_url, 0, $link_length);

            if ($link == $current_url) {
                $output = true;
            }

            if ($link == $current_url_str) {
                $output = true;
            }
        } elseif (is_array($link)) {
            foreach ($link as $item) {
                $item = trim($item);
                $item = trim($item, '/');
                $item_length = strlen($item);
                $current_url_str = substr($current_url, 0, $item_length);

                if ($item == $current_url) {
                    $output = true;
                }

                if ($item == $current_url_str) {
                    $output = true;
                }
            }
        }
    }

    return $output;
}

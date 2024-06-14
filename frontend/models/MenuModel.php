<?php

namespace frontend\models;

use common\models\Content;
use Yii;
use common\models\ContentInfos;
use common\models\MenuGroup;
use common\models\MenuItems;
use common\models\Segment;
use common\models\SegmentInfos;

/**
 * Menu Model
 */
class MenuModel
{
    public static $folder;

    /**
     * Get menu
     *
     * @param string $menu_key
     * @param array $args
     * @return void
     */
    public static function get($menu_key, $args = array())
    {
        $output = '';
        $items = self::getItems($menu_key, $args);

        $menu_key_prefix = str_replace('_', '-', $menu_key);
        $menu_key_prefix = ltrim($menu_key_prefix, 'nav-');

        self::$folder = array_value($args, 'folder');
        $menu_id = array_value($args, 'menu_id', 'nav-' . $menu_key_prefix);
        $menu_class = array_value($args, 'menu_class', 'nav nav-' . $menu_key_prefix);

        if ($items) {
            $array = self::buildTree($items, 0);

            if ($array) {
                $items_html = self::drawItems($menu_key, $array);

                $data = array(
                    'menu_id' => $menu_id,
                    'menu_class' => $menu_class,
                    'wrapper' => $items_html,
                );

                $output = self::outer_view($menu_key, $data);
            }
        }

        return $output;
    }

    /**
     * Get menu items
     *
     * @param string $menu_key
     * @param array $args
     * @return array
     */
    public static function getItems($menu_key, $args = array())
    {
        $results = array();
        $menu_key = clean_str($menu_key);

        $current_language = get_current_lang();
        $language = array_value($args, 'language', $current_language);

        if (is_string($menu_key) && !empty($menu_key)) {
            $results = MenuItems::find()
                ->alias('items')
                ->join('INNER JOIN', 'menu_group mgr', 'items.group_key = mgr.group_key')
                ->where(['mgr.group_key' => $menu_key, 'items.language' => $language])
                ->andWhere(['mgr.status' => 1, 'mgr.deleted' => 0])
                ->orderBy('items.sort ASC')
                ->asArray()
                ->all();
        }

        return $results;
    }

    /**
     * Build array items
     *
     * @param array $elements
     * @param integer $parent
     * @param integer $level
     * @return mixed
     */
    private static function buildTree(array &$elements, $parent = 0, $level = 0)
    {
        $level++;
        $output = array();
        $language = get_current_lang();
        $current_url = get_current_url();

        foreach ($elements as &$element) {
            $id = $element['id'];
            $parent_id = $element['parent_id'];

            if ($parent_id == $parent) {
                $item = self::getMenuItem($element, $language, $current_url);
                $snippet = MenuSnippets::init($item);
                $snippet_childs = array_value($snippet, 'childs', array());
                $children = self::buildTree($elements, $id, $level);
                $childs = array_merge($snippet_childs, $children);

                if ($childs) {
                    $item['has_childs'] = true;
                    $item['childs'] = $childs;
                } else {
                    $item['childs'] = array();
                }

                $output[$id] = $item;
                unset($element);
            }
        }

        return $output;
    }

    /**
     * Get menu item
     *
     * @param array $element
     * @return mixed
     */
    private static function getMenuItem($element, $language, $current_url)
    {
        if ($element) {
            $output = array();
            $item = (array) $element;
            $item_id = array_value($item, 'item_id');
            $item_data = array_value($item, 'data');
            $type = array_value($item, 'type');
            $walked_id_array = \base\Container::push('frontend_walked_id_array');

            $data = array();
            $item['has_childs'] = false;
            $item['query'] = array();

            if (!is_null($item_data) && !empty($item_data)) {
                $data = json_decode($item_data, true);
                $output = $data;
                unset($item['data']);
            }

            if (is_numeric($item_id) && $item_id > 0 && !empty($type)) {
                $item_data = array();
                $content_types = Content::contentTypes();
                $segment_types = Segment::segmentTypes();

                $content_item = filter_array($content_types, ['slug' => $type], true);
                $segment_item = filter_array($segment_types, ['slug' => $type], true);

                if ($type == 'page' || $content_item) {
                    $item_data = ContentInfos::find()
                        ->alias('info')
                        ->join('INNER JOIN', 'site_content content', 'content.id = info.content_id')
                        ->where(['content.id' => $item_id, 'info.language' => $language])
                        ->with('model')
                        ->one();

                    if ($item_data) {
                        $item['name'] = $item_data->title;
                        $item['link'] = get_content_url($item_data);
                    }
                } elseif ($segment_item) {
                    $item_data = SegmentInfos::find()
                        ->alias('info')
                        ->join('INNER JOIN', 'site_segment segment', 'segment.id = info.segment_id')
                        ->where(['segment.id' => $item_id, 'info.language' => $language])
                        ->with('model')
                        ->one();

                    if ($item_data) {
                        $item['name'] = $item_data->title;
                        $item['link'] = get_segment_url($item_data);
                    }
                }

                $resource_type = false;
                $set_active_class = false;

                if (isset($item_data->model->resource_type)) {
                    $resource_type = $item_data->model->resource_type;
                }

                if ($walked_id_array && in_array($item_id, $walked_id_array)) {
                    $set_active_class = true;
                } elseif ($current_url == $item['link']) {
                    $set_active_class = true;
                } elseif (($current_url == site_url() || $current_url == site_url(true)) && $resource_type == 'home_page') {
                    $set_active_class = true;
                }

                if ($set_active_class) {
                    $output['class_name'] = $output['class_name'] . ' active';
                    $output['class_name'] = trim($output['class_name']);
                }

                $item['query'] = $item_data;
            }

            return array_merge($output, $item);
        }
    }

    /**
     * Undocumented function
     *
     * @param string $menu_key
     * @param array $elements
     * @param integer $parent
     * @param integer $level
     * @return mixed
     */
    private static function drawItems($menu_key, array &$elements, $parent = 0, $level = 0)
    {
        $level++;
        $output = '';

        foreach ($elements as &$element) {
            if ($element['parent_id'] == $parent) {
                $children = array();

                if (isset($element['childs'])) {
                    $children = $element['childs'];
                    unset($element['childs']);
                }

                $data = $element;
                $data['array'] = $element;

                if ($children) {
                    $childs = self::drawItems($menu_key, $children, $element['id'], $level);
                    $childs_data = $data;
                    $childs_data['wrapper'] = $childs;

                    $data['wrapper'] = self::inner_view($menu_key, $level, $childs_data);
                } else {
                    $data['wrapper'] = false;
                }

                $output .= self::item_view($menu_key, $level, $data);
                unset($element);
            }
        }

        return $output;
    }

    /**
     * Menu outer view
     *
     * @param string $menu_key
     * @param array $data
     * @return mixed
     */
    private static function outer_view($menu_key, $data = array())
    {
        $folder = self::$folder;
        $folder_name = $menu_key;

        if ($folder) {
            $folder = trim($folder, '/');
            $folder_name = trim($folder);
        }

        $file_name = "menu/{$folder_name}/outer";
        return theme_partial($file_name, $data, true);
    }

    /**
     * Menu item item
     *
     * @param string $menu_key
     * @param integer $level
     * @param array $data
     * @return mixed
     */
    private static function item_view($menu_key, $level, $data = array())
    {
        $level = ($level - 1);
        $folder = self::$folder;
        $folder_name = $menu_key;

        if ($folder) {
            $folder = trim($folder, '/');
            $folder_name = trim($folder);
        }

        $file_name = "menu/{$folder_name}/item-{$level}";
        $theme_file = theme_partial($file_name, $data, true);

        if ($theme_file) {
            return $theme_file;
        } else {
            $file_name = "menu/{$folder_name}/item";
            return theme_partial($file_name, $data, true);
        }
    }

    /**
     * Menu inner item
     *
     * @param string $menu_key
     * @param integer $level
     * @param array $data
     * @return mixed
     */
    private static function inner_view($menu_key, $level, $data = array())
    {
        $level = ($level - 1);
        $folder = self::$folder;
        $folder_name = $menu_key;

        if ($folder) {
            $folder = trim($folder, '/');
            $folder_name = trim($folder);
        }

        $file_name = "menu/{$folder_name}/inner-{$level}";
        $theme_file = theme_partial($file_name, $data, true);

        if ($theme_file) {
            return $theme_file;
        } else {
            $file_name = "menu/{$folder_name}/inner";
            return theme_partial($file_name, $data, true);
        }
    }
}

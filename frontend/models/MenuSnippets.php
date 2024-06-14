<?php

namespace frontend\models;

use common\models\Segment;

/**
 * Menu Snippets Model
 */
class MenuSnippets
{
    private $args;
    private $depth;
    private $item;

    /**
     * Construction
     *
     * @param array $item
     */
    public function __construct($item)
    {
        $this->depth = 0;
        $this->item = $item;
        $this->args = array();
    }

    /**
     * Init model
     *
     * @param array $item
     * @return array
     */
    public static function init($item)
    {
        if (is_array($item) && $item) {
            $snippet = array_value($item, 'snippet');
            $class = new MenuSnippets($item);
            $snippet = $class->parseSnippet($snippet);

            if ($snippet) {
                if (method_exists($class, $snippet)) {
                    return $class->$snippet();
                }
            }
        }
    }

    /**
     * Get categories
     *
     * @return array
     */
    public function getCategories()
    {
        $item = $this->item;
        $item_id = array_value($item, 'id', 0);
        $language = array_value($item, 'language', 0);

        $args = $this->args;
        $depth = array_value($args, 'depth', 0);
        $parent_id = array_value($args, 'parent', $item_id);

        if (is_numeric($depth) && $depth > 0) {
            $this->depth = $depth;
        }

        $categories = Segment::find()
            ->alias('segment')
            ->join('INNER JOIN', 'site_segment_info info', 'segment.id = info.segment_id')
            ->where(['segment.id' => $parent_id, 'info.language' => $language])
            ->sortBy('segment.sort ASC, info.title ASC')
            ->limit(100)
            ->with('info')
            ->all();

        if ($categories) {
            $build_data = array(
                'type' => 'product_category',
                'args' => $args,
            );

            $categories = $this->buildChilds($build_data, $categories, 0);
            $this->item['childs'] = $this->buildTree($item, $categories, 0, 0);
        }

        return $this->item;
    }

    private function buildChilds($data, $elements, $level = 0)
    {
        $output = array();
        $level = ($level - 1) + 1;
        $type = array_value($data, 'type');
        $args = array_value($data, 'args');

        if ($elements) {
            foreach ($elements as &$element) {
                $id = $element['id'];
                $childs = array();

                if ($level < $this->depth) {
                    if ($type == 'product_category') {
                        $args['parent_id'] = $id;
                        $childs = \frontend\models\CategoryModel::getMany($args);
                    }
                }

                if ($childs) {
                    $element['childs'] = $this->buildChilds($data, $childs, $level);
                } else {
                    $element['childs'] = array();
                }

                $output[] = $element;
            }
        }

        return $output;
    }

    private function buildTree($item, array &$elements, $parent = 0, $level = 0)
    {
        $level++;
        $output = array();
        $item_id = array_value($item, 'id', 0);
        $group_key = array_value($item, 'group_key', 0);

        if ($elements) {
            foreach ($elements as &$element) {
                $id = $element['id'];
                $parent_id = $element['parent_id'];

                if ($parent_id < 1) {
                    $element['parent_id'] = $item_id;
                }

                if ($parent_id == $parent) {
                    $children = $element['childs'];
                    unset($element['childs']);

                    $menu_item = array();
                    $type = array_value($element, 'type', 0);

                    foreach ($item as $_key => $_item) {
                        if (isset($element[$_key])) {
                            $menu_item[$_key] = $element[$_key];
                        } else {
                            $menu_item[$_key] = '';
                        }
                    }

                    $menu_item['name'] = $element['title'];
                    $menu_item['item_id'] = $element['id'];
                    $menu_item['group_key'] = $group_key;
                    $menu_item['menu_type'] = 'simple-menu';
                    $menu_item['type'] = $type;

                    if ($type == 'product_category') {
                        $menu_item['link'] = get_segment_url($element);
                    } else {
                        $menu_item['link'] = '#';
                    }

                    if ($children) {
                        $childs = $this->buildTree($item, $children, $id, $level);
                        $menu_item['childs'] = $childs;
                        $menu_item['has_childs'] = true;
                    } else {
                        $menu_item['childs'] = array();
                        $menu_item['has_childs'] = false;
                    }

                    $menu_item['query'] = $element;

                    $output[$id] = $menu_item;
                    unset($element);
                }
            }
        }

        return $output;
    }

    /**
     * Parse snippet
     *
     * @param string $snippet
     * @return string
     */
    private function parseSnippet($snippet)
    {
        $args = array();
        $snippetName = '';

        if (is_string($snippet) && $snippet) {
            $snippet = trim($snippet);

            if (($args = strpos($snippet, '?')) !== false) {
                $args_str = substr($snippet, $args + 1);
                $snippetName = substr($snippet, 0, strpos($snippet, '?'));

                if ($args_str) {
                    $args = explode('&', $args_str);
                }
            } else {
                $snippetName = $snippet;
            }
        }

        if ($args) {
            foreach ($args as $arg) {
                $array = explode('=', $arg);

                if ($array) {
                    $this->args[$array[0]] = $array[1];
                }
            }
        }

        return $snippetName;
    }
}

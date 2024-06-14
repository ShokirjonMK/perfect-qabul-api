<?php

namespace backend\models;

use base\libs\Translit;
use common\models\MenuGroup;
use common\models\MenuItems;

/**
 * Menu model
 */
class Menu
{
    public static $selected_language = 'en';
    private static $menu_items_found = array();

    /**
     * Get items
     *
     * @param string $page_type
     * @return object
     */
    public static function getItems($page_type = '', $args = array())
    {
        $search = input_get('s');
        $sort = input_get('sort');

        $current_lexicon = admin_content_lexicon();
        self::$selected_language = array_value($current_lexicon, 'lang_code', 'en');

        if (empty($sort) && array_value($args, 'sort')) {
            $sort = array_value($args, 'sort');
        }

        $query = MenuGroup::find();

        if ($search) {
            $query->andWhere(['like', 'name', $search]);
        }

        if ($sort == 'a-z') {
            $sort_query = ['name' => SORT_ASC];
        } elseif ($sort == 'z-a') {
            $sort_query = ['name' => SORT_DESC];
        } elseif ($sort == 'oldest') {
            $sort_query = ['created_on' => SORT_ASC];
        } else {
            $sort_query = ['created_on' => SORT_DESC];
        }

        $query->orderBy($sort_query);
        return $query;
    }

    /**
     * Page types
     *
     * @param string $active_key
     * @return array
     */
    public static function getPageTypes($active_key = '')
    {
        $page_types = array(
            '' => array(
                'name' => _e('All'),
                'active' => false,
                'count' => self::allCount(['deleted' => 0]),
            ),
            'published' => array(
                'name' => _e('Published'),
                'active' => false,
                'count' => self::publishedCount(),
            ),
            'unpublished' => array(
                'name' => _e('Unpublished'),
                'active' => false,
                'count' => self::unpublishedCount(),
            ),
            'deleted' => array(
                'name' => _e('Deleted'),
                'active' => false,
                'count' => self::deletedCount(),
            ),
        );

        if (isset($page_types[$active_key])) {
            $page_types[$active_key]['active'] = true;
        }

        return $page_types;
    }

    /**
     * Count all
     *
     * @param array $where
     * @return int
     */
    public static function allCount($where = array())
    {
        $query = MenuGroup::find();

        if (is_array($where) && $where) {
            $query->where($where);
        }

        return $query->count();
    }

    /**
     * Count published
     *
     * @param array $where
     * @return int
     */
    public static function publishedCount($where = array())
    {
        $query = MenuGroup::find()
            ->where(['deleted' => 0, 'status' => 1]);

        if (is_array($where) && $where) {
            $query->andWhere($where);
        }

        return $query->count();
    }

    /**
     * Count unpublished
     *
     * @param array $where
     * @return int
     */
    public static function unpublishedCount($where = array())
    {
        $query = MenuGroup::find()
            ->where(['deleted' => 0, 'status' => 0]);

        if (is_array($where) && $where) {
            $query->andWhere($where);
        }

        return $query->count();
    }

    /**
     * Count deleted
     *
     * @param array $where
     * @return int
     */
    public static function deletedCount($where = array())
    {
        $query = MenuGroup::find()
            ->where(['deleted' => 1]);

        if (is_array($where) && $where) {
            $query->andWhere($where);
        }

        return $query->count();
    }

    /**
     * Ajax actions
     *
     * @param string $action
     * @param int $id
     * @param mixed $items
     * @return array
     */
    public static function ajaxAction($action, $id, $items)
    {
        $output['error'] = true;
        $output['success'] = false;

        if ($action == 'unpublish') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = MenuGroup::findOne($id);
                self::bulkActionWithItem('unpublish', $item);

                $output['message'] = _e('Menu group unpublished successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = MenuGroup::findOne($items[$i]);
                    self::bulkActionWithItem('unpublish', $item);
                }

                $output['message'] = _e('Selected menu groups have been successfully unpublished.');
            }
        } elseif ($action == 'publish') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = MenuGroup::findOne($id);
                self::bulkActionWithItem('publish', $item);

                $output['message'] = _e('Menu group published successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = MenuGroup::findOne($items[$i]);
                    self::bulkActionWithItem('publish', $item);
                }

                $output['message'] = _e('Selected menu groups have been successfully published.');
            }
        } elseif ($action == 'trash') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = MenuGroup::findOne($id);
                self::bulkActionWithItem('trash', $item);

                $output['message'] = _e('Menu group moved to the trash successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = MenuGroup::findOne($items[$i]);
                    self::bulkActionWithItem('trash', $item);
                }

                $output['message'] = _e('Selected menu groups have been successfully moved to the trash.');
            }
        } elseif ($action == 'restore') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = MenuGroup::findOne($id);
                self::bulkActionWithItem('restore', $item);

                $output['message'] = _e('Menu group restored successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = MenuGroup::findOne($items[$i]);
                    self::bulkActionWithItem('restore', $item);
                }

                $output['message'] = _e('Selected menu groups have been successfully restored.');
            }
        } elseif ($action == 'delete') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = MenuGroup::findOne($id);
                self::deleteItem($item);

                $output['message'] = _e('Menu group deleted successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = $item = MenuGroup::findOne($items[$i]);
                    self::deleteItem($item);
                }

                $output['message'] = _e('Selected menu groups have been successfully deleted.');
            }
        }

        return $output;
    }

    /**
     * Actions with item
     *
     * @param [type] $type
     * @param [type] $model
     * @return void
     */
    public static function bulkActionWithItem($type, $model)
    {
        if ($model) {
            if ($type == 'trash') {
                $model->deleted = 1;
                $model->save(false);
            } elseif ($type == 'restore') {
                $model->deleted = 0;
                $model->save(false);
            } elseif ($type == 'publish') {
                $model->status = 1;
                $model->save(false);
            } elseif ($type == 'unpublish') {
                $model->status = 0;
                $model->save(false);
            }

            // Set log
            set_log('admin', ['res_id' => $model->id, 'type' => 'menu_group', 'action' => $type]);
        }
    }

    /**
     * Delete item
     *
     * @param [type] $model
     * @return void
     */
    public static function deleteItem($model)
    {
        if ($model) {
            $trash_item['group'] = $model->getAttributes();

            if ($model->delete(false)) {
                $group_key = $model->group_key;
                $menu_items = MenuItems::find()->where(['group_key' => $group_key])->all();

                if ($menu_items) {
                    foreach ($menu_items as $menu_item) {
                        $trash_item['items'][] = $menu_item->getAttributes();
                        $menu_item->delete();
                    }
                }

                // Set trash
                set_trash(array(
                    'res_id' => $model->id,
                    'type' => 'menus',
                    'data' => json_encode($trash_item),
                ));
            }
        }
    }

    /**
     * Status array
     *
     * @param int $key
     * @return array
     */
    public function statusArray($key = null)
    {
        $array = [
            1 => _e('Published'),
            0 => _e('Unpublished'),
        ];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $array;
    }

    /**
     * Create item
     *
     * @param [type] $model
     * @param [type] $post_item
     * @return void
     */
    public static function createItem($model, $post_item = null)
    {
        $log_data = array();
        $now_date = date('Y-m-d H:i:s');
        $current_user_id = current_user_id();
        $current_language = admin_current_lang();

        // Create model
        $model->created_on = $now_date;
        $model->created_by = $current_user_id;
        $model->updated_on = $now_date;
        $model->updated_by = $current_user_id;

        if (empty($model->language)) {
            $model->language = array_value($current_language, 'lang_code', 'en');
        }

        // Slug
        if (is_string($model->group_key) && !empty($model->group_key)) {
            $model->group_key = self::generateSlug($model->group_key);
        } else {
            $model->group_key = self::generateSlug($model->title);
        }

        // Translations
        $translations = array_value($post_item, 'translations_title');

        if ($translations) {
            $model->name = json_encode($translations);
        } else {
            $model->name = json_encode(array($model->language => $model->title));
        }

        if ($model->save()) {
            $logs['group']['attrs'] = $model->getAttributes();
            $logs['group']['old_attrs'] = array();

            $logs['items']['list'] = array();
            $logs['items']['old_list'] = array();

            // Menu items
            $menu_items = array_value($post_item, 'menu_items');

            if ($menu_items) {
                self::menuItemsUpdatePrepeare($menu_items, $model);

                $logs['items']['list'] = $menu_items;
            }

            // Set log
            $log_data['menu'][$model->group_key][$model->language] = $logs;

            set_log('admin', [
                'res_id' => $model->id,
                'type' => 'menu',
                'action' => 'create',
                'data' => json_encode($log_data),
            ]);
        }

        return $model->id;
    }

    /**
     * Update item
     *
     * @param [type] $model
     * @param [type] $post_item
     * @return void
     */
    public static function updateItem($model, $post_item = null)
    {
        $log_data = array();
        $now_date = date('Y-m-d H:i:s');
        $current_user_id = current_user_id();
        $current_language = admin_current_lang();

        // Save model
        $model->updated_on = $now_date;
        $model->updated_by = $current_user_id;

        if (empty($model->language)) {
            $model->language = array_value($current_language, 'lang_code', 'en');
        }

        // Slug
        if (empty($model->group_key)) {
            $model->group_key = self::generateSlug($model->title);
        }

        // Translations
        $translations = array_value($post_item, 'translations_title');

        if ($translations) {
            $model->name = json_encode($translations);
        } elseif (!is_null($model->name) && $model->name) {
            $translations = json_decode($model->name, true);

            foreach ($translations as $lang => $translation) {
                if ($lang = $model->language) {
                    $translations[$lang] = $model->title;
                }
            }

            if ($translations) {
                $model->name = json_encode($translations);
            }
        }

        $model_oldAttributes = $model->getOldAttributes();

        if ($model->save()) {
            $logs['group']['attrs'] = $model->getAttributes();
            $logs['group']['old_attrs'] = $model_oldAttributes;

            $logs['items']['list'] = array();
            $logs['items']['old_list'] = array();

            // Menu items
            $menu_items = array_value($post_item, 'menu_items');

            if ($menu_items) {
                $all_where = array(
                    'language' => $model->language,
                    'group_key' => $model->group_key
                );

                $menu_items_all = self::getMenuItems(['where' => $all_where]);
                self::menuItemsUpdatePrepeare($menu_items, $model);
                $menu_items_found = self::$menu_items_found;

                if ($menu_items_all) {
                    foreach ($menu_items_all as $menu_item_all) {
                        $id = $menu_item_all->id;

                        if (!isset($menu_items_found[$id])) {
                            $menu_item_all->delete();
                        }
                    }
                }

                $logs['items']['list'] = self::getMenuItems(['where' => $all_where]);
                $logs['items']['old_list'] = $menu_items_all;
            }

            // Set log
            $log_data['menu'][$model->group_key][$model->language] = $logs;

            set_log('admin', [
                'res_id' => $model->id,
                'type' => 'menu',
                'action' => 'create',
                'data' => json_encode($log_data),
            ]);
        }

        return $model->id;
    }

    /**
     * Slug generator
     *
     * @param [type] $title
     * @return string
     */
    public static function generateSlug($name)
    {
        $title_slug = Translit::slug($name);
        $slug = $title_slug;
        $where = array('group_key' => $slug);

        $item = MenuGroup::find()
            ->where($where)
            ->one();

        if ($item) {
            $i = 0;

            do {
                $i++;
                $slug = $title_slug . '-' . $i;
                $where['group_key'] = $slug;

                $item = MenuGroup::find()
                    ->where($where)
                    ->one();
            } while ($item && $i < 10000);
        }

        return $slug;
    }

    /**
     * Ajax item add
     *
     * @param [type] $type
     * @param [type] $post
     * @param [type] $yii
     * @return void
     */
    public static function itemAddAjaxAction($type, $post, $yii)
    {
        $output['error'] = true;
        $output['success'] = false;
        $output['message'] = _e('An error occurred while processing your request. Please try again.');

        $content_types = ['post', 'page'];
        $segment_types = ['post_category', 'product_category'];

        if ($type == 'link') {
            $output['error'] = false;
            $output['success'] = true;
            $output['message'] = _e("Menu item was added successfully.");
            $output['view'] = $yii->renderPartial('menu-item', $post);
        } elseif (in_array($type, $content_types)) {
            $output = Content::menuPageItemRender($type, $post, $yii);
        } elseif (in_array($type, $segment_types)) {
            $output = Segment::menuPageItemRender($type, $post, $yii);
        }

        return $output;
    }

    /**
     * Menu item prepeare to update
     *
     * @param [type] $array
     * @param [type] $model
     * @param integer $parent_id
     * @return void
     */
    public static function menuItemsUpdatePrepeare($array, $model, $parent_id = 0)
    {
        if ($array) {
            $menu_sort = 0;
            $menu_item_id = 0;

            foreach ($array as $menu_item) {
                $menu_sort++;
                $menu_item['sort'] = $menu_sort;
                $menu_id = array_value($menu_item, 'id');
                $childs = array_value($menu_item, 'childs');
                $menu_item_model = new MenuItems();

                if (is_numeric($menu_id) && $menu_id > 0) {
                    $get_menu_item = MenuItems::findOne($menu_id);

                    if ($get_menu_item) {
                        $menu_item_id = $get_menu_item->id;
                        self::$menu_items_found[$menu_item_id] = $menu_item_id;
                        $menu_item_model = $get_menu_item;
                    }
                }

                if ($menu_item_model->load($menu_item, '')) {
                    if (array_value($menu_item, 'name')) {
                        $data = array(
                            'name' => array_value($menu_item, 'name'),
                            'attrs' => array_value($menu_item, 'attrs'),
                            'menu_type' => array_value($menu_item, 'menu_type'),
                            'class_name' => array_value($menu_item, 'class_name'),
                            'link' => array_value($menu_item, 'link'),
                            'link_target' => array_value($menu_item, 'link_target'),
                            'icon' => array_value($menu_item, 'icon'),
                            'image' => array_value($menu_item, 'image'),
                            'snippet' => array_value($menu_item, 'snippet'),
                        );

                        $menu_item_model->data = json_encode($data);
                    }

                    if ($parent_id > 0) {
                        $menu_item_model->parent_id = $parent_id;
                    } else {
                        $menu_item_model->parent_id = 0;
                    }

                    $menu_item_model->group_key = $model->group_key;
                    $menu_item_model->language = $model->language;

                    if ($menu_item_model->data) {
                        $menu_item_model->save();
                        $menu_item_id = $menu_item_model->id;
                    }
                }

                if ($childs) {
                    self::menuItemsUpdatePrepeare($childs, $model, $menu_item_id);
                }
            }
        }
    }

    /**
     * Menu item for render
     *
     * @param [type] $menu_items
     * @param [type] $model
     * @param [type] $view
     * @return void
     */
    public static function menuItemsForRender($menu_items, $model, $view)
    {
        foreach ($menu_items as $menu_item) {
            $item = $menu_item->toArray();

            $menu_items_where = array(
                'group_key' => $model->group_key,
                'parent_id' => $menu_item->id,
            );

            $childs = Menu::getMenuItems(['where' => $menu_items_where]);

            if ($childs) {
                $item['childs'] = $childs;
                $item['model'] = $model;
            }

            echo $view->render('menu-item', $item);
        }
    }

    /**
     * Get menu items
     *
     * @param array $args
     * @return mixed
     */
    public static function getMenuItems($args = array())
    {
        $where = array_value($args, 'where');
        $search = array_value($args, 'search');
        $orderBy = array_value($args, 'order_by', ['sort' => SORT_ASC]);

        $query = MenuItems::find();

        if (is_array($where) && $where) {
            $query->where($where);
        }

        if ($search) {
            $query->andWhere([
                'or',
                ['name' => $search],
                ['link' => $search],
            ]);
        }

        if (is_array($orderBy) && $orderBy) {
            $query->orderBy($orderBy);
        }

        return $query->all();
    }
}

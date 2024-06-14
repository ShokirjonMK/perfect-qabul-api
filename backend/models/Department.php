<?php

namespace backend\models;

use base\libs\Translit;
use common\models\DepartmentInfo;
use common\models\Department as CommonDepartment;
use common\models\LogsAdmin;
use common\models\LogsFrontend;

/**
 * Department model
 */
class Department extends CommonDepartment
{
    public static $type;
    public static $items_with_parent = false;

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
        $parent = input_get('department');
        $type = input_get('type');

        $admin_current_lang = admin_current_lang();

        self::$selected_language = array_value($admin_current_lang, 'lang_code', 'en');

        if (empty($sort) && array_value($args, 'sort')) {
            $sort = array_value($args, 'sort');
        }

        if (empty($parent) && array_value($args, 'parent')) {
            $parent = array_value($args, 'parent');
        }

        if (empty($type) && array_value($args, 'type')) {
            $type = array_value($args, 'type');
        }

        $query = self::find()
            ->with('childs')
            ->join('INNER JOIN', 'department_info info', 'info.department_id = department.id');

        $query->andWhere(['language' => self::$selected_language]);

        if (is_numeric($parent) && $parent > 0) {
            $query->andWhere(['department.parent_id' => $parent]);
        }

        if (is_numeric($type) && $type > 0) {
            $query->andWhere(['department.type' => $type]);
        }


        if ($search) {
            $query->andWhere(['like', 'info.name', $search]);
        } 
        

        if ($sort == 'a-z') {
            $sort_query = ['info.name' => SORT_ASC];
        } elseif ($sort == 'z-a') {
            $sort_query = ['info.name' => SORT_DESC];
        } elseif ($sort == 'oldest') {
            $sort_query = ['department.created_on' => SORT_ASC];
        } else {
            $sort_query = ['department.created_on' => SORT_DESC];
        }

        

        $query->orderBy($sort_query);
        // rawsql($query);
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
            'index' => array(
                'name' => _e('All'),
                'active' => false,
                'count' => self::itemsCount(['department.deleted' => 0]),
            ),
            'published' => array(
                'name' => _e('Published'),
                'active' => false,
                'count' => self::itemsCount(['department.deleted' => 0, 'department.status' => 1]),
            ),
            'unpublished' => array(
                'name' => _e('Unpublished'),
                'active' => false,
                'count' => self::itemsCount(['department.deleted' => 0, 'department.status' => 0]),
            ),
            'deleted' => array(
                'name' => _e('Deleted'),
                'active' => false,
                'count' => self::itemsCount(['department.deleted' => 1]),
            ),
        );

        if (isset($page_types[$active_key])) {
            $page_types[$active_key]['active'] = true;
        }

        return $page_types;
    }



    /**
     * Create item
     *
     * @param [type] $model
     * @param [type] $info
     * @param [type] $post_item
     * @return int
     */
    public static function createItem($model, $info, $post_item = array())
    {
        $log_data = array();
        $now_date = date('Y-m-d H:i:s');
        $current_user_id = current_user_id();
        $active_languages = admin_active_langs();

        // Create model
        $model->created_on = $now_date;
        $model->created_by = $current_user_id;
        $model->updated_on = $now_date;
        $model->updated_by = $current_user_id;

        if ($model->save()) {
            $log_data['department']['attrs'] = $model->getAttributes();
            $log_data['department']['old_attrs'] = array();

            // Create translations
            if ($active_languages) {

                foreach ($active_languages as $active_language) {

                    $lang_code = $active_language['lang_code'];

                    $new = new DepartmentInfo();
                    $new->department_id = $model->id;
                    $new->language = $lang_code;
                    $new->name = $info->name[$lang_code];
                    $new->description = $info->description[$lang_code];

                    if ($new->save()) {
                        $log_data['department_info'][$new->language]['attrs'] = $new->getAttributes();
                        $log_data['department_info'][$new->language]['old_attrs'] = array();
                    }else{
                        dd($new->errors);
                    }
                }
            }

            // Count childs
            //self::countChilds($model->parent_id);

            // Set log
            set_log('admin', [
                'res_id' => $model->id,
                'type' => 'department',
                'action' => 'create',
                'data' => json_encode($log_data),
            ]);

        }else{
            dd($model->errors);
        }

        return $model->id;
    }

    /**
     * Update item
     *
     * @param [type] $model
     * @param [type] $info
     * @param [type] $post_item
     * @return int
     */
    public static function updateItem($model, $info, $post_item = array())
    {
        $log_data = array();
        $now_date = date('Y-m-d H:i:s');
        $active_languages = admin_active_langs();
        $old_parent_id = $model->getOldAttribute('parent_id');
        $current_user_id = current_user_id();

        // Save model
        $model->updated_on = $now_date;
        $model->updated_by = $current_user_id;

        $modelOldAttributes = $model->getOldAttributes();

        if ($model->save()) {
            $log_data['department']['attrs'] = $model->getAttributes();
            $log_data['department']['old_attrs'] = $modelOldAttributes;

            // Create translations
            if ($active_languages) {

                foreach ($active_languages as $active_language) {

                    $lang_code = $active_language['lang_code'];

                    $infoSingleLang = DepartmentInfo::find()->where(['department_id' => $model->id, 'language' => $lang_code])->one();
                    if($infoSingleLang){
                        $infoSingleLang->name = $info->name[$lang_code];
                        $infoSingleLang->description = $info->description[$lang_code];

                        $infoOldAttributes = $infoSingleLang->getOldAttributes();

                        if ($infoSingleLang->save()) {
                            $log_data['department_info'][$infoSingleLang->language]['attrs'] = $infoSingleLang->getAttributes();
                            $log_data['department_info'][$infoSingleLang->language]['old_attrs'] = $infoOldAttributes;
                        }else{
                            dd($infoSingleLang->errors);
                        }
                    }
                    
                }
            }

            // Count childs
            //self::countChilds($model->parent_id);
            //self::countChilds($old_parent_id);

            // Set log
            set_log('admin', [
                'res_id' => $model->id,
                'type' => 'department',
                'action' => 'update',
                'data' => json_encode($log_data),
            ]);
        }else{
            dd($model->errors);
        }

        return $model->id;
    }

    /**
     * Get item to edit
     *
     * @param int $id
     * @return array
     */
    public static function getItemToEdit($id)
    {
        $current_language = admin_current_lang();
        $lang_code = array_value($current_language, 'lang_code', 'en');

        $model = self::findOne($id);
        $info = new DepartmentInfo();
        $translations = DepartmentInfo::find()->where(['department_id' => $id])->all();
        $names = [];
        $descriptions = [];
        foreach ($translations as $translation) {
            $names[$translation['language']] = $translation['name'];
            $descriptions[$translation['language']] = $translation['description'];
        }

        $info->name = $names;
        $info->description = $descriptions;

        $output['model'] = $model;
        $output['info'] = $info;
        $output['translations'] = $translations;

        return $output;
    }

    /**
     * Get parents list
     *
     * @param [type] $model
     * @param [type] $info
     * @param [type] $parent_id
     * @param [type] $type
     * @return array
     */
    public static function getListParent($model, $info, $parent_id = 0, $type = '')
    {
        if ($model == 'product') {
            if ($type == 'category') {
                $output = array('' => _e('No category'));
            } elseif ($type == 'brand') {
                $output = array('0' => _e('No brand'));
            } else {
                $output = array('' => '-');
            }

            $where = array('department.type' => $type);
        } else {
            $output = array('0' => _e('No parent'));
            $where = array('department.type' => self::$type);
        }

        if (isset($info->language) && $info->language) {
            self::$selected_language = $info->language;
        }

        if (is_numeric($parent_id) && $parent_id > 0) {
            $where['department.parent_id'] = $parent_id;
        } else {
            $where['department.parent_id'] = 0;
        }

        $query = self::find()
            ->alias('content')
            ->select('department.*, info.name as default_title')
            ->join('INNER JOIN', 'site_content_info info', 'info.content_id = department.id')
            ->where($where);

        if (isset($model->id) && is_numeric($model->id)) {
            $query->andWhere(['!=', 'id', $model->id]);
        }

        $query->with([
            'info' => function ($query) {
                $query->andWhere(['language' => self::$selected_language]);
            },
        ]);

        $query->orderBy(['info.name' => 'ASC']);
        $items = $query->all();

        if ($items) {
            foreach ($items as $item) {
                $childs = self::getParentChilds($item->id, $model, $info, $type, 1);

                if (isset($item->info->name)) {
                    $output[$item->id] = $item->info->name;
                } else {
                    $output[$item->id] = $item->default_title;
                }

                if ($childs) {
                    $output = $output + $childs;
                }
            }
        }

        return $output;
    }

    /**
     * Get parent childs
     *
     * @param integer $parent_id
     * @param [type] $model
     * @param [type] $info
     * @param [type] $type
     * @param integer $level
     * @param string $prefix
     * @return array
     */
    private static function getParentChilds($parent_id = 0, $model = null, $info = null, $type = '', $level = 0, $prefix = '')
    {
        $array = array();

        if ($type) {
            $where = array('department.type' => $type);
        } else {
            $where = array('department.type' => self::$type);
        }

        if (isset($info->language) && $info->language) {
            self::$selected_language = $info->language;
        }

        if (is_numeric($parent_id) && $parent_id > 0) {
            $where['department.parent_id'] = $parent_id;
        } else {
            $where['department.parent_id'] = 0;
        }

        $query = self::find()
            ->alias('content')
            ->select('department.*, info.name as default_title')
            ->join('INNER JOIN', 'site_content_info info', 'info.content_id = department.id')
            ->where($where);

        if (isset($model->id) && is_numeric($model->id)) {
            $query->andWhere(['!=', 'id', $model->id]);
        }

        $query->with([
            'info' => function ($query) {
                $query->andWhere(['language' => self::$selected_language]);
            },
        ]);

        $query->orderBy(['info.name' => 'ASC']);
        $items = $query->all();

        if ($items) {
            if ($level > 0) {
                for ($i = 0; $i < $level; $i++) {
                    $prefix .= '-';
                }
            }

            foreach ($items as $item) {
                if (isset($item->info->name)) {
                    $info_title = $item->info->name;
                } else {
                    $info_title = $item->default_title;
                }

                if ($prefix) {
                    $title = '&nbsp;' . $prefix . ' ' . $info_title;
                } else {
                    $title = $info_title;
                }

                $childs = self::getParentChilds($item->id, $model, $info, $type, $level, $prefix);

                $array[$item->id] = $title;

                if ($childs) {
                    $array = $array + $childs;
                }
            }
        }

        $level++;
        return $array;
    }

    /**
     * Count all
     *
     * @param array $where
     * @return int
     */
    public static function itemsCount($where = array(), $where_in = array())
    {
        $query = self::find()
            ->join('INNER JOIN', 'department_info info', 'info.department_id = department.id');

        if (is_array($where) && $where) {
            $query->andWhere($where);
        }

        if (is_array($where_in) && $where_in) {
            $query->andWhere($where_in);
        }

        $query->groupBy('info.department_id');
        return $query->count();
    }

    /**
     * Childs count
     *
     * @param integer $parent_id
     * @return void
     */
    public static function countChilds($parent_id)
    {
        if (is_numeric($parent_id) && $parent_id > 0) {
            $x = 0;
            $childs_count = 0;

            do {
                $x++;
                $parent = self::findOne($parent_id);

                if ($parent) {
                    $item_where = ['parent_id' => $parent->id];
                    $field_where = ['content_id' => $parent->id, 'field_key' => 'child_count'];

                    $childs = self::find()->where($item_where)->count();

                    $childs_count = ($childs_count + $childs);

                    $parent_id = $parent->parent_id;
                } else {
                    $parent = false;
                }
            } while ($parent && $x < 100000);
        }
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
                $item = self::findOne($id);
                self::bulkActionWithDepartment('unpublish', $item);

                $output['message'] = _e('Item unpublished successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithDepartment('unpublish', $item);
                }

                $output['message'] = _e('Selected items have been successfully unpublished.');
            }
        } elseif ($action == 'publish') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::bulkActionWithDepartment('publish', $item);

                $output['message'] = _e('Item published successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithDepartment('publish', $item);
                }

                $output['message'] = _e('Selected items have been successfully published.');
            }
        } elseif ($action == 'trash') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::bulkActionWithDepartment('trash', $item);

                $output['message'] = _e('Item moved to the trash successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithDepartment('trash', $item);
                }

                $output['message'] = _e('Selected items have been successfully moved to the trash.');
            }
        } elseif ($action == 'restore') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::bulkActionWithDepartment('restore', $item);

                $output['message'] = _e('Item restored successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithDepartment('restore', $item);
                }

                $output['message'] = _e('Selected items have been successfully restored.');
            }
        } elseif ($action == 'delete') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::deleteDepartment($item);

                $output['message'] = _e('Item deleted successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = $item = self::findOne($items[$i]);
                    self::deleteDepartment($item);
                }

                $output['message'] = _e('Selected items have been successfully deleted.');
            }
        }

        return $output;
    }

    /**
     * Actions with content
     *
     * @param [type] $type
     * @param [type] $model
     * @param boolean $with_childs
     * @return void
     */
    public static function bulkActionWithDepartment($type, $model, $with_childs = true)
    {
        if ($model) {
            if ($with_childs) {
                self::bulkActionWithDepartmentChilds($type, $model);
            }

            if ($type == 'trash') {
                $model->deleted = 1;
                $model->save();
            } elseif ($type == 'restore') {
                $model->deleted = 0;
                $model->save();
            } elseif ($type == 'publish') {
                $model->status = 1;
                $model->save();
            } elseif ($type == 'unpublish') {
                $model->status = 0;
                $model->save();
            }

            // Set log
            set_log('admin', ['res_id' => $model->id, 'type' => $model->type, 'action' => $type]);
        }
    }

    /**
     * Actions with content childs
     *
     * @param [type] $type
     * @param [type] $model
     * @return void
     */
    public static function bulkActionWithDepartmentChilds($type, $model)
    {
        if ($model) {
            $id = $model->id;
            $childs = self::find()->where(['parent_id' => $id])->all();

            if ($childs) {
                foreach ($childs as $child) {
                    self::bulkActionWithDepartment($type, $child);
                }
            }
        }
    }

    /**
     * Delete content
     *
     * @param [type] $model
     * @param boolean $with_childs
     * @return void
     */
    public static function deleteDepartment($model, $with_childs = true)
    {
        if ($model) {
            $trash_item['content'] = $model->getAttributes();

            if ($with_childs) {
                self::deleteDepartmentChilds($model, false);
            }

            if ($model->delete(false)) {
                $id = $model->id;
                $info = DepartmentInfo::find()->where(['content_id' => $id])->all();

                if ($info) {
                    foreach ($info as $info_item) {
                        $trash_item['info'][] = $info_item->getAttributes();
                        $info_item->delete();
                    }
                }

                // Set trash
                set_trash(array(
                    'res_id' => $id,
                    'type' => $model->type,
                    'data' => json_encode($trash_item),
                ));

                // Set log
                set_log('admin', [
                    'res_id' => $model->id,
                    'type' => $model->type,
                    'action' => 'delete',
                    'data' => json_encode($trash_item),
                ]);
            }
        }
    }

    /**
     * Delete content childs
     *
     * @param [type] $model
     * @param boolean $self_delete
     * @return void
     */
    public static function deleteDepartmentChilds($model, $self_delete = true)
    {
        if ($model) {
            $id = $model->id;
            $type = $model->type;
            $childs = self::find()->where(['parent_id' => $id])->all();

            if ($childs) {
                foreach ($childs as $child) {
                    self::deleteDepartmentChilds($child);
                }
            }

            if ($self_delete && $model->delete(false)) {
                $trash_item['content'] = $model->getAttributes();
                $info = DepartmentInfo::find()->where(['content_id' => $id])->all();

                if ($info) {
                    foreach ($info as $info_item) {
                        $trash_item['infos'][] = $info_item->getAttributes();
                        $info_item->delete();
                    }
                }

                LogsAdmin::deleteAll(['res_id' => $id, 'type' => $type]);
                LogsFrontend::deleteAll(['res_id' => $id, 'type' => $type]);

                // Set trash
                set_trash(array(
                    'res_id' => $id,
                    'type' => $type,
                    'data' => json_encode($trash_item),
                ));

                // Set log
                set_log('admin', [
                    'res_id' => $id,
                    'type' => $type,
                    'action' => 'delete',
                    'data' => json_encode($trash_item),
                ]);
            }
        }
    }

    /**
     * Settings array
     *
     * @param mixed $model
     * @param string $type
     * @return array
     */
    public static function settingsArray($model, $type = 'settings')
    {
        $array = array();

        if ($type == 'meta') {
            $types = array('meta_title', 'meta_description', 'focus_keywords');
        } else {
            $types = array('posts_column', 'posts_sorting', 'posts_per_page', 'subcategories_view_type', 'products_view_type', 'products_sorting', 'products_per_page');
        }

        foreach ($types as $type) {
            if (isset($model->$type)) {
                $array[$type] = $model->$type;
            }
        }

        return $array;
    }




    /**
     * Menu page item render
     *
     * @param [type] $type
     * @param [type] $post
     * @param [type] $yii
     * @return array
     */
    public static function menuPageItemRender($type, $post, $yii)
    {
        $output['error'] = true;
        $output['success'] = false;
        $output['message'] = _e('An error occurred while processing your request. Please try again.');

        $results = array();
        $search_items = array_value($post, 'search_items');
        $selected_items = array_value($post, 'selected_items');

        $current_language = admin_current_lang('lang_code');
        self::$selected_language = array_value($post, 'lang', $current_language);

        if ($selected_items) {
            $array = array_value($post, 'selected_array');

            if (is_array($array) && $array) {
                $query = self::find()
                    ->alias('content')
                    ->select('department.*, info.*')
                    ->join('INNER JOIN', 'site_content_info info', 'info.content_id = department.id')
                    ->where(['in', 'department.id', $array])
                    ->andWhere(['department.type' => $type])
                    ->with([
                        'info' => function ($query) {
                            $query->andWhere(['language' => self::$selected_language]);
                        },
                    ])
                    ->all();

                if ($query) {
                    $output['error'] = false;
                    $output['success'] = true;
                    $output['message'] = _e('Menu item was added successfully.');

                    foreach ($query as $value) {
                        $value_json = ['name' => $value->info->name];

                        $value_data = array(
                            'action_type' => $type,
                            'item_id' => $value->id,
                            'data' => json_encode($value_json),
                        );

                        $data[] = $yii->renderPartial('menu-item', $value_data);
                    }

                    $output['view'] = implode(' ', $data);
                }
            } else {
                $output['message'] = _e('No items selected to add. Please select item to add to the menu.');
            }
        } elseif ($search_items) {
            $search = array_value($post, 'search');
            $search_key = clean_str($search);

            if (strlen($search_key) < 3) {
                $output['message'] = _e('Please enter at least 3 characters to search.');
            } elseif ($search_key) {
                $results = self::find()
                    ->alias('content')
                    ->select('department.*, info.*')
                    ->join('INNER JOIN', 'site_content_info info', 'info.content_id = department.id')
                    ->where(['department.type' => $type])
                    ->andWhere(['like', 'info.name', $search])
                    ->with([
                        'info' => function ($query) {
                            $query->andWhere(['language' => self::$selected_language]);
                        },
                    ])
                    ->all();

                if (!$results) {
                    $output['message'] = _e('No results were found for your request. Please try other keywords.');
                }
            } else {
                $output['message'] = _e('Enter your search keyword to find item.');
            }
        } else {
            $data = array();
            $results = self::find()
                ->alias('content')
                ->select('department.*, info.*')
                ->join('INNER JOIN', 'site_content_info info', 'info.content_id = department.id')
                ->where(['department.type' => $type, 'department.deleted' => 0, 'department.status' => 1])
                ->with([
                    'info' => function ($query) {
                        $query->andWhere(['language' => self::$selected_language]);
                    },
                ])
                ->orderBy(['department.created_on' => SORT_DESC])
                ->limit(10)
                ->all();

            if ($results) {
                $output['message'] = '';
            } else {
                $output['message'] = _e('Items not found.');
            }
        }

        if ($results) {
            $data = array();
            $output['error'] = false;
            $output['success'] = true;

            foreach ($results as $value) {
                $value_data = array(
                    'item_type' => $type,
                    'item_id' => $value->id,
                    'item_title' => $value->info->name,
                );

                $data[] = $yii->renderPartial('ajax-item', $value_data);
            }

            $output['html'] = $data;
        }

        return $output;
    }
}

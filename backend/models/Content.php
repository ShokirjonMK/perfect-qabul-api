<?php

namespace backend\models;

use base\libs\Translit;
use common\models\ContentFields;
use common\models\ContentInfos;
use common\models\Content as Contents;
use common\models\LogsAdmin;
use common\models\LogsFrontend;
use common\models\SegmentRelations;

/**
 * Content model
 */
class Content extends Contents
{
    public static $type;
    public static $items_with_parent = false;
    public static $slug_generator = false;

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
        $parent = input_get('parent');

        $current_lexicon = admin_content_lexicon();
        self::$selected_language = array_value($current_lexicon, 'lang_code', 'en');

        if (empty($sort) && array_value($args, 'sort')) {
            $sort = array_value($args, 'sort');
        }

        if (empty($parent) && array_value($args, 'parent')) {
            $parent = array_value($args, 'parent');
        }

        $query = self::find()
            ->alias('content')
            ->select('content.*, info.*')
            ->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id')
            ->where(['content.type' => self::$type]);

        if ($search) {
            $query->andWhere(['like', 'info.title', $search]);

            if (is_numeric($parent) && $parent > 0) {
                $query->andWhere(['content.parent_id' => $parent]);
            }
        } elseif (is_numeric($parent) && $parent > 0) {
            $query->andWhere(['content.parent_id' => $parent]);
        } elseif ($page_type != 'deleted') {
            $query->andWhere(['content.parent_id' => 0]);
        }

        if ($sort == 'a-z') {
            $sort_query = ['info.title' => SORT_ASC];
        } elseif ($sort == 'z-a') {
            $sort_query = ['info.title' => SORT_DESC];
        } elseif ($sort == 'oldest') {
            $sort_query = ['content.created_on' => SORT_ASC];
        } else {
            $sort_query = ['content.created_on' => SORT_DESC];
        }

        if (self::$items_with_parent) {
            $query->with([
                'info' => function ($query) {
                    $query->andWhere(['language' => self::$selected_language]);
                },
                'parentInfo' => function ($query) {
                    $query->andWhere(['language' => self::$selected_language]);
                },
            ]);
        } else {
            $query->with([
                'info' => function ($query) {
                    $query->andWhere(['language' => self::$selected_language]);
                },
            ]);
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
            'all' => array(
                'name' => _e('All'),
                'active' => false,
                'count' => self::itemsCount(['content.type' => self::$type, 'content.deleted' => 0]),
            ),
            'published' => array(
                'name' => _e('Published'),
                'active' => false,
                'count' => self::itemsCount(['content.type' => self::$type, 'content.deleted' => 0, 'content.status' => 1]),
            ),
            'unpublished' => array(
                'name' => _e('Unpublished'),
                'active' => false,
                'count' => self::itemsCount(['content.type' => self::$type, 'content.deleted' => 0, 'content.status' => 0]),
            ),
            'deleted' => array(
                'name' => _e('Deleted'),
                'active' => false,
                'count' => self::itemsCount(['content.type' => self::$type, 'content.deleted' => 1]),
            ),
        );

        if (isset($page_types[$active_key])) {
            $page_types[$active_key]['active'] = true;
        }

        return $page_types;
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
     * @param mixed $model
     * @param mixed $info
     * @param array $post_item
     * @return int
     */
    public static function createItem($model, $info, $post_item = array())
    {
        $log_data = array();
        $now_date = date('Y-m-d H:i:s');
        $current_user_id = current_user_id();
        $active_languages = admin_active_langs();
        $current_language = admin_current_lang();
        $translations_title = array_value($post_item, 'translations_title');

        if (empty($info->language)) {
            $info->language = array_value($current_language, 'lang_code', 'en');
        }

        // Create model
        $model->type = self::$type;
        $model->created_on = $now_date;
        $model->created_by = $current_user_id;
        $model->updated_on = $now_date;
        $model->updated_by = $current_user_id;

        // Settings
        $settings = self::settingsArray($model);
        $model->settings = $settings ? $settings : null;

        if ($model->save()) {
            $log_data['content']['attrs'] = $model->getAttributes();
            $log_data['content']['old_attrs'] = array();

            // Create info
            if (!isset($info->icon) || empty($info->icon)) {
                $info->icon = '';
            }

            $info->content_id = $model->id;
            $info->slug = self::generateSlug($info->title, $info->language);

            // Meta
            $meta = self::settingsArray($info, 'meta');
            $info->meta = $meta ? $meta : null;

            // Check content blocks
            if ($info->content_blocks) {
                $content_blocks = array();
                $content_blocks_str = $info->content_blocks;

                if (is_string($content_blocks_str) && !empty($content_blocks_str)) {
                    $content_blocks = json_decode($content_blocks_str, true);
                }

                if (is_array($content_blocks) && $content_blocks) {
                    $info->content_blocks = remove_php_tags_from_array($content_blocks);
                } else {
                    $info->content_blocks = array();
                }
            }

            if ($info->save()) {
                $log_data['info'][$info->language]['attrs'] = $info->getAttributes();
                $log_data['info'][$info->language]['old_attrs'] = array();

                // Segment relations
                self::bindRelations($model);

                // Create translations
                if ($active_languages) {
                    $item_lang = $info->language;

                    foreach ($active_languages as $active_language) {
                        $lang_code = $active_language['lang_code'];

                        if ($item_lang != $lang_code) {
                            $new = new ContentInfos();
                            $new->setAttributes($info->getAttributes());

                            if (isset($translations_title[$lang_code]) && $translations_title[$lang_code]) {
                                $new->title = $translations_title[$lang_code];
                            }

                            $new->language = $lang_code;
                            $new->slug = self::generateSlug($new->title, $new->language);

                            if ($new->save()) {
                                $log_data['info'][$new->language]['attrs'] = $new->getAttributes();
                                $log_data['info'][$new->language]['old_attrs'] = array();
                            }
                        }
                    }
                }

                // Count childs
                self::countChilds($model->parent_id);

                // Set log
                set_log('admin', [
                    'res_id' => $model->id,
                    'type' => $model->type,
                    'action' => 'create',
                    'data' => json_encode($log_data),
                ]);
            }

            if ($info->hasErrors()) {
                $errors = $info->getFirstErrors();
                return reset($errors);
            }
        }

        if ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            return reset($errors);
        }

        return $model->id;
    }

    /**
     * Update item
     *
     * @param mixed $model
     * @param mixed $info
     * @param array $post_item
     * @return int
     */
    public static function updateItem($model, $info, $post_item = array())
    {
        $log_data = array();
        $now_date = date('Y-m-d H:i:s');
        $current_language = admin_current_lang();

        if (empty($info->language)) {
            $info->language = array_value($current_language, 'lang_code', 'en');
        }

        $item_lang = $info->language;
        $item_lang_old = $info->getOldAttribute('language');
        $old_parent_id = $model->getOldAttribute('parent_id');
        $current_user_id = current_user_id();

        // Check language
        if ($item_lang != $item_lang_old && $info->info_id > 0) {
            $find_translation = ContentInfos::find()
                ->where(['content_id' => $model->id, 'language' => $item_lang])
                ->all();

            if ($find_translation) {
                $error = _e('Translation found. Please select an empty language.');
                return $error;
            }
        }

        // Save model
        $model->type = self::$type;
        $model->updated_on = $now_date;
        $model->updated_by = $current_user_id;

        // Settings
        $settings = self::settingsArray($model);
        $model->settings = $settings ? $settings : null;
        $model_oldAttributes = $model->getOldAttributes();

        if ($model->save()) {
            $log_data['content']['attrs'] = $model->getAttributes();
            $log_data['content']['old_attrs'] = $model_oldAttributes;

            // Segment relations
            self::bindRelations($model);

            // Check slug
            if (empty($info->slug)) {
                $info->slug = self::generateSlug($info->title, $info->language);
            }

            // Check content blocks
            if ($info->content_blocks) {
                $content_blocks = array();
                $content_blocks_str = $info->content_blocks;

                if (is_string($content_blocks_str) && !empty($content_blocks_str)) {
                    $content_blocks = json_decode($content_blocks_str, true);
                }

                if (is_array($content_blocks) && $content_blocks) {
                    $info->content_blocks = remove_php_tags_from_array($content_blocks);
                } else {
                    $info->content_blocks = array();
                }
            }

            // Update info
            if ($info->info_id < 1) {
                unset($info->info_id);

                $new = new ContentInfos();
                $new->setAttributes($info->getAttributes());
                $new->slug = self::generateSlug($new->title, $new->language);

                if ($new->save()) {
                    $log_data['info'][$new->language]['attrs'] = $new->getAttributes();
                    $log_data['info'][$new->language]['old_attrs'] = array();
                }
            } else {
                if (!isset($info->icon) || empty($info->icon)) {
                    $info->icon = '';
                }

                $meta = self::settingsArray($info, 'meta');
                $info->meta = $meta ? $meta : null;
                $info_oldAttributes = $info->getOldAttributes();

                if ($info->save()) {
                    $log_data['info'][$info->language]['attrs'] = $info->getAttributes();
                    $log_data['info'][$info->language]['old_attrs'] = $info_oldAttributes;
                }

                if ($info->hasErrors()) {
                    $errors = $info->getFirstErrors();
                    return reset($errors);
                }
            }

            // Count childs
            self::countChilds($model->parent_id);
            self::countChilds($old_parent_id);

            // Set log
            set_log('admin', [
                'res_id' => $model->id,
                'type' => $model->type,
                'action' => 'update',
                'data' => json_encode($log_data),
            ]);
        }

        if ($model->hasErrors()) {
            $errors = $model->getFirstErrors();
            return reset($errors);
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
        $lang_key = input_get('lang', $lang_code);

        $model = self::findOne($id);
        $info = ContentInfos::find()->where(['content_id' => $id, 'language' => $lang_key])->one();
        $translations = ContentInfos::find()->where(['content_id' => $id])->all();

        if (!$info) {
            $info = ContentInfos::find()->where(['content_id' => $id])->one();

            if ($info) {
                $info->title = $info->title . " [{$lang_key}]";
                $info->slug = '';
                $info->info_id = 0;
                $info->language = $lang_key;
            }
        }

        $output['model'] = $model;
        $output['info'] = $info;
        $output['translations'] = $translations;

        return $output;
    }

    /**
     * Get parents list
     *
     * @param mixed $model
     * @param mixed $info
     * @param int $parent_id
     * @param string $type
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

            $where = array('content.type' => $type);
        } else {
            $output = array('0' => _e('No parent'));
            $where = array('content.type' => self::$type);
        }

        if (isset($info->language) && $info->language) {
            self::$selected_language = $info->language;
        }

        if (is_numeric($parent_id) && $parent_id > 0) {
            $where['content.parent_id'] = $parent_id;
        } else {
            $where['content.parent_id'] = 0;
        }

        $query = self::find()
            ->alias('content')
            ->select('content.*, info.title as default_title')
            ->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id')
            ->where($where);

        if (isset($model->id) && is_numeric($model->id)) {
            $query->andWhere(['!=', 'id', $model->id]);
        }

        $query->with([
            'info' => function ($query) {
                $query->andWhere(['language' => self::$selected_language]);
            },
        ]);

        $query->orderBy(['info.title' => 'ASC']);
        $items = $query->all();

        if ($items) {
            foreach ($items as $item) {
                $childs = self::getParentChilds($item->id, $model, $info, $type, 1);

                if (isset($item->info->title)) {
                    $output[$item->id] = $item->info->title;
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
     * @param mixed $model
     * @param mixed $info
     * @param string $type
     * @param integer $level
     * @param string $prefix
     * @return array
     */
    private static function getParentChilds($parent_id = 0, $model = null, $info = null, $type = '', $level = 0, $prefix = '')
    {
        $array = array();

        if ($type) {
            $where = array('content.type' => $type);
        } else {
            $where = array('content.type' => self::$type);
        }

        if (isset($info->language) && $info->language) {
            self::$selected_language = $info->language;
        }

        if (is_numeric($parent_id) && $parent_id > 0) {
            $where['content.parent_id'] = $parent_id;
        } else {
            $where['content.parent_id'] = 0;
        }

        $query = self::find()
            ->alias('content')
            ->select('content.*, info.title as default_title')
            ->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id')
            ->where($where);

        if (isset($model->id) && is_numeric($model->id)) {
            $query->andWhere(['!=', 'id', $model->id]);
        }

        $query->with([
            'info' => function ($query) {
                $query->andWhere(['language' => self::$selected_language]);
            },
        ]);

        $query->orderBy(['info.title' => 'ASC']);
        $items = $query->all();

        if ($items) {
            if ($level > 0) {
                for ($i = 0; $i < $level; $i++) {
                    $prefix .= '-';
                }
            }

            foreach ($items as $item) {
                if (isset($item->info->title)) {
                    $info_title = $item->info->title;
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
        $query = self::find()->alias('content');
        $query->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id');

        if (is_array($where) && $where) {
            $query->andWhere($where);
        }

        if (is_array($where_in) && $where_in) {
            $query->andWhere($where_in);
        }

        $query->groupBy('info.content_id');
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

                    $field = ContentFields::find()->where($field_where)->one();
                    $childs = self::find()->where($item_where)->count();

                    $childs_count = ($childs_count + $childs);

                    if ($field) {
                        $field->field_value = $childs_count;
                        $field->save(false);
                    } else {
                        $field = new ContentFields();
                        $field->content_id = $parent->id;
                        $field->field_key = 'child_count';
                        $field->field_value = $childs_count;
                        $field->save(false);
                    }

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
                self::bulkActionWithContent('unpublish', $item);

                $output['message'] = _e('Item unpublished successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithContent('unpublish', $item);
                }

                $output['message'] = _e('Selected items have been successfully unpublished.');
            }
        } elseif ($action == 'publish') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::bulkActionWithContent('publish', $item);

                $output['message'] = _e('Item published successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithContent('publish', $item);
                }

                $output['message'] = _e('Selected items have been successfully published.');
            }
        } elseif ($action == 'trash') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::bulkActionWithContent('trash', $item);

                $output['message'] = _e('Item moved to the trash successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithContent('trash', $item);
                }

                $output['message'] = _e('Selected items have been successfully moved to the trash.');
            }
        } elseif ($action == 'restore') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::bulkActionWithContent('restore', $item);

                $output['message'] = _e('Item restored successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithContent('restore', $item);
                }

                $output['message'] = _e('Selected items have been successfully restored.');
            }
        } elseif ($action == 'delete') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::deleteContent($item);

                $output['message'] = _e('Item deleted successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = $item = self::findOne($items[$i]);
                    self::deleteContent($item);
                }

                $output['message'] = _e('Selected items have been successfully deleted.');
            }
        }

        return $output;
    }

    /**
     * Actions with content
     *
     * @param string $type
     * @param mixed $model
     * @param boolean $with_childs
     * @return void
     */
    public static function bulkActionWithContent($type, $model, $with_childs = true)
    {
        if ($model) {
            if ($with_childs) {
                self::bulkActionWithContentChilds($type, $model);
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
     * @param string $type
     * @param mixed $model
     * @return void
     */
    public static function bulkActionWithContentChilds($type, $model)
    {
        if ($model) {
            $id = $model->id;
            $childs = self::find()->where(['parent_id' => $id])->all();

            if ($childs) {
                foreach ($childs as $child) {
                    self::bulkActionWithContent($type, $child);
                }
            }
        }
    }

    /**
     * Delete content
     *
     * @param mixed $model
     * @param boolean $with_childs
     * @return void
     */
    public static function deleteContent($model, $with_childs = true)
    {
        if ($model) {
            $trash_item['content'] = $model->getAttributes();

            if ($with_childs) {
                self::deleteContentChilds($model, false);
            }

            if ($model->delete(false)) {
                $id = $model->id;
                $info = ContentInfos::find()->where(['content_id' => $id])->all();
                $fields = ContentFields::find()->where(['content_id' => $id])->all();

                if ($info) {
                    foreach ($info as $info_item) {
                        $trash_item['info'][] = $info_item->getAttributes();
                        $info_item->delete();
                    }
                }

                if ($fields) {
                    foreach ($fields as $field_item) {
                        $trash_item['field'][] = $field_item->getAttributes();
                        $field_item->delete();
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
     * @param mixed $model
     * @param boolean $self_delete
     * @return void
     */
    public static function deleteContentChilds($model, $self_delete = true)
    {
        if ($model) {
            $id = $model->id;
            $type = $model->type;
            $childs = self::find()->where(['parent_id' => $id])->all();

            if ($childs) {
                foreach ($childs as $child) {
                    self::deleteContentChilds($child);
                }
            }

            if ($self_delete && $model->delete(false)) {
                $trash_item['content'] = $model->getAttributes();
                $info = ContentInfos::find()->where(['content_id' => $id])->all();
                $fields = ContentFields::find()->where(['content_id' => $id])->all();

                if ($info) {
                    foreach ($info as $info_item) {
                        $trash_item['infos'][] = $info_item->getAttributes();
                        $info_item->delete();
                    }
                }

                if ($fields) {
                    foreach ($fields as $field_item) {
                        $trash_item['fields'][] = $field_item->getAttributes();
                        $field_item->delete();
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
     * Slug generator
     *
     * @param string $title
     * @return string
     */
    public static function generateSlug($title, $lang = false)
    {
        if (self::$slug_generator == 'prefix') {
            $slug = self::generateSlugWithPrefix($title, self::$type, $lang);
        } else {
            $slug = self::generateSlugSame($title, self::$type, $lang);
        }

        return $slug;
    }

    /**
     * Slug generator
     *
     * @param string $title
     * @param string $type
     * @param boolean $lang
     * @return string
     */
    public static function generateSlugSame($title, $type, $lang = false)
    {
        $title_slug = Translit::slug($title);
        $slug = $title_slug;
        $where = array('content.type' => $type, 'info.slug' => $slug);

        if ($lang) {
            $where['info.language'] = $lang;
        }

        $item = self::find()
            ->alias('content')
            ->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id')
            ->where($where)
            ->one();

        if ($item) {
            $i = 0;

            do {
                $i++;
                $slug = $title_slug . '-' . $i;
                $where['info.slug'] = $slug;

                $item = self::find()
                    ->alias('content')
                    ->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id')
                    ->where($where)
                    ->one();
            } while ($item && $i < 10000);
        }

        return $slug;
    }

    /**
     * Slug generator with prefix
     *
     * @param string $title
     * @param string $type
     * @param boolean $lang
     * @return string
     */
    public static function generateSlugWithPrefix($title, $type, $lang = false)
    {
        $title_slug = Translit::slug($title);
        $slug = $title_slug . '-' . rand(1000, 9999);

        $where = array('content.type' => $type, 'info.slug' => $slug);

        if ($lang) {
            $where['info.language'] = $lang;
        }

        $item = self::find()
            ->alias('content')
            ->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id')
            ->where($where)
            ->one();

        if ($item) {
            $i = 0;

            do {
                $i++;
                $slug = $title_slug . '-' . rand(1000, 9999);
                $where['info.slug'] = $slug;

                $item = self::find()
                    ->alias('content')
                    ->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id')
                    ->where($where)
                    ->one();
            } while ($item && $i < 10000);
        }

        return $slug;
    }

    /**
     * Get relations array
     *
     * @param mixed $model
     * @param boolean $as_single
     * @return void
     */
    public static function getRelationsArray($model, $as_single = false)
    {
        $array = array();

        if ($as_single) {
            $segment_relation = SegmentRelations::find()
                ->where(['content_id' => $model->id])
                ->one();

            if ($segment_relation) {
                return $segment_relation->segment_id;
            }
        } else {
            $segment_relations = SegmentRelations::find()
                ->where(['content_id' => $model->id])
                ->all();

            if ($segment_relations) {
                foreach ($segment_relations as $segment_relation) {
                    $segment_id = $segment_relation->segment_id;

                    if (is_numeric($segment_id) && $segment_id > 0) {
                        $array[] = $segment_id;
                    }
                }
            }
        }

        return $array;
    }

    /**
     * Bind relations
     *
     * @param mixed $model
     * @return void
     */
    public static function bindRelations($model)
    {
        $old = array();
        $for_count = array();
        $segments = $model->segment_relations;

        $segment_relations = SegmentRelations::find()
            ->where(['content_id' => $model->id])
            ->all();

        if ($segment_relations) {
            foreach ($segment_relations as $segment_relation) {
                $prc_id = $segment_relation->segment_id;

                if (is_numeric($prc_id) && $prc_id > 0) {
                    $old[$prc_id] = $segment_relation;
                    $for_count[$prc_id] = $prc_id;
                } else {
                    $segment_relation->delete();
                }
            }
        }

        if (is_array($segments) && $segments) {
            foreach ($segments as $segment_type => $segment_value) {
                if (is_array($segment_value) && $segment_value) {
                    $segment_array = $segment_value;

                    foreach ($segment_array as $segment_id) {
                        if (is_numeric($segment_id) && isset($old[$segment_id])) {
                            unset($old[$segment_id]);
                        } elseif (is_numeric($segment_id) && $segment_id > 0) {
                            $item = new SegmentRelations();
                            $item->content_id = $model->id;
                            $item->segment_id = $segment_id;
                            $item->segment_type = $segment_type;
                            $item->save();

                            $for_count[$segment_id] = $segment_id;
                        }
                    }
                } elseif (is_numeric($segment_value)) {
                    $segment_id = $segment_value;

                    if (isset($old[$segment_id])) {
                        unset($old[$segment_id]);
                    } elseif ($segment_id > 0) {
                        $item = new SegmentRelations();
                        $item->content_id = $model->id;
                        $item->segment_id = $segment_id;
                        $item->segment_type = $segment_type;
                        $item->save();

                        $for_count[$segment_id] = $segment_id;
                    }
                }
            }
        }

        if ($old) {
            foreach ($old as $old_category) {
                $old_category->delete();
            }
        }

        if ($for_count) {
            foreach ($for_count as $fc_id) {
                Segment::countContents($fc_id);
            }
        }
    }

    /**
     * Menu page item render
     *
     * @param string $type
     * @param mixed $post
     * @param mixed $yii
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
                    ->select('content.*, info.*')
                    ->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id')
                    ->where(['in', 'content.id', $array])
                    ->andWhere(['content.type' => $type])
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
                        $value_json = ['name' => $value->info->title];

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
                    ->select('content.*, info.*')
                    ->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id')
                    ->where(['content.type' => $type])
                    ->andWhere(['like', 'info.title', $search])
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
                ->select('content.*, info.*')
                ->join('INNER JOIN', 'site_content_info info', 'info.content_id = content.id')
                ->where(['content.type' => $type, 'content.deleted' => 0, 'content.status' => 1])
                ->with([
                    'info' => function ($query) {
                        $query->andWhere(['language' => self::$selected_language]);
                    },
                ])
                ->orderBy(['content.created_on' => SORT_DESC])
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
                    'item_title' => $value->info->title,
                );

                $data[] = $yii->renderPartial('ajax-item', $value_data);
            }

            $output['html'] = $data;
        }

        return $output;
    }
}
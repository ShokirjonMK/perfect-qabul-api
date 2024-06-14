<?php

namespace backend\models;

use common\models\JobInfo;
use common\models\Job as CommonJob;

/**
 * Job model
 */
class Job extends CommonJob
{
    public static $type;

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
        $department = input_get('department');

        $admin_current_lang = admin_current_lang();

        self::$selected_language = array_value($admin_current_lang, 'lang_code', 'en');

        if (empty($sort) && array_value($args, 'sort')) {
            $sort = array_value($args, 'sort');
        }

        if (empty($department) && array_value($args, 'department')) {
            $department = array_value($args, 'department');
        }

        $query = self::find()
            ->with(['department.infoRelation'])
            ->join('INNER JOIN', 'job_info info', 'info.job_id = job.id');

        $query->andWhere(['language' => self::$selected_language]);

        if (is_numeric($department) && $department > 0) {
            $query->andWhere(['job.department_id' => $department]);
        }

        if ($search) {
            $query->andWhere(['like', 'info.name', $search]);
        } 
        
        if ($sort == 'a-z') {
            $sort_query = ['info.name' => SORT_ASC];
        } elseif ($sort == 'z-a') {
            $sort_query = ['info.name' => SORT_DESC];
        } elseif ($sort == 'oldest') {
            $sort_query = ['job.created_on' => SORT_ASC];
        } else {
            $sort_query = ['job.created_on' => SORT_DESC];
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
                'count' => self::itemsCount(['job.deleted' => 0]),
            ),
            'published' => array(
                'name' => _e('Published'),
                'active' => false,
                'count' => self::itemsCount(['job.deleted' => 0, 'job.status' => 1]),
            ),
            'unpublished' => array(
                'name' => _e('Unpublished'),
                'active' => false,
                'count' => self::itemsCount(['job.deleted' => 0, 'job.status' => 0]),
            ),
            'deleted' => array(
                'name' => _e('Deleted'),
                'active' => false,
                'count' => self::itemsCount(['job.deleted' => 1]),
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
            $log_data['job']['attrs'] = $model->getAttributes();
            $log_data['job']['old_attrs'] = array();

            // Create translations
            if ($active_languages) {

                foreach ($active_languages as $active_language) {

                    $lang_code = $active_language['lang_code'];

                    $new = new JobInfo();
                    $new->job_id = $model->id;
                    $new->language = $lang_code;
                    $new->name = $info->name[$lang_code];
                    $new->description = $info->description[$lang_code];

                    if ($new->save()) {
                        $log_data['job_info'][$new->language]['attrs'] = $new->getAttributes();
                        $log_data['job_info'][$new->language]['old_attrs'] = array();
                    }else{
                        dd($new->errors);
                    }
                }
            }

            // Set log
            set_log('admin', [
                'res_id' => $model->id,
                'type' => 'job',
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
        $current_user_id = current_user_id();

        // Save model
        $model->updated_on = $now_date;
        $model->updated_by = $current_user_id;

        $modelOldAttributes = $model->getOldAttributes();

        if ($model->save()) {
            $log_data['job']['attrs'] = $model->getAttributes();
            $log_data['job']['old_attrs'] = $modelOldAttributes;

            // Create translations
            if ($active_languages) {

                foreach ($active_languages as $active_language) {

                    $lang_code = $active_language['lang_code'];

                    $infoSingleLang = JobInfo::find()->where(['job_id' => $model->id, 'language' => $lang_code])->one();
                    if($infoSingleLang){
                        $infoSingleLang->name = $info->name[$lang_code];
                        $infoSingleLang->description = $info->description[$lang_code];

                        $infoOldAttributes = $infoSingleLang->getOldAttributes();

                        if ($infoSingleLang->save()) {
                            $log_data['job_info'][$infoSingleLang->language]['attrs'] = $infoSingleLang->getAttributes();
                            $log_data['job_info'][$infoSingleLang->language]['old_attrs'] = $infoOldAttributes;
                        }else{
                            dd($infoSingleLang->errors);
                        }
                    }
                    
                }
            }

            // Set log
            set_log('admin', [
                'res_id' => $model->id,
                'type' => 'job',
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
        $info = new JobInfo();
        $translations = JobInfo::find()->where(['job_id' => $id])->all();
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
     * Count all
     *
     * @param array $where
     * @return int
     */
    public static function itemsCount($where = array(), $where_in = array())
    {
        $query = self::find();
        $query->join('INNER JOIN', 'job_info info', 'info.job_id = job.id');

        if (is_array($where) && $where) {
            $query->andWhere($where);
        }

        if (is_array($where_in) && $where_in) {
            $query->andWhere($where_in);
        }

        $query->groupBy('info.job_id');
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
                $item = self::findOne($id);
                self::bulkActionWithJob('unpublish', $item);

                $output['message'] = _e('Item unpublished successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithJob('unpublish', $item);
                }

                $output['message'] = _e('Selected items have been successfully unpublished.');
            }
        } elseif ($action == 'publish') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::bulkActionWithJob('publish', $item);

                $output['message'] = _e('Item published successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithJob('publish', $item);
                }

                $output['message'] = _e('Selected items have been successfully published.');
            }
        } elseif ($action == 'trash') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::bulkActionWithJob('trash', $item);

                $output['message'] = _e('Item moved to the trash successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithJob('trash', $item);
                }

                $output['message'] = _e('Selected items have been successfully moved to the trash.');
            }
        } elseif ($action == 'restore') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::bulkActionWithJob('restore', $item);

                $output['message'] = _e('Item restored successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = self::findOne($items[$i]);
                    self::bulkActionWithJob('restore', $item);
                }

                $output['message'] = _e('Selected items have been successfully restored.');
            }
        } elseif ($action == 'delete') {
            $output['error'] = false;
            $output['success'] = true;

            if (is_numeric($id) && $id > 0) {
                $item = self::findOne($id);
                self::deleteJob($item);

                $output['message'] = _e('Item deleted successfully.');
            } elseif (!empty($items)) {
                for ($i = 0; $i < count($items); $i++) {
                    $item = $item = self::findOne($items[$i]);
                    self::deleteJob($item);
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
    public static function bulkActionWithJob($type, $model, $with_childs = true)
    {
        if ($model) {

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
     * Delete content
     *
     * @param [type] $model
     * @param boolean $with_childs
     * @return void
     */
    public static function deleteJob($model, $with_childs = true)
    {
        if ($model) {
            $trash_item['content'] = $model->getAttributes();

            if ($model->delete(false)) {
                $id = $model->id;
                $info = JobInfo::find()->where(['content_id' => $id])->all();

                if ($info) {
                    foreach ($info as $info_item) {
                        $trash_item['info'][] = $info_item->getAttributes();
                        $info_item->delete();
                    }
                }

                // Set trash
                set_trash(array(
                    'res_id' => $id,
                    'type' => 'job',
                    'data' => json_encode($trash_item),
                ));

                // Set log
                set_log('admin', [
                    'res_id' => $model->id,
                    'type' => 'job',
                    'action' => 'delete',
                    'data' => json_encode($trash_item),
                ]);
            }
        }
    }


}

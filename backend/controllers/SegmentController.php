<?php

namespace backend\controllers;

use base\BackendController;
use backend\models\Segment;
use common\models\SegmentInfos;
use Yii;
use yii\data\Pagination;
use yii\helpers\Url;

/**
 * Segment controller
 */
class SegmentController extends BackendController
{
    public $url;
    public $settings;
    public $segment_type;
    public $items_with_parent;

    /**
     * Before action
     *
     * @param $action
     * @return void
     */
    public function beforeAction($action)
    {
        $settings = array();
        $type = Yii::$app->request->get('type');

        if (!is_null($type) && $type) {
            $type = trim($type);
            $type = trim($type, '/');

            $segment_types = Segment::segmentTypes();
            $settings = filter_array($segment_types, ['slug' => $type], true);
        }

        if ($settings) {
            $this->settings = $settings;
            $this->url = "/segment/{$type}";
            $this->segment_type = array_value($settings, 'key');

            Segment::$type = $this->segment_type;
            Segment::$items_with_parent = array_value($settings, 'items_with_parent');
            Segment::$slug_generator = array_value($settings, 'slug_generator');
        }

        return parent::beforeAction($action);
    }

    /**
     * Displays all page
     *
     * @return string
     */
    public function actionAll()
    {
        $bulk_actions = array('publish', 'unpublish', 'trash');
        $where = ['segment.deleted' => 0];

        if ($this->settings) {
            return $this->page('all', $where, $bulk_actions);
        } else {
            return $this->errorPage();
        }
    }

    /**
     * Displays published page
     *
     * @return string
     */
    public function actionPublished()
    {
        $bulk_actions = array('unpublish', 'trash');
        $where = ['segment.status' => 1, 'segment.deleted' => 0];

        if ($this->settings) {
            return $this->page('published', $where, $bulk_actions);
        } else {
            return $this->errorPage();
        }
    }

    /**
     * Displays unpublished page
     *
     * @return string
     */
    public function actionUnpublished()
    {
        $bulk_actions = array('publish', 'trash');
        $where = ['segment.status' => 0, 'segment.deleted' => 0];

        if ($this->settings) {
            return $this->page('unpublished', $where, $bulk_actions);
        } else {
            return $this->errorPage();
        }
    }

    /**
     * Displays deleted page
     *
     * @return string
     */
    public function actionDeleted()
    {
        $bulk_actions = array('publish', 'unpublish', 'restore', 'delete');
        $where = ['segment.deleted' => 1];

        if ($this->settings) {
            return $this->page('deleted', $where, $bulk_actions);
        } else {
            return $this->errorPage();
        }
    }

    /**
     * Page
     *
     * @param string $type
     * @param array $where_query
     * @param array $bulk_actions
     * @param array $where_in
     * @return void
     */
    public function page($type, $where_query, $bulk_actions, $where_in = array())
    {
        $main_url = Url::to([$this->url]);

        $limit_default = 20;
        $sort_default = 'a-z';

        $ajax = input_post('ajax');
        $limit = input_get('limit', $limit_default);

        $args = ['sort' => $sort_default];
        $query = Segment::getItems('', $args)->andWhere($where_query);

        if (is_array($where_in) && $where_in) {
            $query->andWhere($where_in);
        }

        $count = $query->count();

        if ($ajax == 'bulk-actions') {
            $ajax_action = input_post('action');
            $ajax_items = input_post('items');
            $ajax_item_id = input_post('id');
            $items = explode(',', $ajax_items);

            $output = Segment::ajaxAction($ajax_action, $ajax_item_id, $items);

            echo json_encode($output);
            exit();
        }

        if (!empty($limit)) {
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        } else {
            $pagination = new Pagination(['totalCount' => $count]);
        }

        $segments = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $page_types = Segment::getPageTypes($type);

        return $this->render('page', array(
            'main_url' => $main_url,
            'page_types' => $page_types,
            'bulk_actions' => $bulk_actions,
            'limit_default' => $limit_default,
            'sort_default' => $sort_default,
            'segments' => $segments,
            'pagination' => $pagination,
        ));
    }

    /**
     * Displays create page
     *
     * @return string
     */
    public function actionCreate()
    {
        $main_url = Url::to([$this->url]);
        $all_url = Url::to([$this->url . '/all']);

        if (!$this->settings) {
            return $this->errorPage();
        }

        $model = new Segment();
        $info = new SegmentInfos();
        $post_item = Yii::$app->request->post();
        $lexicon = array_value($this->settings, 'lexicon');

        if ($post_item) {
            $submit_button = input_post('submit_button');

            $flash_type = 'success-error';
            $flash_message = _e('An error occurred while processing your request. Please try again.');

            if ($model->load($post_item) && $info->load($post_item)) {
                $id = Segment::createItem($model, $info, $post_item);

                $flash_type = 'success-alert';
                $flash_message = array_value($lexicon, 'successfully_created');

                Yii::$app->session->setFlash($flash_type, $flash_message);

                if ($submit_button == 'create_and_add_new') {
                    return $this->refresh();
                } else {
                    $url = rtrim($main_url, '/') . "/edit/?id={$id}";

                    if ($info->language) {
                        $url .= "&lang={$info->language}";
                    }

                    return $this->redirect($url);
                }
            }

            Yii::$app->session->setFlash($flash_type, $flash_message);
            return $this->refresh();
        }

        $this->registerJs(array(
            'dist/libs/tinymce/tinymce.min.js',
            'theme/components/tinymce-editor.js',
        ));

        return $this->render('create', array(
            'all_url' => $all_url,
            'main_url' => $main_url,
            'model' => $model,
            'info' => $info,
        ));
    }

    /**
     * Displays edit page
     *
     * @return string
     */
    public function actionEdit()
    {
        $main_url = Url::to([$this->url]);
        $all_url = Url::to([$this->url . '/all']);

        if (!$this->settings) {
            return $this->errorPage();
        }

        $id = Yii::$app->request->get('id');
        $post_item = Yii::$app->request->post();

        $item = Segment::getItemToEdit($id);
        $model = array_value($item, 'model');
        $info = array_value($item, 'info');
        $translations = array_value($item, 'translations');
        $lexicon = array_value($this->settings, 'lexicon');

        if (!$model || !$info) {
            return $this->render('error', array(
                'all_url' => $all_url,
                'main_url' => $main_url,
            ));
        }

        if ($post_item) {
            $model->cacheable = 0;
            $model->searchable = 0;
            $submit_button = input_post('submit_button');

            $flash_type = 'success-error';
            $flash_message = _e('An error occurred while processing your request. Please try again.');

            if ($model->load($post_item) && $info->load($post_item)) {
                $action = Segment::updateItem($model, $info, $post_item);

                if (is_string($action) && $action) {
                    $flash_type = 'error-alert';
                    $flash_message = $action;
                } else {
                    $flash_type = 'success-alert';
                    $flash_message = array_value($lexicon, 'successfully_updated');
                }
            }

            Yii::$app->session->setFlash($flash_type, $flash_message);

            if ($submit_button == 'create_and_add_new') {
                return $this->redirect(['create']);
            } else {
                return $this->refresh();
            }
        }

        $this->registerJs(array(
            'dist/libs/tinymce/tinymce.min.js',
            'theme/components/tinymce-editor.js',
        ));

        return $this->render('update', array(
            'all_url' => $all_url,
            'main_url' => $main_url,
            'model' => $model,
            'info' => $info,
            'translations' => $translations,
        ));
    }
}

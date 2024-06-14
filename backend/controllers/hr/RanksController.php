<?php

namespace backend\controllers\hr;

use base\BackendController;
use backend\models\Rank;
use common\models\RankInfo;
use Yii;
use yii\data\Pagination;
use yii\helpers\Url;

/**
 * Ranks controller
 */
class RanksController extends BackendController
{
    public $url = '/hr/ranks';
    public $settings;



    /**
     * Displays all page
     *
     * @return string
     */
    public function actionIndex()
    {
        $bulk_actions = array('publish', 'unpublish', 'trash');
        $where = ['rank.deleted' => 0];
        

        return $this->page('index', $where, $bulk_actions);
    }

    /**
     * Displays published page
     *
     * @return string
     */
    public function actionPublished()
    {
        $bulk_actions = array('unpublish', 'trash');
        $where = ['rank.status' => 1, 'rank.deleted' => 0];

        return $this->page('published', $where, $bulk_actions);
    }

    /**
     * Displays unpublished page
     *
     * @return string
     */
    public function actionUnpublished()
    {
        $bulk_actions = array('publish', 'trash');
        $where = ['rank.status' => 0, 'rank.deleted' => 0];

        return $this->page('unpublished', $where, $bulk_actions);
    }

    /**
     * Displays deleted page
     *
     * @return string
     */
    public function actionDeleted()
    {
        $bulk_actions = array('publish', 'unpublish', 'restore', 'delete');
        $where = ['rank.deleted' => 1];

        return $this->page('deleted', $where, $bulk_actions);
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
        $query = Rank::getItems('', $args)->andWhere($where_query);
        
        if (is_array($where_in) && $where_in) {
            $query->andWhere($where_in);
        }
        
        $count = $query->count();

        if ($ajax == 'bulk-actions') {
            $ajax_action = input_post('action');
            $ajax_items = input_post('items');
            $ajax_item_id = input_post('id');
            $items = explode(',', $ajax_items);

            $output = Rank::ajaxAction($ajax_action, $ajax_item_id, $items);

            echo json_encode($output);
            exit();
        }

        if (!empty($limit)) {
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        } else {
            $pagination = new Pagination(['totalCount' => $count]);
        }

        $content = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $page_types = Rank::getPageTypes($type);

        return $this->render('page', array(
            'main_url' => $main_url,
            'page_types' => $page_types,
            'bulk_actions' => $bulk_actions,
            'limit_default' => $limit_default,
            'sort_default' => $sort_default,
            'content' => $content,
            'pagination' => $pagination
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

        $model = new Rank();
        $info = new RankInfo();
        $post_item = Yii::$app->request->post();

        if ($post_item) {
            $submit_button = input_post('submit_button');

            $flash_type = 'success-error';
            $flash_message = _e('An error occurred while processing your request. Please try again.');

            if ($model->load($post_item) && $info->load($post_item)) {
                $id = Rank::createItem($model, $info, $post_item);

                $flash_type = 'success-alert';
                $flash_message = _e('successfully_created');

                Yii::$app->session->setFlash($flash_type, $flash_message);

                if ($submit_button == 'create_and_add_new') {
                    return $this->refresh();
                } else {
                    $url = rtrim($main_url, '/') . "/edit/?id={$id}";

                    if ($info->language) {
                        $url .= "&lang={$info->language}";
                    }

                    return $this->redirect(['index']);
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
            'main_url' => $main_url,
            'model' => $model,
            'info' => $info
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

        $id = Yii::$app->request->get('id');
        $post_item = Yii::$app->request->post();

        $item = Rank::getItemToEdit($id);
        $model = array_value($item, 'model');
        $info = array_value($item, 'info');
        $lexicon = array_value($this->settings, 'lexicon');

        if (!$model || !$info) {
            return $this->render('error', array(
                'all_url' => $all_url,
                'main_url' => $main_url,
            ));
        }

        if ($post_item) {
            $submit_button = input_post('submit_button');

            $flash_type = 'success-error';
            $flash_message = _e('An error occurred while processing your request. Please try again.');

            if ($model->load($post_item) && $info->load($post_item)) {
                $action = Rank::updateItem($model, $info, $post_item);

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
                return $this->redirect(['index']);
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
            'info' => $info
        ));
    }
}

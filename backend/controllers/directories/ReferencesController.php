<?php

namespace backend\controllers\directories;

use base\BackendController;
use backend\models\Reference;
use common\models\ReferenceInfo;
use Yii;
use yii\data\Pagination;
use yii\helpers\Url;

/**
 * References controller
 */
class ReferencesController extends BackendController
{
    public $url;
    public $settings = [];

    /**
     * Before action
     *
     * @param $action
     * @return void
     */
    public function beforeAction($action)
    {
        $this->settings['type'] = Yii::$app->request->get('type');
        $this->settings['title'] = $this->getTitle();
        $this->url = '/directories/references/' . $this->settings['type'];
        return parent::beforeAction($action);
    }

    private function getTitle(){
        $title = '';

        switch ($this->settings['type']) {
            case 'nationality': $title = ['plural' => _e('Nationalities'), 'singular' => 'nationality']; break;
            case 'residence-type': $title = ['plural' =>_e('Residence types'),'singular' => 'residence type']; break;
            case 'language': $title = ['plural' =>_e('Languages'),'singular' => 'language']; break;
            case 'science-degree': $title = ['plural' =>_e('Science degrees'),'singular' => 'science degree']; break;
            case 'scientific-title': $title = ['plural' =>_e('Scientific titles'),'singular' => 'scientific title']; break;
            case 'special-title': $title = ['plural' =>_e('Special titles'),'singular' => 'special title']; break;
            case 'basis-of-learning': $title = ['plural' =>_e('Basis of learning'),'singular' => 'basis of learning']; break;
        }

        return $title;
    }


    /**
     * Displays all page
     *
     * @return string
     */
    public function actionAll()
    {

        // dd($this->type);
        $bulk_actions = array('publish', 'unpublish', 'trash');
        $where = ['reference.deleted' => 0];
        

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
        $where = ['reference.status' => 1, 'reference.deleted' => 0];

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
        $where = ['reference.status' => 0, 'reference.deleted' => 0];

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
        $where = ['reference.deleted' => 1];

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

        $where_query = array_merge(['reference.type' => $this->settings['type']], $where_query);
        $query = Reference::getItems('', $args)->andWhere($where_query);
        
        if (is_array($where_in) && $where_in) {
            $query->andWhere($where_in);
        }
        
        $count = $query->count();

        if ($ajax == 'bulk-actions') {
            $ajax_action = input_post('action');
            $ajax_items = input_post('items');
            $ajax_item_id = input_post('id');
            $items = explode(',', $ajax_items);

            $output = Reference::ajaxAction($ajax_action, $ajax_item_id, $items);

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

        $page_types = Reference::getPageTypes($this->settings['type']);

        return $this->render('page', array(
            'main_url' => $main_url,
            'page_types' => $page_types,
            'bulk_actions' => $bulk_actions,
            'limit_default' => $limit_default,
            'sort_default' => $sort_default,
            'content' => $content,
            'settings' => $this->settings,
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
        $all_url = Url::to([$this->url . '/all']);

        $model = new Reference();
        $info = new ReferenceInfo();
        $post_item = Yii::$app->request->post();

        $model->type = $this->settings['type'];

        if ($post_item) {
            $submit_button = input_post('submit_button');

            $flash_type = 'success-error';
            $flash_message = _e('An error occurred while processing your request. Please try again.');

            if ($model->load($post_item) && $info->load($post_item)) {
                $id = Reference::createItem($model, $info, $post_item);

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

                    return $this->redirect($all_url);
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
            'info' => $info,
            'settings' => $this->settings,
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

        $item = Reference::getItemToEdit($id);
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
                $action = Reference::updateItem($model, $info, $post_item);

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
                return $this->redirect($all_url);
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
            'settings' => $this->settings,
        ));
    }
}

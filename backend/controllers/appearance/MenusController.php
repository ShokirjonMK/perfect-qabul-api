<?php

namespace backend\controllers\appearance;

use backend\models\Menu;
use base\BackendController;
use common\models\MenuGroup;
use common\models\MenuItems;
use Yii;
use yii\data\Pagination;
use yii\helpers\Url;

/**
 * Menus controller
 */
class MenusController extends BackendController
{
    public $url = '/appearance/menus';

    /**
     * Displays main page
     *
     * @return string
     */
    public function actionIndex()
    {
        $main_url = Url::to([$this->url]);
        $limit_default = 20;
        $sort_default = 'a-z';
        $bulk_actions = array('publish', 'unpublish', 'trash');

        $args = ['sort' => $sort_default];
        $where_query = ['deleted' => 0];

        $query = Menu::getItems('', $args)->andWhere($where_query);
        $count = $query->count();

        $ajax = input_post('ajax');
        $limit = input_get('limit', $limit_default);

        if ($ajax == 'bulk-actions') {
            $ajax_action = input_post('action');
            $ajax_items = input_post('items');
            $ajax_item_id = input_post('id');
            $items = explode(',', $ajax_items);

            $output = Menu::ajaxAction($ajax_action, $ajax_item_id, $items);

            echo json_encode($output);
            exit();
        }

        if (!empty($limit)) {
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        } else {
            $pagination = new Pagination(['totalCount' => $count]);
        }

        $items = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $page_types = Menu::getPageTypes('');

        return $this->render('index', array(
            'main_url' => $main_url,
            'page_types' => $page_types,
            'bulk_actions' => $bulk_actions,
            'limit_default' => $limit_default,
            'sort_default' => $sort_default,
            'items' => $items,
            'pagination' => $pagination,
        ));
    }

    /**
     * Displays published page
     *
     * @return string
     */
    public function actionPublished()
    {
        $main_url = Url::to([$this->url]);
        $limit_default = 20;
        $sort_default = 'a-z';
        $bulk_actions = array('unpublish', 'trash');

        $args = ['sort' => $sort_default];
        $where_query = ['deleted' => 0, 'status' => 1];

        $query = Menu::getItems('published', $args)->andWhere($where_query);
        $count = $query->count();

        $ajax = input_post('ajax');
        $limit = input_get('limit', $limit_default);

        if ($ajax == 'bulk-actions') {
            $ajax_action = input_post('action');
            $ajax_items = input_post('items');
            $ajax_item_id = input_post('id');
            $items = explode(',', $ajax_items);

            $output = Menu::ajaxAction($ajax_action, $ajax_item_id, $items);

            echo json_encode($output);
            exit();
        }

        if (!empty($limit)) {
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        } else {
            $pagination = new Pagination(['totalCount' => $count]);
        }

        $items = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $page_types = Menu::getPageTypes('published');

        return $this->render('index', array(
            'main_url' => $main_url,
            'page_types' => $page_types,
            'bulk_actions' => $bulk_actions,
            'limit_default' => $limit_default,
            'sort_default' => $sort_default,
            'items' => $items,
            'pagination' => $pagination,
        ));
    }

    /**
     * Displays unpublished page
     *
     * @return string
     */
    public function actionUnpublished()
    {
        $main_url = Url::to([$this->url]);
        $limit_default = 20;
        $sort_default = 'a-z';
        $bulk_actions = array('publish', 'trash');

        $args = ['sort' => $sort_default];
        $where_query = ['deleted' => 0, 'status' => 0];

        $query = Menu::getItems('unpublished', $args)->andWhere($where_query);
        $count = $query->count();

        $ajax = input_post('ajax');
        $limit = input_get('limit', $limit_default);

        if ($ajax == 'bulk-actions') {
            $ajax_action = input_post('action');
            $ajax_items = input_post('items');
            $ajax_item_id = input_post('id');
            $items = explode(',', $ajax_items);

            $output = Menu::ajaxAction($ajax_action, $ajax_item_id, $items);

            echo json_encode($output);
            exit();
        }

        if (!empty($limit)) {
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        } else {
            $pagination = new Pagination(['totalCount' => $count]);
        }

        $items = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $page_types = Menu::getPageTypes('unpublished');

        return $this->render('index', array(
            'main_url' => $main_url,
            'page_types' => $page_types,
            'bulk_actions' => $bulk_actions,
            'limit_default' => $limit_default,
            'sort_default' => $sort_default,
            'items' => $items,
            'pagination' => $pagination,
        ));
    }

    /**
     * Displays deleted page
     *
     * @return string
     */
    public function actionDeleted()
    {
        $main_url = Url::to([$this->url]);
        $limit_default = 20;
        $sort_default = 'a-z';
        $bulk_actions = array('publish', 'unpublish', 'restore', 'delete');

        $args = ['sort' => $sort_default];
        $where_query = ['deleted' => 1];

        $query = Menu::getItems('deleted', $args)->andWhere($where_query);
        $count = $query->count();

        $ajax = input_post('ajax');
        $limit = input_get('limit', $limit_default);

        if ($ajax == 'bulk-actions') {
            $ajax_action = input_post('action');
            $ajax_items = input_post('items');
            $ajax_item_id = input_post('id');
            $items = explode(',', $ajax_items);

            $output = Menu::ajaxAction($ajax_action, $ajax_item_id, $items);

            echo json_encode($output);
            exit();
        }

        if (!empty($limit)) {
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        } else {
            $pagination = new Pagination(['totalCount' => $count]);
        }

        $items = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $page_types = Menu::getPageTypes('deleted');

        return $this->render('index', array(
            'main_url' => $main_url,
            'page_types' => $page_types,
            'bulk_actions' => $bulk_actions,
            'limit_default' => $limit_default,
            'sort_default' => $sort_default,
            'items' => $items,
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
        $post_item = Yii::$app->request->post();
        $ajax_action = Yii::$app->request->post('ajax_action');

        $menu = new Menu();
        $model = new MenuGroup();
        $items = new MenuItems();

        if ($ajax_action) {
            $action_type = Yii::$app->request->post('action_type');
            $output = Menu::itemAddAjaxAction($action_type, $post_item, $this);

            echo json_encode($output);
            exit();
        } elseif ($post_item) {
            $submit_button = input_post('submit_button');

            $flash_type = 'success-error';
            $flash_message = _e('An error occurred while processing your request. Please try again.');

            if ($model->load($post_item)) {
                $id = Menu::createItem($model, $post_item);

                $flash_type = 'success-alert';
                $flash_message = _e('The menu was created successfully.');

                Yii::$app->session->setFlash($flash_type, $flash_message);

                if ($submit_button == 'create_and_add_new') {
                    return $this->refresh();
                } else {
                    return $this->redirect(['edit', 'id' => $id]);
                }
            }

            Yii::$app->session->setFlash($flash_type, $flash_message);
            return $this->refresh();
        }

        $this->registerJs(array(
            'dist/libs/sortablejs/sortable.min.js',
            'dist/libs/sortablejs/jquery-sortable.min.js',
            'dist/libs/tinymce/tinymce.min.js',
            'theme/components/tinymce-editor.js',
            'theme/components/menu-page.js',
        ));

        return $this->render('create', array(
            'main_url' => $main_url,
            'menu' => $menu,
            'model' => $model,
            'items' => $items,
        ));
    }

    /**
     * Displays edit page
     *
     * @return string
     */
    public function actionEdit($id)
    {
        $model = array();
        $menu = new Menu();

        $main_url = Url::to([$this->url]);
        $post_item = Yii::$app->request->post();
        $ajax_action = Yii::$app->request->post('ajax_action');

        if (is_numeric($id) && $id > 0) {
            $model = MenuGroup::findOne($id);
        }

        if (!$model) {
            return $this->render('error', array(
                'main_url' => $main_url,
            ));
        }

        if ($post_item) {
            $submit_button = input_post('submit_button');

            $flash_type = 'success-error';
            $flash_message = _e('An error occurred while processing your request. Please try again.');

            if ($ajax_action) {
                $action_type = Yii::$app->request->post('action_type');
                $output = Menu::itemAddAjaxAction($action_type, $post_item, $this);

                echo json_encode($output);
                exit();
            } elseif ($model->load($post_item)) {
                $action = Menu::updateItem($model, $post_item);

                if (is_string($action) && $action) {
                    $flash_type = 'error-alert';
                    $flash_message = $action;
                } else {
                    $flash_type = 'success-alert';
                    $flash_message = _e('The menu has been successfully updated.');
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
            'dist/libs/sortablejs/sortable.min.js',
            'dist/libs/sortablejs/jquery-sortable.min.js',
            'dist/libs/tinymce/tinymce.min.js',
            'theme/components/tinymce-editor.js',
            'theme/components/menu-page.js',
        ));

        return $this->render('update', array(
            'main_url' => $main_url,
            'menu' => $menu,
            'model' => $model,
        ));
    }
}

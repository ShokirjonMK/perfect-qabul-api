<?php

namespace frontend\controllers;

use common\models\ContentInfos;
use base\Frontend;
use base\FrontendController;
use common\models\Analytics;
use common\models\Content;
use common\models\Segment;
use Yii;


use yii\rest\ActiveController;

/**
 * Site controller
 */
class SiteController extends FrontendController
{
    /**
     * Actions
     */
    public function actions()
    {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }
    public function actionIndex()
    {
            return 1;
    }
    /**
     * Displays homepage
     *
     * @return mixed
     */
    public function actionHome()
    {

        $lang = get_current_lang();

        if ($lang) {
            $content = array();
            $set_view_file = 'index';

            $start_page = ContentInfos::find()
                ->alias('info')
                ->join('INNER JOIN', 'site_content content', 'info.content_id = content.id')
                ->where(['resource_type' => 'start_page'])
                ->andWhere(['info.language' => $lang])
                ->with('model')
                ->one();

            if ($start_page) {
                $content = $start_page;
                $object = Frontend::contentToController(null, null, $content);
            } else {
                $content = ContentInfos::find()
                    ->alias('info')
                    ->join('INNER JOIN', 'site_content content', 'info.content_id = content.id')
                    ->where(['resource_type' => 'home_page'])
                    ->andWhere(['info.language' => $lang])
                    ->with('model')
                    ->one();

                $object = Frontend::contentToController(null, null, $content);
            }

            if ($object) {
                $model = $object->model;
                $oldAttributes = $object->oldAttributes;

                $info = $model->info;
                $content = $model->content;

                $this->data = array(
                    'obj' => $object,
                    'info' => $info,
                    'content' => $content,
                    'oldAttributes' => $oldAttributes,
                );

                $this->meta['title'] = $info->title;
                $this->body_class[] = 'page-home content-id-' . $content->id;
                $this->viewPath = rtrim($this->theme_alias, '/') . '/views/content';

                \base\Container::push('frontend_walked_id_array', [$content->id]);

                return $this->renderView($content, $info);
            } else {
                $this->meta['title'] = _e('Home');
                $this->body_class[] = 'page-home';

                return $this->render('index');
            }

            return $this->render($set_view_file, ['page' => $content]);
        }

        return $this->errorPage();
    }

    /**
     * Displays error page
     *
     * @return mixed
     */
    public function actionError()
    {
        $this->meta['title'] = _e('Page Not Found');
        $exception = Yii::$app->errorHandler->exception;

        if ($exception !== null) {
            return $this->render('error', ['exception' => $exception]);
        }
    }

    /**
     * Displays maintenance page
     *
     * @return mixed
     */
    public function actionMaintenance()
    {
        $view_name = 'underconstruction';
        $view_file = rtrim($this->viewPath, '/') . '/' . $view_name . '.php';

        $data['brand_name'] = get_setting_value('brand_name');
        $data['site_name'] = get_setting_value('site_name');
        $data['message'] = get_setting_value('unavailable_message');

        if (is_file($view_file)) {
            return $this->renderPartial($view_name, $data);
        } else {
            $this->viewPath = '@frontend/views/system';
            return $this->renderPartial($view_name, $data);
        }
    }

    /**
     * Displays locked with password page
     *
     * @return mixed
     */
    public function actionStoreLocked()
    {

        return "Not found";
        $data['error'] = '';
        $session = Yii::$app->session;

        $view_name = 'locked-with-password';
        $view_file = rtrim($this->viewPath, '/') . '/' . $view_name . '.php';

        $post = Yii::$app->request->post('form_action');
        $password = get_setting_value('site_password');
        $master_pass = Yii::$app->params['site_master_pass'];

        if ($post == 'store_pass') {
            $post_pass = Yii::$app->request->post('password');

            if ($post_pass == $password || $post_pass == $master_pass) {
                $session->set('master_pass_check', 'pass');

                return $this->refresh();
            } else {
                $data['error'] = 'Password is wrong! Please check and try again!';
            }
        }

        if (is_file($view_file)) {
            return $this->renderPartial($view_name, $data);
        } else {
            $this->viewPath = '@frontend/views/system';
            return $this->renderPartial($view_name, $data);
        }
    }

    /**
     * Displays search page
     *
     * @return mixed
     */
    public function actionSearch()
    {
        $this->meta['title'] = _e('Search');
        $this->body_class[] = 'page-search';

        return $this->render('search');
    }

    /**
     * Ajax requests
     *
     * @return mixed
     */
    public function actionAjax()
    {
        $xre = input_post('xre');
        $ajax_type = input_post('ajax');
        $file = $this->theme_path . "app/ajax/{$ajax_type}.php";

        if ($xre == 'analytics') {
            $output['error'] = false;
            $output['success'] = true;
            $output['message'] = '';

            Analytics::ajax();
        } elseif (is_file($file)) {
            $output = include $file;
        } else {
            $output['error'] = true;
            $output['success'] = false;
            $output['message'] = _e('An error occurred while processing your request. Please try again.');
        }

        return $this->asJson($output);
    }

    /**
     * Displays content
     *
     * @return mixed
     */
    public function actionContent()
    {
        $object = array();
        $is_segment = false;
        $url_parsed = get_parsed_url();

        if ($url_parsed) {
            $special_type = false;
            $slug_type = $url_parsed[0];

            if (count($url_parsed) > 1) {
                $content_types = Content::contentTypes();
                $segment_types = Segment::segmentTypes();

                $content_item = filter_array($content_types, ['slug' => $slug_type], true);
                $segment_item = filter_array($segment_types, ['slug' => $slug_type], true);

                if ($content_item) {
                    $special_type = true;
                    unset($url_parsed[0]);

                    $url_array = array_values($url_parsed);
                    $lexicon = array_value($content_item, 'lexicon');

                    $this->meta['title'] = array_value($lexicon, 'title');
                    $this->body_class = ['page-with-content', 'content-type-' . $slug_type];

                    $where_query = array('content.type' => array_value($content_item, 'key'));
                    $object = Frontend::contentToController($url_array, null, $where_query);
                } elseif ($segment_item) {
                    $is_segment = true;
                    $special_type = true;
                    unset($url_parsed[0]);

                    $url_array = array_values($url_parsed);
                    $lexicon = array_value($segment_item, 'lexicon');

                    $this->meta['title'] = array_value($lexicon, 'title');
                    $this->body_class = ['page-with-segment', 'segment-type-' . $slug_type];

                    $where_query = array('segment.type' => array_value($segment_item, 'key'));
                    $object = Frontend::segmentToController($url_array, null, $where_query);
                }
            }

            if (!$special_type) {
                $this->meta['title'] = _e('Page');
                $this->body_class = ['page-with-content', 'content-type-page'];

                $where_query = array('content.type' => 'page');
                $object = Frontend::contentToController($url_parsed, null, $where_query);
            }
        }

        if ($object) {
            $model = $object->model;
            $oldAttributes = $object->oldAttributes;

            // Check type
            if ($is_segment) {
                $info = $model->info;
                $segment = $model->segment;

                $this->data = array(
                    'obj' => $object,
                    'info' => $info,
                    'segment' => $segment,
                    'oldAttributes' => $oldAttributes,
                );

                $this->meta['title'] = $info->title;
                $this->body_class[] = 'segment-id-' . $segment->id;
                $this->viewPath = rtrim($this->theme_alias, '/') . '/views/segment';

                return $this->renderView($segment, $info);
            } else {
                $info = $model->info;
                $content = $model->content;

                $this->data = array(
                    'obj' => $object,
                    'info' => $info,
                    'content' => $content,
                    'oldAttributes' => $oldAttributes,
                );

                $this->meta['title'] = $info->title;
                $this->body_class[] = 'content-id-' . $content->id;
                $this->viewPath = rtrim($this->theme_alias, '/') . '/views/content';

                return $this->renderView($content, $info);
            }

            return $this->errorPage();
        } else {
            return $this->errorPage();
        }
    }

    /**
     * Render
     *
     * @param mixed $model
     * @param mixed $info
     * @return mixed
     */
    private function renderView($model, $info)
    {
        $id = $model->id;
        $meta = $info->meta;
        $type = $model->type;
        $set_view_file = 'index';
        $view = str_replace('.php', '', $model->view);
        $layout = str_replace('.php', '', $model->layout);
        $template = str_replace('.php', '', $model->template);

        // Check meta
        if (is_array($meta) && $meta) {
            $meta_array = $meta;

            $this->meta['title'] = array_value($meta_array, 'meta_title', $info->title, true);
            $this->meta['keywords'] = array_value($meta_array, 'focus_keywords');
            $this->meta['description'] = array_value($meta_array, 'meta_description');
        }

        // Check layout
        if ($layout) {
            $layout_file = $this->theme_path . 'layouts/' . $layout . '.php';

            if (is_file($layout_file)) {
                $this->layout = $layout;
            }
        }

        // Check type file
        $type_file = rtrim($this->viewPath, '/') . '/' . $type . '.php';

        if (is_file($type_file)) {
            $set_view_file = $type;
        }

        // Check resource file
        $resource_type = $model->resource_type;

        if ($resource_type && $resource_type != 'default') {
            $type_file = rtrim($this->viewPath, '/') . '/' . $resource_type . '-all.php';

            if (is_file($type_file)) {
                $set_view_file = $resource_type . '-all';
            }
        }

        // Check view
        $view_with_id = "id-{$id}";
        $view_with_id_file = rtrim($this->viewPath, '/') . '/' . $view_with_id . '.php';

        if (is_file($view_with_id_file)) {
            $set_view_file = $view_with_id;
        } elseif ($view) {
            $view = trim($view, '/');
            $view_array = explode('/', $view);

            if (count($view_array) > 2 && $view_array[0] == 'views') {
                unset($view_array[0]);
                $this->viewPath = rtrim($this->theme_alias, '/') . '/views/';

                $view = implode('/', $view_array);
                $view_file = rtrim($this->viewPath, '/') . '/' . $view . '.php';
            } else {
                $view_file = rtrim($this->viewPath, '/') . '/' . $view . '.php';
            }

            if (is_file($view_file)) {
                $set_view_file = $view;
            }
        }

        // Check template
        if ($template) {
            $template_file = $this->theme_path . 'templates/' . $template . '.php';

            if (is_file($template_file)) {
                $this->viewPath = rtrim($this->theme_alias, '/') . '/templates';
                return $this->render($template, $this->data);
            }
        }

        return $this->render($set_view_file, $this->data);
    }
}

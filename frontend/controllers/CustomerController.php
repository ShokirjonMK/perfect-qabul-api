<?php

namespace frontend\controllers;

use base\Container;
use base\Frontend;
use base\FrontendController;
use common\models\Profile;

/**
 * Customer controller
 */
class CustomerController extends FrontendController
{
    /**
     * Displays customer
     *
     * @return mixed
     */
    public function actionInit()
    {
        $object = array();
        $url_parsed = get_parsed_url();

        if ($url_parsed) {
            unset($url_parsed[0]);
            $object = Frontend::customerToController($url_parsed);
        }

        if ($object) {
            $model = $object->model;
            $oldAttributes = $object->oldAttributes;

            $customer = $model->customer;
            $profile = $model->profile;
            $set_view_file = 'index';
            $fullname = Profile::getFullname($profile);

            $this->data = array(
                'obj' => $object,
                'customer' => $customer,
                'profile' => $profile,
                'oldAttributes' => $oldAttributes,
            );

            $this->meta['title'] = $fullname;
            $this->body_class = ['page-with-customer', 'customer-id-' . $customer->id];

            $meta = $customer->meta;
            $view = str_replace('.php', '', $customer->view);
            $layout = str_replace('.php', '', $customer->layout);
            $template = str_replace('.php', '', $customer->template);

            // Check meta
            if ($meta && !is_null($meta)) {
                $meta_array = json_decode($meta, true);

                $this->meta['title'] = array_value($meta_array, 'meta_title', $fullname, true);
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

            // Check view
            $view_with_id = "id-{$customer->id}";
            $view_with_id_file = rtrim($this->viewPath, '/') . '/' . $view_with_id . '.php';

            if (is_file($view_with_id_file)) {
                $set_view_file = $view_with_id;
            } elseif ($view) {
                $view_file = rtrim($this->viewPath, '/') . '/' . $view . '.php';

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

            // Analytics run
            Container::push('analytics-js', "web_analytics.init('customer');");

            return $this->render($set_view_file, $this->data);
        } else {
            $this->viewPath = rtrim($this->theme_alias, '/') . '/views/site';
            return $this->errorPage();
        }
    }
}

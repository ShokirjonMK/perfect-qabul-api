<?php

namespace backend\controllers\system;

use backend\models\Locations;
use base\BackendController;
use base\libs\Translit;
use common\models\Countries;
use common\models\Regions;
use Yii;
use yii\data\Pagination;
use yii\helpers\Url;

/**
 * Locations controller
 */
class LocationsController extends BackendController
{
    public $url = '/system/locations';

    /**
     * Displays main page
     *
     * @return string
     */
    public function actionIndex()
    {
        $main_url = Url::to([$this->url]);
        $countries = Countries::find()->all();

        // Checking temp file existence
        $temp = new \base\libs\Temp();
        $temp_file = $temp->getArray('system', 'countries');

        // if temp file not exists, then create
        if (!$temp_file) {
            // Create temp
            create_temp_for('countries');
        }



        return $this->render('index', array(
            'main_url' => $main_url,
            'countries' => $countries,
        ));
    }

    /**
     * Displays cities page
     *
     * @return string
     */
    public function actionCities()
    {
        $main_url = Url::to([$this->url]);
        $query = Locations::getCitiesAll();
        $ajax = input_post('ajax');
        $limit = input_get('limit');
        $count = $query->count();

        if ($ajax == 'bulk-actions') {
            $output['error'] = true;
            $output['success'] = false;

            $ajax_action = input_post('action'); // Action nomi
            $ajax_items = input_post('items'); // Select qilingan elementlar ID si
            $ajax_item_id = input_post('id');

            if ($ajax_action == 'delete') {
                Regions::findOne($ajax_item_id)->delete();
                $output['error'] = false;
                $output['success'] = true;
                $output['message'] = _e('Selected item has been successfully deleted.');

                // Create temp
                create_temp_for('regions');
            }

            echo json_encode($output);
            exit();
        }

        if (!empty($limit)) {
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        } else {
            $pagination = new Pagination(['totalCount' => $count]);
        }

        $city = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('cities', array(
            'main_url' => $main_url,
            'city' => $city,
            'pagination' => $pagination,
        ));
    }

    /**
     * Displays regions page
     *
     * @return string
     */
    public function actionRegions()
    {
        $main_url = Url::to([$this->url]);
        $query = Locations::getRegionAll();
        $ajax = input_post('ajax');
        $limit = input_get('limit');
        $count = $query->count();

        if ($ajax == 'bulk-actions') {
            $output['error'] = true;
            $output['success'] = false;

            $ajax_action = input_post('action'); // Action nomi
            $ajax_items = input_post('items'); // Select qilingan elementlar ID si
            $ajax_item_id = input_post('id');

            if ($ajax_action == 'delete') {
                Regions::findOne($ajax_item_id)->delete();
                $output['error'] = false;
                $output['success'] = true;
                $output['message'] = _e('Selected item has been successfully deleted.');

                // Create temp
                create_temp_for('regions');
            }

            echo json_encode($output);
            exit();
        }

        if (!empty($limit)) {
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        } else {
            $pagination = new Pagination(['totalCount' => $count]);
        }

        $region = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        return $this->render('regions', array(
            'main_url' => $main_url,
            'region' => $region,
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
        $type = input_get('type');
        $main_url = Url::to([$this->url]);
        $post_item = Yii::$app->request->post();

        $this->registerJs(array(
            'js/custom.js',
        ));

        if ($type == 'city') {
            $model = new Regions();
            $model->type = Regions::TYPE_CITY;

            if ($post_item) {
                $submit_button = input_post('submit_button');

                if ($submit_button == 'create_and_add_new' && $model->load($post_item)) {
                    $model->slug = Translit::slug($model->name) . '-' . rand(1000, 9999);
                    $model->created_by = current_user_id();
                    $model->updated_by = current_user_id();
                    $model->save();

                    Yii::$app->session->setFlash('success-alert', _e("The region was created successfully."));
                    return $this->refresh();
                } elseif ($model->load($post_item)) {
                    $model->slug = Translit::slug($model->name) . '-' . rand(1000, 9999);
                    $model->created_by = current_user_id();
                    $model->updated_by = current_user_id();
                    $model->save();

                    // Create temp
                    create_temp_for('regions');

                    Yii::$app->session->setFlash('success-alert', _e("The region was created successfully."));
                    return $this->redirect(array('edit', 'type' => $type, 'id' => $model->id));
                }
            }

            return $this->render('create-city', array(
                'main_url' => $main_url,
                'model' => $model,
            ));
        } elseif ($type == 'region') {
            $model = new Regions();
            $model->type = Regions::TYPE_REGION;

            if ($post_item) {
                $submit_button = input_post('submit_button');

                if ($submit_button == 'create_and_add_new' && $model->load($post_item)) {
                    $model->slug = Translit::slug($model->name) . '-' . rand(1000, 9999);
                    $model->created_by = current_user_id();
                    $model->updated_by = current_user_id();
                    $model->save();

                    Yii::$app->session->setFlash('success-alert', _e("The city was created successfully."));
                    return $this->refresh();
                } elseif ($model->load($post_item)) {
                    $model->slug = Translit::slug($model->name) . '-' . rand(1000, 9999);
                    $model->created_by = current_user_id();
                    $model->updated_by = current_user_id();
                    $model->save();

                    // Create temp
                    create_temp_for('regions');

                    Yii::$app->session->setFlash('success-alert', _e("The city was created successfully."));
                    return $this->redirect(array('edit', 'type' => $type, 'id' => $model->id));
                }
            }

            return $this->render('create-region', array(
                'main_url' => $main_url,
                'model' => $model,
            ));
        } else {
            return $this->render('error', array(
                'main_url' => $main_url,
            ));
        }
    }

    /**
     * Displays edit page
     *
     * @return string
     */
    public function actionEdit($id)
    {
        $type = input_get('type');
        $main_url = Url::to([$this->url]);
        $post_item = Yii::$app->request->post();

        $this->registerJs(array(
            'js/custom.js',
        ));

        if ($type == 'city') {
            $model = Regions::findOne($id);
            $city_name = $model->name;

            if (!$model) {
                return $this->render('error', array(
                    'main_url' => $main_url,
                ));
            }

            if ($post_item) {
                $submit_button = input_post('submit_button');

                if ($submit_button == 'create_and_add_new' && $model->load($post_item)) {
                    if ($city_name != $model->name) {
                        $model->slug = Translit::slug($model->name) . '-' . rand(1000, 9999);
                    }

                    $model->updated_on = date('Y-m-d H:i:s');
                    $model->updated_by = current_user_id();
                    $model->save();

                    Yii::$app->session->setFlash('success-alert', _e("The region has been successfully updated."));
                    return $this->redirect(['create?type=city']);
                } elseif ($model->load($post_item)) {
                    if ($city_name != $model->name) {
                        $model->slug = Translit::slug($model->name) . '-' . rand(1000, 9999);
                    }

                    $model->updated_on = date('Y-m-d H:i:s');
                    $model->updated_by = current_user_id();
                    $model->save();

                    // Create temp
                    create_temp_for('regions');

                    Yii::$app->session->setFlash('success-alert', _e("The region has been successfully updated."));
                    return $this->refresh();
                }
            }

            return $this->render('edit-city', array(
                'main_url' => $main_url,
                'model' => $model,
            ));
        } elseif ($type == 'region') {
            $model = Regions::findOne($id);
            $region_name = $model->name;

            if (!$model) {
                return $this->render('error', array(
                    'main_url' => $main_url,
                ));
            }

            if ($post_item) {
                $submit_button = input_post('submit_button');

                if ($submit_button == 'create_and_add_new' && $model->load($post_item)) {
                    if ($region_name != $model->name) {
                        $model->slug = Translit::slug($model->name) . '-' . rand(1000, 9999);
                    }

                    $model->updated_on = date('Y-m-d H:i:s');
                    $model->updated_by = current_user_id();
                    $model->save();

                    Yii::$app->session->setFlash('success-alert', _e("The city has been successfully updated."));
                    return $this->redirect(['create?type=region']);
                } elseif ($model->load($post_item)) {
                    if ($region_name != $model->name) {
                        $model->slug = Translit::slug($model->name) . '-' . rand(1000, 9999);
                    }

                    $model->updated_on = date('Y-m-d H:i:s');
                    $model->updated_by = current_user_id();
                    $model->save();

                    // Create temp
                    create_temp_for('regions');

                    Yii::$app->session->setFlash('success-alert', _e("The city has been successfully updated."));
                    return $this->refresh();
                }
            }

            return $this->render('edit-region', array(
                'main_url' => $main_url,
                'model' => $model,
            ));
        } else {
            return $this->render('error', array(
                'main_url' => $main_url,
            ));
        }
    }
}

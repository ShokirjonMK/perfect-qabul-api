<?php

namespace backend\controllers;

use base\BackendController;
use common\models\Job;
use common\models\Regions;

/**
 * JSON controller
 */
class JsonController extends BackendController
{

    /**
     * Before action
     *
     * @param $action
     * @return void
     */
    public function beforeAction($action)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return parent::beforeAction($action);
    }

    public function actionGetRegions($country_id = null)
    {
        return Regions::listRegions($country_id);
    }

    public function actionGetDistricts($region_id = null)
    {
        return Regions::listDistricts($region_id);
    }

    public function actionGetJobs($department_id = null)
    {
        return Job::listAll($department_id);
    }

}

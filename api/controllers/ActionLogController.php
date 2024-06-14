<?php

namespace api\controllers;

use common\models\model\ActionLog;
use common\models\model\Building;
use Yii;
use base\ResponseStatus;
use common\models\model\Translate;

class ActionLogController extends ApiActiveController
{
    public $modelClass = 'api\resources\Building';

    public function actions()
    {
        return [];
    }

    public $table_name = 'action_log';
    public $controller_name = 'ActionLog';

    public function actionIndex($lang)
    {
        $model = new ActionLog();

        $query = $model->find();

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);
        return $this->response(1, _e('Success'), $data);
    }

    public function actionView($lang, $id)
    {
        $model = ActionLog::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

}

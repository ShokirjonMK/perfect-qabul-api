<?php

namespace api\controllers;

use common\models\Area;
use Yii;
use base\ResponseStatus;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class AreaController extends ApiActiveController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => [
                            'admin',
                            'student'
                        ],
                    ]
                ],
            ],
        ]);
    }

    public $modelClass = 'common\models\Area';

    public function actions()
    {
        return [];
    }

    public $table_name = 'area';
    public $controller_name = 'Area';

    public function actionIndex($lang)
    {
        $model = new Area();

        $query = $model->find()
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);

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
        $model = Area::findOne($id);
            
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

}

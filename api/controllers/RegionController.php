<?php

namespace api\controllers;

use common\models\Region;
use Yii;
use base\ResponseStatus;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class RegionController extends ApiActiveController
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
                        ],
                    ]
                ],
            ],
        ]);
    }

    public function actions()
    {
        return [];
    }

    public $modelClass = 'common\models\Region';

    public $table_name = 'region';
    public $controller_name = 'Region';

    public function actionIndex($lang)
    {
        $model = new Region();

        $query = $model->find()
            ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionView($lang, $id)
    {
        $model = Region::findOne($id);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}

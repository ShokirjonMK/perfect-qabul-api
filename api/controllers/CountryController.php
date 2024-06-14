<?php

namespace api\controllers;

use common\models\Countries;
use Yii;
use base\ResponseStatus;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class CountryController extends ApiActiveController
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

    public $modelClass = 'api\resources\Country';

    public function actions()
    {
        return [];
    }


    public $table_name = 'country';
    public $controller_name = 'Country';

    public function actionIndex($lang)
    {
        $model = new Countries();

        $query = $model->find()
            ->where(['id' => [229,219,209,115,109]])
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
        $model = Countries::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }
}

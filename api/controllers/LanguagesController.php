<?php

namespace api\controllers;

use common\models\Languages;
use Yii;
use base\ResponseStatus;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class LanguagesController extends ApiActiveController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public $modelClass = 'api\resources\Languages';

    public function actions()
    {
        return [];
    }

    public function actionIndex($lang)
    {
        $model = new Languages();

        $query = $model->find()
            ->andWhere(['status' => 1]);
//            ->andWhere(['is_deleted' => 0]);
        //    ->andFilterWhere(['like', 'name', Yii::$app->request->get('query')]);

        $all = Yii::$app->request->get('all');
        if ($all != 1) {
            $query->andWhere(['status' => 1]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data =  $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate($lang)
    {
        $model = new Languages();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Languages::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Languages successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Languages::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Languages::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('Languages successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Languages::find()
            ->andWhere(['id' => $id])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Languages::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        // remove model
        $result = Languages::findOne($id);

        if ($result) {
            $result->is_deleted = 1;
            $result->update();

            return $this->response(1, _e('Languages succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}

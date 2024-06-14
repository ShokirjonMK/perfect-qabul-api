<?php

namespace api\controllers;

use common\models\Option;
use common\models\Question;
use Yii;
use base\ResponseStatus;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class QuestionController extends ApiController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            'admin'
                        ],
                    ],
                ],
            ],
        ]);
    }

    public $modelClass = 'common\models\Option';

    public function actions()
    {
        return [];
    }

    public $table_name = 'question';
    public $controller_name = 'Question';

    public function actionIndex($lang)
    {
        $model = new Question();

        $query = $model->find()
            ->andWhere(['is_deleted' => 0]);
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
        $model = new Question();
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Question::createItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionAdd()
    {
        $query = Question::find()->where(['subject_id' => 4])->limit(7)->all();
        foreach ($query as $item) {
            $new = new Question();
            $new->is_checked = 1;
            $new->subject_id = $item->subject_id;
            $new->text = $item->text;
            $new->save(false);

            $options = Option::find()->where(['question_id' => $item->id])->all();
            foreach ($options as $option) {
                $op = new Option();
                $op->question_id = $new->id;
                $op->text = $option->text;
                $op->is_correct = $option->is_correct;
                $op->save(false);
            }
        }
    }

    public function actionIsCheck($lang , $id) {
        $model = Question::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = Question::ischeck($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $model = Question::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $result = Question::updateItem($model, $post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionView($lang, $id)
    {
        $model = Question::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Question::findone([
            'id' => $id,
            'is_deleted' => 0
        ]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $model->is_deleted = 1;
            $model->save(false);
            return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }
}

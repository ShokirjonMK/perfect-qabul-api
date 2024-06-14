<?php

namespace api\controllers;


use common\models\DirectionSubject;
use common\models\Exam;
use common\models\ExamStudent;
use common\models\ExamStudentQuestion;
use common\models\ExamSubject;
use Yii;
use common\models\Direction;
use common\models\Translate;
use base\ResponseStatus;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class ExamSubjectController extends ApiActiveController
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
                    ]
                ],
            ],
        ]);
    }

    public $modelClass = 'common\models\ExamSubject';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_subject';
    public $controller_name = 'ExamSubject';

    public function actionUpdate($lang, $id)
    {
        $model = ExamSubject::findOne([
            'id' => $id,
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $post = Yii::$app->request->post();
        $result = ExamSubject::updateItem($model ,$post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully update.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }
}

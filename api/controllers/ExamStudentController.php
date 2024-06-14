<?php

namespace api\controllers;


use common\models\DirectionSubject;
use common\models\Exam;
use common\models\ExamStudent;
use common\models\ExamStudentQuestion;
use kartik\mpdf\Pdf;
use Yii;
use common\models\Direction;
use common\models\Translate;
use base\ResponseStatus;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;

class ExamStudentController extends ApiActiveController
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
                            'student',
                            'admin'
                        ],
                    ]
                ],
            ],
        ]);
    }

    public $modelClass = 'common\models\Exam';

    public function actions()
    {
        return [];
    }

    public $table_name = 'exam_student';
    public $controller_name = 'ExamStudent';

    public function actionIndex($lang)
    {
        $model = new ExamStudent();

        $query = $model->find()
            ->andWhere([$this->table_name . '.is_deleted' => 0]);

        if (isRole('student')) {
            $query = $query->andWhere(['user_id' => current_user_id()]);
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
        $post = Yii::$app->request->post();

        $result = ExamStudent::createItem($post);
        if ($result['is_ok']) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), $result['data'], null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionQuestion($lang, $id)
    {
        $model = ExamStudentQuestion::findOne([
            'id' => $id,
            'user_id' => current_user_id(),
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $post = Yii::$app->request->post();

        $result = ExamStudent::question($model ,$post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully update.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionFinish($lang, $id)
    {
        $student = current_student();
        $model = ExamStudent::findOne([
            'id' => $id,
            'user_id' => $student->user_id,
            'attempt_count' => $student->attempt_count,
            'is_deleted' => 0
        ]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->status == ExamStudent::STATUS_END) {
            return $this->response(0, _e('You have already completed the exam.'), null, null, ResponseStatus::OK);
        }
        $result = ExamStudent::finish($model);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully finished.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionIncrement()
    {
        $post = Yii::$app->request->post();

        $result = ExamStudent::increment($post);

        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully update.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null,  $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionAllFinish()
    {
        $post = Yii::$app->request->post();

        $result = ExamStudent::allFinish($post);
        if (!is_array($result)) {
            return $this->response(1, _e($this->controller_name . ' successfully created.'), null, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null,  $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionGet($lang, $id)
    {
        $model = ExamStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if (isRole('student')) {
            $student = current_student();
            if (!($model->attempt_count == $student->attempt_count && $model->student_id == $student->id)) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionView($lang, $id)
    {
        $model = Exam::find()
            ->andWhere([
                'id' => $id,
                'status' => 1,
                'is_deleted' => 0
            ])->one();
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if ($model->direction_id != current_student()->direction_id) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        $result = ExamStudent::createItem($model);
        if ($result['is_ok']) {
            if ($result['data']['status'] == 1) {
                return $this->response(1, _e($this->controller_name . ' successfully created.'), $result['data'], null, ResponseStatus::CREATED);
            } elseif ($result['data']['status'] == 2) {
                return $this->response(2, _e($this->controller_name . ' successfully created.'), $result['data'], null, ResponseStatus::CREATED);
            }
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result['errors'], ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionDelete($lang, $id)
    {
        $model = ExamStudent::find()
            ->where(['id' => $id, 'attempt_count' => 2, 'is_deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $result = ExamStudent::examStdDelete($model);
            if (!is_array($result)) {
                return $this->response(1, _e($this->controller_name . ' succesfully removed.'), null, null, ResponseStatus::OK);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null,  $result, ResponseStatus::UPROCESSABLE_ENTITY);
            }
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);
    }

    public function actionPdf()
    {
        $content = $this->renderPartial('pdf');
        $pdfDirectory = Yii::getAlias('@app/web/pdf');
        if (!is_dir($pdfDirectory)) {
            mkdir($pdfDirectory, 0777, true);
        }
        $pdfFilePath = $pdfDirectory . '/report.pdf';
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8,
            'format' => Pdf::FORMAT_A4,
            'orientation' => Pdf::ORIENT_PORTRAIT,
            'destination' => Pdf::DEST_FILE,
            'filename' => $pdfFilePath,
            'content' => $content,
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.kv-heading-1{font-size:18px}',

        ]);
        $pdf->render();
        return "PDF saved to $pdfFilePath";
    }
}

<?php

namespace api\controllers;

use api\components\HemisMK;
use api\forms\ConfirmInformation;
use api\forms\StepFour;
use api\forms\StepLoad;
use api\forms\StepOne;
use api\forms\StepThree;
use api\forms\StepTwo;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


use Yii;
use api\resources\StudentUser;
use api\resources\User;
use api\forms\StepOneLoad;

use base\ResponseStatus;
use common\models\Profile;
use common\models\Student;

use Exception;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class  StudentController extends ApiActiveController
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
                            'admin',
                        ],
                    ],
                    [
                        'actions' => ['view' , 'index'],
                        'allow' => true,
                        'roles' => [
                            'student'
                        ],
                    ]
                ],
            ],
        ]);
    }

    public $modelClass = 'api\resources\Student';

    public function actions()
    {
        return [];
    }

    public $table_name = 'student';

    public $controller_name = 'Student';

    public function actionIndex($lang)
    {
        $model = new Student();

        $query = $model->find()
            ->where(['student.is_deleted' => 0]);

        if (isRole('student')) {
            $query = $query->andWhere(['student.user_id' => current_user_id()]);
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate() {
        $post = Yii::$app->request->post();
        $student = Student::findOne([
            'user_id' => current_user_id()
        ]);
        if (!$student) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if (isset($post['step'])) {
            $step = $post['step'];
        } else {
            $step = $student->user->step;
        }
        if ($step > 0 && $step != User::STEP_6) {
            if ($step == 1 && $post['type'] == 1) {
                $model = new StepLoad();
                $this->load($model, $post);
                $result = StepLoad::stepOne($student, $model);
            } elseif ($step == 1 && $post['type'] == 2) {
                $model = new StepOne();
                $this->load($model, $post);
                $result = StepOne::stepOne($student, $model);
            } elseif ($step == 2) {
                $model = new StepTwo();
                $this->load($model, $post);
                $result = StepTwo::stepTwo($student, $model);
            } elseif ($step == 3) {
                $model = new StepThree();
                $this->load($model, $post);
                $result = StepThree::stepThree($student, $model);
            } elseif ($step == 4) {
                $model = new StepFour();
                $this->load($model, $post);
                $result = StepFour::stepFour($student, $model);
            } elseif ($step == 5) {
                $result = ConfirmInformation::stepConfirm($student);
            } else {
                return $this->response(0, _e('The number of steps is incorrect.'), null, [], ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, [], ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully create.'), $student, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($lang, $id)
    {
        $post = Yii::$app->request->post();
        $student = Student::findOne($id);
        if (!$student) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $step = $student->user->step;
        if ($step > 0 && $step != User::STEP_6) {
            if ($step == 1 && $post['type'] == 1) {
                $result = StepOneLoad::stepOne($student, $post);
            } elseif ($step == 1 && $post['type'] == 2) {
                $model = new StepOne();
                $this->load($model, $post);
                $result = StepOne::stepOne($student, $model);
            } elseif ($step == 2) {
                $result = StepOne::stepOne($student, $post);
            } elseif ($step == 3) {
                $result = StepTwo::stepThree($student, $post);
            } else {
                return $this->response(0, _e('There is an error occurred while processing.'), null, [], ResponseStatus::UPROCESSABLE_ENTITY);
            }
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, [], ResponseStatus::UPROCESSABLE_ENTITY);
        }

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully updated.'), $student, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionEduType($lang,$key = null)
    {
        $model = new StepTwo();
        return $model->eduTypesArray($key);
    }

    public function actionExamType($lang,$key = null)
    {
        $model = new StepFour();
        return $model->examTypesArray($key);
    }

    public function actionSertificateType($lang,$key = null, $level = null)
    {
        $model = new StepTwo();
        return $model->sertificateArray($key, $level);
    }

//    public function actionSertificateType($lang,$key = null, $level = null)
//    {
//        $model = new StepTwo();
//        return $model->sertificateArray($key, $level);
//    }


    public function actionView($lang, $id)
    {
        $model = Student::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        if (isRole('student')) {
            if ($model->user_id != current_user_id()) {
                return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
            }
        }

        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($lang, $id)
    {
        $model = Student::findOne(['id' => $id, 'is_deleted' => 0]);

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if (isRole('student')) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
        }


        $result = StudentUser::deleteItem($id);

        if (!is_array($result)) {
            return $this->response(1, _e('Student successfully deleted.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }

        /*
        $model = StudentUser::findOne(['id' => $id, 'is_deleted' => 0]);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }

        if ($model) {
            $user = User::findOne($model->user_id);
            $user->status = User::STATUS_BANNED;
            $user->save(false);
            $model->is_deleted = 1;
            $model->update();

            return $this->response(1, _e('Student succesfully removed.'), null, null, ResponseStatus::OK);
        }
        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::BAD_REQUEST);

        */
    }

}

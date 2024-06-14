<?php

namespace api\controllers;


use common\models\ExamStudent;
use common\models\University;
use Yii;
use common\models\Direction;
use common\models\Translate;
use base\ResponseStatus;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class SiteController extends Controller
{

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['?','@'],
                    ]
                ],
            ],
        ]);
    }

    public $modelClass = 'common\models\ExamStudent';

    public function actions()
    {
        return [];
    }

    public $table_name = 'site';
    public $controller_name = 'Site';

    public function actionView($lang, $id)
    {
        $model = ExamStudent::find()
            ->andWhere(['id' => $id, 'is_deleted' => 0])
            ->one();
        if (!$model) {
            return null;
        }
        $student = $model->student;
        $profile = $student->profile;
        $data = [
            'contract' => $model->contract,
            'finish_time' => $model->finish_time,
            'edu_year' => $model->eduYear,
            'id' => $model->id,
            'student' => [
                'first_name' => $profile->first_name,
                'last_name' => $profile->last_name,
                'middle_name' => $profile->middle_name,
                'eduForm' => $student->eduForm,
                'passport_number' => $student->passport_number,
                'passport_serial' => $student->passport_serial,
                'passport_pin' => $student->passport_pin,
                'invois' => $student->invois,
                'contract_type' => $student->contract_type,
                'entered' => $student->entered,
                'general_edu_type' => $student->general_edu_type,
                'edu_type' => $student->edu_type,
                'direction' => $student->direction,
                'phone' => $student->phone,
                'language_id' => $student->language_id,
                'exam_type' => $student->exam_type,
            ],
        ];
        return $data;
    }

}

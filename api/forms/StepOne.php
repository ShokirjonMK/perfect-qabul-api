<?php

namespace api\forms;

use api\resources\User;
use common\models\Languages;
use common\models\LoginHistory;
use common\models\Student;
use Yii;
use yii\base\Model;

class StepOne extends Model
{
    public $phone;
    public $first_name;
    public $last_name;
    public $middle_name;
    public $image;
    public $passport_number;
    public $passport_serial;
    public $passport_pin;
    public $passport_issued_date;
    public $passport_given_date;
    public $passport_given_by;
    public $birthday;
    public $gender;

    public function rules()
    {
        return [
            [['first_name','last_name','passport_number','passport_serial','passport_pin','birthday','gender'], 'required'],
            [['passport_number'], 'integer'],
            [['first_name','last_name','middle_name','image','passport_serial','passport_pin','passport_given_by'], 'string' , 'max' => 255],
            [['phone'], 'string' , 'max' => 50],
//            [['passport_issued_date','passport_given_date'], 'date' , 'format' => 'yyyy-mm-dd'],
        ];
    }

    public static function stepOne($student, $model) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$model->validate()) {
            $errors[] = $model->errors;
        } else {
            $student->first_name = $model->first_name;
            $student->last_name = $model->last_name;
            $student->middle_name = $model->middle_name;
            $student->image = $model->image;
            $student->passport_number = $model->passport_number;
            $student->passport_serial = $model->passport_serial;
            $student->passport_pin = $model->passport_pin;
//            $student->passport_issued_date = date('d-m-Y' , strtotime($model->passport_issued_date));
//            $student->passport_given_date = date('d-m-Y' , strtotime($model->passport_given_date));
//            $student->passport_given_by = $model->passport_given_by;
            $student->birthday = $model->birthday;
            $student->gender = $model->gender;
            if (!$student->validate()){
                $errors[] = $student->errors;
            } else {
                $student->save(false);
                $user = $student->user;
                $user->step = User::STEP_3;
                $user->save(false);
                $profile = $student->user->profile;
                if (isset($profile)) {
                    $profile->first_name = $student->first_name;
                    $profile->last_name = $student->last_name;
                    $profile->middle_name = $student->middle_name;
                    $profile->save(false);
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }
}

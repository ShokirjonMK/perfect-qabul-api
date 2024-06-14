<?php

namespace api\forms;

use api\resources\User;
use common\models\Languages;
use common\models\LoginHistory;
use common\models\Student;
use Yii;
use yii\base\Model;
use yii\httpclient\Client;

class StepLoad extends Model
{
    public $passport_serial;
    public $passport_number;
    public $birthday;

    public function rules()
    {
        return [
            [['birthday','passport_number','passport_serial'], 'required'],
            [['passport_number'], 'integer'],
            [['passport_serial'], 'string' , 'max' => 2],
            [['birthday'], 'date' , 'format' => 'dd-mm-yyyy'],
        ];
    }

    public static function stepOne($student, $model) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$model->validate()) {
            $errors[] = $model->errors;
        } else {

            $client = new Client();
            $url = 'https://api.online-mahalla.uz/api/v1/public/tax/passport';
            $params = [
                'series' => $model->passport_serial,
                'number' => $model->passport_number,
                'birth_date' => date('Y-m-d' , strtotime($model->birthday)),
            ];
            $response = $client->createRequest()
                ->setMethod('GET')
                ->setUrl($url)
                ->setData($params)
                ->send();



            if ($response->isOk) {

                $responseData = $response->data;
                $passport = $responseData['data']['info']['data'];
                $student->first_name = $passport['name'];
                $student->last_name = $passport['sur_name'];
                $student->middle_name = $passport['patronymic_name'];
                $student->passport_number = $model->passport_number;
                $student->passport_serial = $model->passport_serial;
                $student->passport_pin = $passport['pinfl'];

                $student->passport_issued_date = date("Y-m-d" , strtotime($passport['expiration_date']));
                $student->passport_given_date = date("Y-m-d" , strtotime($passport['given_date']));
                $student->passport_given_by = $passport['given_place'];
                $student->birthday = $model->birthday;
                $student->gender = $passport['gender'];
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

            } else {
                $errors[] = 'Error: ' . $response->statusCode . ' ' . $response->statusText;
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

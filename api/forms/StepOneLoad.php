<?php

namespace api\forms;

use api\resources\User;
use common\models\Languages;
use common\models\Student;
use Yii;
use yii\base\Model;
use yii\httpclient\Client;

class StepOneLoad extends Model
{
    public $passport_serial;
    public $passport_number;
    public $birthday;

    public function rules()
    {
        return [
            [['passport_serial','passport_number', 'birthday'], 'required'],
            [['passport_number'], 'integer'],
            [['passport_serial'], 'string'],
            [['birthday'], 'date' , 'yyyy-mm-dd'],
        ];
    }

    public static function stepOne($student, $model) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $client = new Client();

        $url = 'https://api.online-mahalla.uz/api/v1/public/tax/passport';
        $params = [
            'series' => 'AC',
            'number' => '1662283',
            'birth_date' => '2003-02-02',
        ];
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setData($params)
            ->send();

        dd($response->isOk);
        if ($response->isOk) {
            // Access the response data
            $responseData = $response->data;

            // Process or use the response data as needed
            dd($responseData);
        } else {
            // Handle the case when the request was not successful
            $errors[] = 'Error: ' . $response->statusCode . ' ' . $response->statusText;
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

<?php

namespace common\models;

use Yii;
use DateTime;
use DateTimeZone;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "sms".
 *
 * @property int $id
 * @property int $kimdan
 * @property int $kimga
 * @property string $title
 * @property int $status
 * @property int $date
 */

class Message extends \yii\db\ActiveRecord
{
    public static function sendSms($phone, $number)
    {
        $text = "Tashkent Perfect University ( qabul.perfectuniversity.uz ) qabul platformasi tasdiqlash kodi: " . $number;
        $data = '{
                "messages":
                    [
                        {
                        "recipient":'.$phone.',
                        "message-id":"abc000000001",
                            "sms":{
                                "originator": "3700",
                                "content": {
                                    "text": "'.$text.'"
                                }
                            }
                        }
                    ]
                }';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://send.smsxabar.uz/broker-api/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic ".Yii::$app->params['sms_key'],
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return 200;
    }


    public static function send($phone, $text)
    {
        $data = '{
                "messages":
                    [
                        {
                        "recipient":'.$phone.',
                        "message-id":"abc000000001",
                            "sms":{
                                "originator": "3700",
                                "content": {
                                    "text": "'.$text.'"
                                }
                            }
                        }
                    ]
                }';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://send.smsxabar.uz/broker-api/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic ".smsLogin(),
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $phone." --- ".$response;
    }


    public static function examFinish($phone)
    {
        $text = 'Tabriklaymiz! Siz TASHKENT PERFECT UNIVERSITY ga talabalikka qabul qilindingiz. To\'lov shartnomasini yuklab olishni unutmang. Shartnomangizni ( qabul.perfectuniversity.uz ) havola orqali yuklab oling. Aloqa markazi: 771292929';
        $data = '{
                "messages":
                    [
                        {
                        "recipient":'.$phone.',
                        "message-id":"abc000000001",
                            "sms":{
                                "originator": "3700",
                                "content": {
                                    "text": "'.$text.'"
                                }
                            }
                        }
                    ]
                }';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://send.smsxabar.uz/broker-api/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic ".smsLogin(),
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $phone." --- ".$response;
    }

    public static function examSertificate($phone)
    {
        $text = 'Tabriklaymiz! Siz TASHKENT PERFECT UNIVERSITY ga topshirgan til sertifikatingiz tasdiqlandi va sizga ushbu fandan maksimal ball berildi. Yangi to\'lov shartnomasini yuklab olishni unutmang. Shartnomangizni ( qabul.perfectuniversity.uz ) havola orqali yuklab oling. Aloqa markazi: 771292929';
        $data = '{
                "messages":
                    [
                        {
                        "recipient":'.$phone.',
                        "message-id":"abc000000001",
                            "sms":{
                                "originator": "3700",
                                "content": {
                                    "text": "'.$text.'"
                                }
                            }
                        }
                    ]
                }';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://send.smsxabar.uz/broker-api/send",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic ".smsLogin(),
                "cache-control: no-cache",
                "content-type: application/json",
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        return $phone." --- ".$response;
    }

}


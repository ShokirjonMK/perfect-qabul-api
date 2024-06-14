<?php

namespace console\controllers;

use common\models\Message;
use common\models\ResetPassword;
use common\models\User;
use common\models\Student;
use yii\console\Controller;
use yii\helpers\BaseConsole;

class SettingController extends Controller
{

    public function actionMictime()
    {
        $stds = Student::find()
            ->orderBy('id asc')
            ->all();
        foreach ($stds as $std) {
            $time = rand(1200000000000 , 1500000000000);
            $std->invois = $time;
            $std->save(false);
        }
    }

    public function actionSendSms()
    {
        $users = User::find()
            ->where(['status' => 0, 'attach_role' => 'student', 'username' => '998945055250'])
            ->all();

        $text = 'Hurmatli abituriyent! TASHKENT PERFECT UNIVERSITY ga ariza topshirishni davom ettirishingiz mumkin ( qabul.perfectuniversity.uz ). Aloqa markazi: +998(77)-129-29-29';
        foreach ($users as $user) {
            if (preg_match('/^\d+$/', $user->username)) {
                if (strlen($user->username) == 12) {
                    echo "111 \n";
                    $sendSms = Message::send((int)$user->username , $text);
                    echo $sendSms."\n";
                } else {
                    echo "222 \n";
                }
            } else {
                echo "333 \n";
            }
        }
    }

    public function actionReset()
    {
        $users = User::find()
            ->where(['attach_role' => 'student'])
            ->all();

        foreach ($users as $user) {
            $query = ResetPassword::findOne([
                'user_id' => $user->id,
            ]);
            if (!$query) {
                $new = new ResetPassword();
                $new->user_id = $user->id;
                $new->phone = $user->username;
                $new->save(false);
            }
        }
    }

    public function actionSms()
    {
        $students = Student::find()
            ->where(['status' => 1, 'is_deleted' => 0, 'exam_form' => 1])
            ->all();

        $count = 0;
        $text = 'Hurmatli abituriyent! Sizga TASHKENT PERFECT UNIVERSITY da 12.06.2024y. soat 10:00da offline imtihon o\'tkazilishini ma\'lum qilamiz. Shaxsni tasdiqlovchi hujjat(pasport) bilan universitet binosiga kelishingizni so\'raymiz. Address: Toshkent shahar, Yunusobod tumani, Bog’ishamol ko’chasi 220-uy. Aloqa markazi: 771292929';
        foreach ($students as $student) {
            $user = User::findOne(['id' => $student->user_id]);
            if ($user->attach_role == 'student') {
                if (preg_match('/^\d+$/', $user->username)) {
                    if (strlen($user->username) == 12) {
                        $count++;
                        echo $count ." \n";
                        $sendSms = Message::send((int)$user->username , $text);
                    }
                }
            }
        }
    }
    
}

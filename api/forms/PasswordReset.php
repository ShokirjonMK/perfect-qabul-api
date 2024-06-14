<?php

namespace api\forms;

use api\resources\Profile;
use api\resources\User;
use common\models\Languages;
use common\models\Message;
use common\models\model\LoginHistory;
use common\models\ResetPassword;
use common\models\Student;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class PasswordReset extends Model
{
    public $phone;

    /**
     * Rules
     *
     * @return array
     */

    public function rules()
    {
        return [
            [['phone'], 'required'],
            [['phone'], 'integer'],
            [
                'phone',
                'match',
                'pattern'=>'/^\+?\d{12}$/',
                'message'=> 'Enter the full phone number.',
            ],
        ];
    }


    public static function resetPass($model , $post)
    {
        $errors = [];
        if ($model->load($post, '')) {
            $result = $model->sms();
            if ($result['is_ok']) {
                $smsInfo = $result['data'];
                $data = [
                    'current_time' => time(),
                    'time' => $smsInfo->sms_time,
                    // 'sms_number' => $smsInfo->sms_number,
                    'phone' => $smsInfo->phone,
                    'key' => $smsInfo->reset_token,
                    'message' => 'SMS service is not working.',
                ];
            } else {
                $errors[] = $result['errors'];
            }
        } else {
            $errors[] = _e('Password cannot be blank.');
        }

        if (count($errors) == 0) {
            return ['is_ok' => true , 'data' => $data];
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    public function sms()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if (!$user || $user->status == 0) {
                return ['is_ok' => false, 'errors' => _e("Phone number not found.")];
            }
            $getSms = ResetPassword::findByUser($user->id , $user->username);
            if (!$getSms) {
                return ['is_ok' => false, 'errors' => _e("SMS ERRORS!")];
            }
            $smsModel = ResetPassword::isLimit($getSms);
            if ($smsModel->limit_count == 0) {
                return ['is_ok' => false, 'errors' => _e("Juda ko'p urinishlar bo'lgan 10-15 daqiqadan so'ng harakat qilib ko'ring!")];
            }

            if ($smsModel->sms_time < time() || $smsModel->sms_time == null) {
                $smsModel->sms_number = rand(100000, 999999);
                $smsModel->sms_time = strtotime('+2 minutes');
                Message::sendSms($user->username, $smsModel->sms_number);
            }

            $smsModel->reset_token = \Yii::$app->security->generateRandomString();
            $smsModel->save(false);
            return ['is_ok' => true , 'data' => $smsModel];
        } else {
            return ['is_ok' => false, 'errors' => $this->getErrorSummary(true)];
        }
    }


    public static function smsConfirm($post)
    {
        $errors = [];
        if (isset($post['key'])) {
            $smsToken = ResetPassword::findOne([
                'reset_token' => $post['key'],
                'phone' => $post['phone'],
            ]);
            if ($smsToken == null) {
                $errors[] = _e("The information was sent in error.");
            } else {
                if ($smsToken->sms_time > time()) {
                    if ($smsToken->sms_number == $post['sms_number']) {
                        $smsToken->reset_token = \Yii::$app->security->generateRandomString(10).time().\Yii::$app->security->generateRandomString();
                        $smsToken->save(false);
                        return ['is_ok' => true , 'data' => $smsToken];
                    } else {
                        $errors[] = _e('SMS code is incorrect.');
                    }
                } else {
                    $errors[] = _e('Code verification timed out. Send sms again!');
                }
            }
        } else {
            $errors[] = _e("Key must be sent!");
        }

        if (count($errors) == 0) {
            return ['is_ok' => true , 'data' => $smsToken];
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }


    public static function newPassword($post)
    {
        $errors = [];
        if (isset($post['key'])) {
            $smsToken = ResetPassword::findOne([
                'reset_token' => $post['key']
            ]);
            if ($smsToken == null) {
                $errors[] = _e("The information was sent in error.");
            } else {
                $user = User::findOne([
                    'attach_role' => 'student',
                    'id' => $smsToken->user_id,
                    'status' => 10
                ]);
                if ($user) {
                    $user->password_hash = \Yii::$app->security->generatePasswordHash($post['password']);
                    $user->auth_key = \Yii::$app->security->generateRandomString(20);
                    $user->password_reset_token = \Yii::$app->security->generateRandomString(15).time();
                    $user->access_token = \Yii::$app->security->generateRandomString();
                    $user->access_token_time = time();
                    $user->status = User::STATUS_ACTIVE;
                    $user->save(false);
                    $user->savePassword($post['password'], $user->id);
                    $data = [
                        'user_id' => $user->id,
                        'phone' => $user->username,
                        'role' => $user->attach_role,
                        'access_token' => $user->access_token,
                        'step' => $user->step,
                        'student' => $user->student,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                    return ['is_ok' => true , 'data' => $data];
                } else {
                    $errors[] = _e("Student user not found!");
                }
            }
        } else {
            $errors[] = _e("Key must be sent!");
        }

        return ['is_ok' => false, 'errors' => simplify_errors($errors)];
    }


    protected function getUser()
    {
        return User::findByUsername($this->phone);
    }

    protected static function getSms($user_id , $phone)
    {
        return ResetPassword::findByUser($user_id , $phone);
    }
}

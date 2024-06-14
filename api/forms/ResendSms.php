<?php

namespace api\forms;

use api\resources\User;
use common\models\Message;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class ResendSms extends Model
{
    public $key;

    /**
     * Rules
     *
     * @return array
     */

    public function rules()
    {
        return [
            [['key'], 'required'],
            [['key'], 'string'],
        ];
    }

    public static function resendSms($model , $post)
    {
        $errors = [];
        if ($model->load($post, '')) {
            $result = $model->sms();
            if ($result['is_ok']) {
                $user = $result['user'];
                if ($user->status == User::STATUS_PENDING) {
                    $data = [
                        'current_time' => time(),
                        'time' => $user->sms_time,
                        //'sms_number' => $user->sms_number,
                        'phone' => $user->username,
                        'key' => $user->password_reset_token
                    ];
                } else {
                    $errors[] = _e('User is not active.');
                }
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

    public function sms() {
        if ($this->validate()) {
            $user = User::findOne(['password_reset_token' => $this->key]);
            if ($user != null) {
                if ($user->status == User::STATUS_PENDING) {
                    if ($user->sms_time < time()) {
                        $user->sms_number = rand(100000, 999999);
                        $user->sms_time = strtotime('+2 minutes');
                        Message::sendSms($user->username, $user->sms_number);
                    }
                    $user->password_reset_token = \Yii::$app->security->generateRandomString(20).$user->username;
                    if (!$user->validate()) {
                        return ['is_ok' => false, 'errors' => $user->errors];
                    }
                    if ($user->save(false)) {
                        return ['is_ok' => true, 'user' => $user];
                    } else {
                        return ['is_ok' => false, 'errors' => _e("ERRORS!")];
                    }
                } else {
                    return ['is_ok' => false, 'errors' => _e("This number is registered!")];
                }
            } else {
                return ['is_ok' => false, 'errors' => _e("User not found.")];
            }
        } else {
            return ['is_ok' => false, 'errors' => $this->getErrorSummary(true)];
        }
    }

    protected function getUser()
    {
        return User::findByUsername($this->phone);
    }
}

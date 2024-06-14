<?php

namespace api\forms;

use api\resources\Profile;
use api\resources\User;
use common\models\Languages;
use common\models\Message;
use common\models\model\LoginHistory;
use common\models\Student;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class SignIn extends Model
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


    public static function signUp($model, $post)
    {
        $data = null;
        $errors = [];
        if ($model->load($post, '')) {
            $result = $model->registration();
            if ($result['is_ok']) {
                $user = $result['user'];
                if ($user->status === User::STATUS_PENDING) {
                    $resultCode = Message::sendSms($user->username, $user->sms_number);
                    if ($resultCode == 200) {
                        $data = [
                            'current_time' => time(),
                            'time' => $user->sms_time,
                            'phone' => $user->username,
                            'key' => $user->password_reset_token,
                        ];
                    } else {
                        $data = [
                            'current_time' => time(),
                            'time' => $user->sms_time,
                            //'sms_number' => $user->sms_number,
                            'phone' => $user->username,
                            'key' => $user->password_reset_token,
                            'message' => 'SMS service is not working.',
                        ];
                    }
                } else {
                    $errors[] = _e('User is not active.');
                }
            } else {
                $errors[] = $result['errors'];
            }
        } else {
            $errors[] = _e('Phone and password cannot be blank.');
        }

        if (count($errors) == 0) {
            return ['is_ok' => true, 'data' => $data];
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    public function registration()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user != null) {
                if ($user->status == 0) {
                    if ($user->sms_time < time()) {
                        $user->sms_number = rand(100000, 999999);
                        $user->sms_time = strtotime('+2 minutes');
                    }
                    $user->password_reset_token = \Yii::$app->security->generateRandomString(20).$this->phone;
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
                $user = new User();
                $user->username = $this->phone;
                $user->password_hash = \Yii::$app->security->generateRandomString(15);
                $user->auth_key = \Yii::$app->security->generateRandomString(20);
                $user->password_reset_token = \Yii::$app->security->generateRandomString(20).$this->phone;
                $user->access_token = null;
                $user->access_token_time = 0;
                $user->status = User::STATUS_PENDING;

                $user->attach_role = 'student';
                $user->sms_number = rand(100000, 999999);
                $user->sms_time = strtotime('+2 minutes');

                if (!$user->validate()) {
                    return ['is_ok' => false, 'errors' => $user->errors];
                }
                if ($user->save(false)) {
                    $auth = Yii::$app->authManager;
                    $authorRole = $auth->getRole('student');
                    if ($authorRole) {
                        $auth->assign($authorRole, $user->id);
                    }
                    $student = new Student();
                    $student->user_id = $user->id;
                    $student->phone = $user->username;
                    $student->save(false);

                    $profile = new Profile();
                    $profile->user_id = $user->id;
                    $profile->save(false);
                    return ['is_ok' => true, 'user' => $user];
                }
            }
        } else {
            return ['is_ok' => false, 'errors' => $this->getErrorSummary(true)];
        }
    }

    public static function smsNumber($post)
    {
        $errors = [];
        if (isset($post['key'])) {
            $user = User::findOne([
                'password_reset_token' => $post['key']
            ]);
            if ($user == null) {
                $errors[] = [$post['key'], _e("This user does not exist!")];
            } else {
                if ($user->status != 0) {
                    $errors[] = _e("You have already registered!");
                } else {
                    if ($user->sms_time < time()) {
                        $errors[] = _e("SMS code is outdated.");
                    } else {
                        if (isset($post['number']) && $user->sms_number == $post['number']) {
                            $user->password_reset_token = \Yii::$app->security->generateRandomString(20).$user->username;
                            $user->save(false);
                            $data = [
                                'key' => $user->password_reset_token
                            ];
                        } else {
                            $errors[] = _e("This number did not match the SMS code number.");
                        }
                    }
                }
            }
        } else {
            $errors[] = _e("Key must be sent!");
        }
        if (count($errors) == 0) {
            return ['is_ok' => true , 'data' => $data];
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    protected function getUser()
    {
        return User::findByUsername($this->phone);
    }
}

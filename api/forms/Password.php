<?php

namespace api\forms;

use api\resources\User;
use common\models\ResetPassword;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class Password extends Model
{
    public $password;
    public $key;

    /**
     * Rules
     *
     * @return array
     */

    public function rules()
    {
        return [
            [['password', 'key'], 'required'],
            [['password'], 'string' , 'min' => 5 , 'max' => 20],
            [['key'], 'string'],
        ];
    }

    public static function password($model , $post)
    {
        $errors = [];
        if ($model->load($post, '')) {
            $result = $model->savePassword();
            if ($result['is_ok']) {
                $user = $result['user'];
                if ($user->status === User::STATUS_ACTIVE) {
                    $data = [
                        'user_id' => $user->id,
                        'phone' => $user->username,
                        'role' => $user->attach_role,
                        'access_token' => $user->access_token,
                        'step' => $user->step,
                        'student' => $user->student,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
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

    public function savePassword() {
        if ($this->validate()) {
            $user = User::findOne(['password_reset_token' => $this->key]);
            if ($user == null) {
                return ['is_ok' => false, 'errors' => _e("This user does not exist!")];
            } else {
                if ($user->status == 0 && $user->attach_role == 'student') {
                    $user->password_hash = \Yii::$app->security->generatePasswordHash($this->password);
                    $user->auth_key = \Yii::$app->security->generateRandomString(20);
                    $user->password_reset_token = \Yii::$app->security->generateRandomString(15).time();
                    $user->access_token = \Yii::$app->security->generateRandomString();
                    $user->access_token_time = time();
                    $user->status = User::STATUS_ACTIVE;
                    $user->save(false);

                    $query = ResetPassword::findOne([
                        'user_id' => $user->id,
                    ]);
                    if (!$query) {
                        $new = new ResetPassword();
                        $new->user_id = $user->id;
                        $new->phone = $user->username;
                        $new->save(false);
                    }

                    $user->savePassword($this->password, $user->id);

                    return ['is_ok' => true, 'user' => $user];
                } else {
                    return ['is_ok' => false, 'errors' => _e("ERROR!")];
                }
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

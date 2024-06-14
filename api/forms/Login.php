<?php

namespace api\forms;

use api\resources\User;
use common\models\LoginHistory;
use Yii;
use yii\base\Model;

/**
 * Login form
 */
class Login extends Model
{
    public $phone;
    public $password;

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['phone', 'password'], 'required'],
            [['phone'], 'number'],
            [['password'], 'string'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || (!$user->validatePassword($this->password))) {
                $this->addError($attribute, _e('Incorrect login or password.'));
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool|object whether the user is logged in successfully
     */

    public function authorize()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            if ($user) {
                if ($user->status == User::STATUS_ACTIVE) {
                    $user->generateAccessToken();
                    $user->access_token_time = time();
                    $user->save(false);
                    return ['is_ok' => true, 'user' => $user];
                }
                return ['is_ok' => false, 'errors' => [_e('Unregistered user.')]];
            } else {
                return ['is_ok' => false, 'errors' => [_e('User not found.')]];
            }
        } else {
            return ['is_ok' => false, 'errors' => $this->getErrorSummary(true)];
        }
    }

    public static function logout()
    {
        $user = User::findOne(current_user_id());
        if (isset($user)) {
            LoginHistory::createItemLogin(current_user_id(), LoginHistory::LOGOUT);
            Yii::$app->user->logout();
            $user->access_token = NULL;
            $user->access_token_time = NULL;
            $user->save(false);
            // $user->logout();
            return true;
        }

        return false;
    }

    public static function loginMain($model, $post) {
        $data = null;
        $errors = [];
        if ($model->load($post, '')) {
            $result = $model->authorize();
            if ($result['is_ok']) {
                $user = $result['user'];
                if ($user->status === User::STATUS_ACTIVE) {
                    $profile = $user->profile;
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name,
                        'first_name' => $profile->first_name,
                        'avatar' => $profile->image,
                        'role' => $user->role,
                        'attach_role' => $user->attach_role,
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                } else {
                    $errors[] = [_e('User is not active.')];
                }
            } else {
                $errors[] = $result['errors'];
            }
        } else {
            $errors[] = [_e('Username and password cannot be blank.')];
        }

        if (count($errors) == 0) {
            return ['is_ok' => true, 'data' => $data];
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    public static function loginStd($model, $post)
    {
        $data = null;
        $errors = [];
        if ($model->load($post, '')) {
            $result = $model->authorize();
            if ($result['is_ok']) {
                $user = $result['user'];
                if ($user->status === User::STATUS_ACTIVE) {
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name ?? '',
                        'first_name' => $profile->first_name ?? '',
                        'role' => $user->attach_role,
                        'step' => $user->step,
                        'access_token' => $user->access_token,
                        'student' => $user->student,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                } else {
                    $errors[] = [_e('User is not active.')];
                }
            } else {
                $errors[] = $result['errors'];
            }
        } else {
            $errors[] = [_e('Username and password cannot be blank.')];
        }

        if (count($errors) == 0) {
            return ['is_ok' => true, 'data' => $data];
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        return User::findByUsername($this->phone);
    }
}

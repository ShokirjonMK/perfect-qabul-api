<?php

namespace api\resources;

use common\models\model\EncryptPass;
use common\models\model\Keys;
use common\models\model\PasswordEncrypts;
use Yii;
use yii\base\Model;

class Password extends Model
{
    use ResourceTrait;

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields =  [
            // 'created_by',
            // 'updated_by',
        ];

        return $fields;
    }

    public function decryptThisUser($user_id = NULL)
    {

        // return "asd";

        if (!isset($user_id)) {
            $user_id = current_user_id();
        }
        $user = User::findOne($user_id);
        if (!isset($user)) {
            $data['username'] = '))';
            $data['password'] = ':)';
            return $data;
        }
        $pass = PasswordEncrypts::find()
            ->where(['user_id' => $user->id])
            ->one();
        if (isset($pass)) {
            $key = Keys::findOne($pass->key_id);
            $dec_m = new EncryptPass();
            $ded = $dec_m->decrypt($pass->password, $key->name);
            $data['username'] = $user->username;
            $data['password'] = $ded;
        } else {
            $data['username'] = $user->username;
            $data['password'] = 'Not found ):';
        }
        return $data;
    }

    /**
     * Get User
     *
     * @return void
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}

<?php

namespace common\models;

use api\resources\ResourceTrait;
use api\resources\User;
use common\models\model\Key;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "key".
 *
 * @property int $id
 * @property string $name
 * @property int|null $order
 * @property int|null $status
 * @property int $created_at
 * @property int $updated_at
 * @property int $created_by
 * @property int $updated_by
 * @property int $is_deleted
 *
 */
class ResetPassword extends \yii\db\ActiveRecord
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    public static function tableName()
    {
        return 'reset_password';
    }

    public function rules()
    {
        return [
            [['user_id' , 'phone'], 'required'],
            [['user_id' , 'phone'], 'unique'],
            [['user_id' , 'phone', 'status', 'sms_token_time', 'reset_token_time', 'limit_count', 'limit_time', 'sms_time', 'sms_number', 'created_at', 'updated_at', 'created_by', 'updated_by', 'is_deleted'], 'integer'],
            [['sms_token' , 'reset_token'], 'string', 'max' => 255],
        ];
    }

    public static function findByUser($id , $phone)
    {
        return static::findOne(['user_id' => $id , 'phone' => $phone]);
    }

    public static function isLimit($model)
    {
        $time = time();
        if ($time < $model->sms_time) {
            if ($model->limit_time < $time) {
                $model->limit_time = $time + (10 * 60);
                $model->limit_count = 3;
                $model->save(false);
            } else {
                if ($model->limit_count > 0) {
                    $model->limit_count = $model->limit_count - 1;
                    $model->save(false);
                }
            }
        }
        return $model;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->limit_time = time() + (10 * 60);
            $this->limit_count = 5;
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }


}

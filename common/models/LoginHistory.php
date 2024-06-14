<?php

namespace common\models;

use api\resources\ResourceTrait;
use common\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "login".
 *
 * @property int $id
 * @property string $ip
 * @property int $user_id
 * @property string $device
 * @property string $device_id
 * @property string $type
 * @property string $model_device
 * @property int|null $status
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int $created_by
 * @property int $updated_by
 *
 * @property Users $user
 */
class LoginHistory extends \yii\db\ActiveRecord
{

    use ResourceTrait;

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    const LOGIN = 1;
    const LOGOUT = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'login_history';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['ip_data'], 'safe'],
            [['data', 'host'], 'string'],
            [['user_id', 'log_in_out', 'status', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['ip', 'device', 'device_id', 'type', 'model_device'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ip' => 'Ip',
            'ip_data' => 'ip_data',
            'user_id' => 'User ID',
            'device' => 'Device',
            'device_id' => 'Device ID',
            'type' => 'Type',
            'data' => 'Data',
            'log_in_out' => 'Data',
            'host' => 'Host',
            'model_device' => 'Model Device',
            'status' => _e('Status'),
            'created_at' => _e('Created At'),
            'updated_at' => _e('Updated At'),
            'created_by' => _e('Created By'),
            'updated_by' => _e('Updated By'),
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\api\resources\User::className(), ['id' => 'user_id']);
    }


    public function extraFields()
    {
        $extraFields =  [
            //            'department',
            'createdBy',
            'updatedBy',
            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }

    public static function createItemLogin($user_id = null, $log_in_out = self::LOGIN)
    {
        // dd($log_in_out);
        $model = new self;
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model->user_id = $user_id ?? current_user_id();
        $model->ip = getIpMK();
        $browser = getBrowser();

        $model->device_id = $browser['device'];
        $model->device = $browser['platform'];
        $model->data = json_encode($browser);
        $model->host = get_host();
        $model->log_in_out = $log_in_out;
        if ($ipData = getIpAddressData()) {
            $model->ip_data = $ipData;
        }

        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if ($model->save()) {
            // dd($model->errors);
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public static function createItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];


        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
        }
        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }


    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
            $this->created_on = date("Y-m-d H:i:s");
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}

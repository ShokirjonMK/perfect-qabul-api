<?php

namespace common\models\model;

use api\resources\ResourceTrait;
use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "action_log".
 *
 * @property int $id
 * @property int|null $created_at
 * @property string|null $controller
 * @property string|null $action
 * @property string|null $method
 * @property int|null $user_id
 * @property string|null $result
 * @property string|null $errors
 * @property string|null $data
 * @property string|null $post_data
 * @property string|null $get_data
 * @property string|null $message
 * @property string|null $browser
 * @property string|null $ip_address
 * @property string|null $host
 * @property string|null $ip_address_data
 * @property string|null $log_date
 * @property int|null $status

 */
class ActionLog extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'action_log';
    }

    /**
     * {@inheritdoc}
     */

    public function rules()
    {
        return [
            [['created_at', 'user_id', 'status'], 'integer'],
            [['result', 'errors', 'data', 'post_data', 'get_data', 'browser', 'host', 'ip_address_data'], 'string'],
            [['controller', 'action', 'method', 'message', 'log_date'], 'string', 'max' => 255],
            [['ip_address'], 'string', 'max' => 33],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'created_at' => _e('Created At'),
            'controller' => _e('Controller'),
            'action' => _e('Action'),
            'method' => _e('Method'),
            'user_id' => _e('User ID'),
            'result' => _e('Result'),
            'errors' => _e('Errors'),
            'data' => _e('Data'),
            'post_data' => _e('Post Data'),
            'get_data' => _e('Get Data'),
            'message' => _e('Message'),
            'browser' => _e('Browser'),
            'ip_address' => _e('Ip Address'),
            'host' => _e('Host'),
            'ip_address_data' => _e('Ip Address Data'),
            'log_date' => _e('Log Date'),
            'status' => _e('Status'),
                           
        ];
    }

    public function fields()
    {
        $fields = [
            'id',
            'created_at',
            'controller',
            'action',
            'method',
            'user_id',
            'result',
            'errors',
            'data',
            'post_data',
            'get_data',
            'message',
            'browser',
            'ip_address',
            'host',
            'ip_address_data',
            'log_date',
            'status',
        ];
        return $fields;
    }


    public function extraFields()
    {
        $extraFields = [
            'user',
        ];

        return $extraFields;
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

}

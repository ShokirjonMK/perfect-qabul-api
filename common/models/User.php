<?php

namespace common\models;

use common\models\AuthAssignment;
use common\models\Employee;
use common\models\Profile;
use common\models\Student;
use common\models\UserSubject;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_ACTIVE = 10;
    const STATUS_BANNED = 5;
    const STATUS_PENDING = 0;
    const STATUS_DELETE = 1;


    public static $url_prefix = 'U29';
    public $roleName;

    public static function tableName()
    {
        return '{{%users}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_PENDING]],
            [['template', 'layout', 'view'], 'string'],
            ['template', 'default', 'value' => ''],
            ['layout', 'default', 'value' => ''],
            ['view', 'default', 'value' => ''],
            [['roleName', 'access_token_time'], 'safe'],
            [['status', 'deleted', 'created_by', 'updated_by'], 'integer'],

        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => _e('ID'),
            'username' => _e('Username'),
            'auth_key' => _e('Auth key'),
            'password' => _e('Password'),
            'password_repeat' => _e('Confirm password'),
            'password_reset_token' => _e('Password reset token'),
            'email' => _e('Email address'),
            'template' => _e('Template'),
            'layout' => _e('Layout'),
            'view' => _e('View'),
            'roleName' => _e('Role'),
            'meta' => _e('Meta'),
            'status' => _e('Status'),
            'deleted' => _e('Deleted'),
            'created_at' => _e('Created on'),
            'updated_at' => _e('Updated on'),
            'verification_token' => _e('Verification token'),
            'password_hash' => _e('Password'),
        ];
    }

    public function fields()
    {
        $fields =  [
            'id',
            'username',
            'email',
            'roleName',
            'rolesAll',
            'status',
            'deleted',
            'created_at',
            'updated_at',

        ];

        return $fields;
    }

    public function extraFields()
    {
        $extraFields =  [
            'profile',
        ];

        return $extraFields;
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    public function getRole()
    {
        return $this->hasOne(AuthAssignment::class, ['user_id' => 'id']);
    }


    public function getRolesAll()
    {
        return $this->hasMany(AuthAssignment::class, ['user_id' => 'id']);
    }

    public function getRoleItem()
    {
        return ($this->role) ? $this->role->item_name : '';
    }

    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['user_id' => 'id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::class, ['user_id' => 'id']);
    }

    public function getUserSubjects()
    {
        return UserSubject::listAll($this->id) ?? [];
    }


    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        // user expiration time
        $apiExpirationTimeInSeconds = 60 * 60 * 24 * API_EXPIRATION_DAYS;
        return static::find()->where([
            'AND',
            ['access_token' => $token],
            ['<=', '(UNIX_TIMESTAMP() - access_token_time) ', $apiExpirationTimeInSeconds]
        ])->one();
    }

    public function getExpireTime()
    {
        return $this->access_token_time + API_EXPIRATION_DAYS * 24 * 60 * 60;
    }

    /**
     * Finds user by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_PENDING,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Get user ID
     *
     * @return void
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Get user auth key
     *
     * @return void
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validate auth key
     *
     * @param [type] $authKey
     * @return void
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates  access token
     */
    public function generateAccessToken()
    {
        $this->access_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes email verification token
     */
    public function removeEmailVerificationToken()
    {
        $this->verification_token = null;
    }

    /**
     * Finds customer by email
     *
     * @param string $email
     * @return static|null
     */
    public static function findCustomerByEmail($email)
    {
        return static::find()
            ->join('INNER JOIN', 'profile', 'profile.user_id = users.id')
            ->join('INNER JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->where(['auth_assignment.item_name' => 'customer'])
            ->andWhere(['users.email' => $email])
            ->one();
    }

    /**
     * Finds customer by username
     *
     * @param string $email
     * @return static|null
     */
    public static function findCustomerByUsername($username)
    {
        return static::find()
            ->join('INNER JOIN', 'profile', 'profile.user_id = users.id')
            ->join('INNER JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->where(['auth_assignment.item_name' => 'customer'])
            ->andWhere(['users.username' => $username])
            ->one();
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->created_by = current_user_id();
        } else {
            $this->updated_by = current_user_id();
        }
        return parent::beforeSave($insert);
    }
}

<?php

namespace api\resources;

use Yii;
use api\resources\Profile;
use common\models\Employee;
use common\models\Student;
use common\models\User as CommonUser;
use common\models\UserSubject;
use yii\behaviors\TimestampBehavior;
use yii\web\UploadedFile;

class ParentUser extends CommonUser
{
    use ResourceTrait;

    const UPLOADS_FOLDER = 'uploads/user-images/';
    const UPLOADS_FOLDER_STUDENT_IMAGE = 'uploads/student-images/';
    public $avatar;
    public $avatarMaxSize = 1024 * 200; // 200 Kb

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'email', 'status', 'password_hash'], 'required'],
            [['status'], 'integer'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['email'], 'email'],
            [['password_reset_token'], 'unique'],
//            [['avatar'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg', 'maxSize' => $this->avatarMaxSize],
            [['deleted'], 'default', 'value' => 0],
            [['template', 'layout', 'view'], 'default', 'value' => ''],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields =  [
            'id',
            'username',
            'first_name' => function ($model) {
                return $model->profile->first_name ?? '';
            },
            'last_name' => function ($model) {
                return $model->profile->last_name ?? '';
            },
            'role' => function ($model) {
                return $model->roleItem ?? '';
            },
            'avatar' => function ($model) {
                return $model->profile->image ?? '';
            },
            'email',
            'status'
            
        ];

        return $fields;
    }

    /**
     * Fields
     *
     * @return array
     */
    public function extraFields()
    {
        $extraFields =  [
            'created_at',
            'updated_at',
            'profile',
            'employee',
            'student'
        ];

        return $extraFields;
    }

    public function getPermissions(){
        if($this->roleItem){
            $authItem = AuthItem::find()->where(['name' => $this->roleItem])->one();
            $perms = $authItem->permissions;
            $result = [];
            if($perms && is_array($perms)){
                foreach ($perms as $row) {
                    $result[] = $row['name'];
                }
            }
            return $result;
        }else{
            return [];
        }
    }

    public function getProfile()
    {
        return $this->hasOne(Profile::class, ['user_id' => 'id']);
    }

    public function getEmployee()
    {
        return $this->hasOne(Employee::class, ['user_id' => 'id']);
    }

    public function getStudent()
    {
        return $this->hasOne(Student::class, ['user_id' => 'id']);
    }

    public static function statusList()
    {
        return [
            self::STATUS_ACTIVE => _e('Active'),
            self::STATUS_BANNED => _e('Banned'),
            self::STATUS_PENDING => _e('Pending'),
        ];
    }    
    
    public function upload()
    {
        if ($this->validate()) { 
            $fileName = \Yii::$app->security->generateRandomString(10) . '.' . $this->avatar->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = STORAGE_PATH . $miniUrl;
            $this->avatar->saveAs($url);
            return assets_url($miniUrl);
        } else {
            return false;
        }
    }
    
}
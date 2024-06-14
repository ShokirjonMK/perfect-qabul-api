<?php

namespace api\resources;

use common\models\AuthAssignment;
use common\models\model\TeacherAccess;
use common\models\model\PasswordEncrypts;
use Yii;
//use api\resources\Profile;
use common\models\model\Profile;
use common\models\model\EncryptPass;
use common\models\model\Keys;
use common\models\model\UserAccess;
use common\models\model\UserAccessType;
use common\models\User as CommonUser;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\web\UploadedFile;

class GetTeacher extends CommonUser
{
    use ResourceTrait;

    public $avatar;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
        ];
    }



    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields = [
            'id',
            'first_name' => function ($model) {
                return $model->profile->first_name ?? '';
            },
            'last_name' => function ($model) {
                return $model->profile->last_name ?? '';
            },
            'middle_name' => function ($model) {
                return $model->profile->middle_name ?? '';
            },
            'avatar' => function ($model) {
                return $model->profile->image ?? '';
            },
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
        $extraFields = [
            // 'profile',
            'department',
            // 'userAccess',

        ];

        return $extraFields;
    }


    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    // UserAccess
    public function getUserAccess() 
    {
        return $this->hasOne(UserAccess::className(), ['user_id' => 'id']);
    }

    // UserAccess
    public function getDepartment()
    {
        // return $this->userAccess->user_access_type_id;
        $user_access_type = $this->userAccess ? UserAccessType::findOne($this->userAccess->user_access_type_id) : null;

        return $user_access_type ? $user_access_type->table_name::findOne(['id' => $this->userAccess->table_id])->translate->name : '';
    }
}

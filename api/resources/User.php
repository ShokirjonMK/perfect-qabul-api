<?php

namespace api\resources;

use common\models\Area;
use common\models\AuthAssignment;
use common\models\ExamStudent;
use common\models\ExamStudentQuestion;
use common\models\ExamSubject;
use common\models\Nationality;
use common\models\Student;
use common\models\PasswordEncrypts;
use common\models\Keys;
use Yii;
//use api\resources\Profile;
use common\models\Profile;
use common\models\EncryptPass;
use common\models\LoginHistory;
use common\models\UserAccess;
use common\models\UserAccessType;
use common\models\User as CommonUser;
use yii\behaviors\TimestampBehavior;
use yii\db\Query;
use yii\web\UploadedFile;

class User extends CommonUser
{
    use ResourceTrait;

    const UPLOADS_FOLDER = 'uploads/user-images/';
    const PASSWORD_CHANED = 1;
    const PASSWORD_NO_CHANED = 0;
     const UPLOADS_FOLDER_PASSPORT = 'uploads/user-passport/';
     const UPLOADS_FOLDER_ALL_FILE = 'uploads/user-all-file/';

    public $avatar;
    public $passport_file;
    public $all_file;

    public $excel;


    public $password;
    public $avatarMaxSize = 1024 * 1024 * 2; // 200 Kb
    public $avatarExtension = 'png, jpg';

    const STEP_1 = 1;
    const STEP_2 = 2;
    const STEP_3 = 3;
    const STEP_4 = 4;
    const STEP_5 = 5;
    const STEP_6 = 6;


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
            [['username', 'status'], 'required'],
            [['status'], 'integer'],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['email'], 'email'],
            ['password','string', 'min'=>4, 'max'=>50],
            [['password_reset_token'], 'unique'],

            [['avatar'], 'file', 'skipOnEmpty' => true, 'extensions' => $this->avatarExtension, 'maxSize' => $this->avatarMaxSize],

            [['sms_number' , 'sms_time','step'], 'integer'],
            [['deleted'], 'default', 'value' => 0],
            ['is_changed', 'in', 'range' => [self::PASSWORD_CHANED, self::PASSWORD_NO_CHANED]],
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
            'username',
            'first_name' => function ($model) {
                return $model->profile->first_name ?? '';
            },
            'last_name' => function ($model) {
                return $model->profile->last_name ?? '';
            },
            'middle_name' => function ($model) {
                return $model->profile->middle_name ?? '';
            },
            'role' => function ($model) {
                return $model->roles ?? '';
            },
            'avatar' => function ($model) {
                return $model->profile->image ?? '';
            },
            'status',
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
            'profile',

            'roles',
            'loginHistory',

            'decryptUser',
            'updatedBy',
            'createdBy',

            'createdAt',
            'updatedAt',
        ];

        return $extraFields;
    }





    public function getRoles()
    {
        if ($this->roleItem) {
            $authItems = AuthAssignment::find()->where(['user_id' => $this->id])->all();
            $result = [];
            foreach ($authItems as $authItem) {
                $result[] = $authItem['item_name'];
            }
            return $result;
        } else {
            return [];
        }
    }

    public static function attachRole($role , $user_id = null) {
        if (is_null($user_id)) {
            $user_id = current_user_id();
        }
        if (is_null($role)) {
            $role = null;
        }
        $user = User::findOne($user_id);
        $user->attach_role = $role;
        $user->save(false);
    }


    public function getOfertaIsComformed()
    {
        return $this->oferta ? 1 : 0;
    }



    public function getProfile()
    {
        return $this->hasOne(Profile::className(), ['user_id' => 'id']);
    }

    public function getLoginHistory()
    {
        return $this->hasMany(LoginHistory::className(), ['user_id' => 'id']);
    }

    // getLoginHistory
    public function getLastIn()
    {
        return $this->hasOne(LoginHistory::className(), ['user_id' => 'id'])->onCondition(['log_in_out' => LoginHistory::LOGIN])->orderBy(['id' => SORT_DESC]);
    }

    public static function createItem($model, $profile, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        $roles = $post['role'];
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (!(isset($role) && !empty($role) && is_string($role))) {
                    $errors[] = ['role' => [_e('Role is not valid.')]];
                }
            }
        } else {
            if (!(isset($roles) && !empty($roles) && is_string($roles))) {
                $errors[] = ['role' => [_e('Role is not valid.')]];
            }
        }

        if (count($errors) == 0) {
            if (isset($post['password']) && !empty($post['password'])) {
                if ($post['password'] != 'undefined' && $post['password'] != 'null' && $post['password'] != '') {
                    $password = $post['password'];
                } else {
                    $password = _passwordMK();
                }
            } else {
                $password = _passwordMK();
            }
            if (isset($post['email']) && $post['email'] == "") {
                $model->email = null;
            }
            $model->password_hash = \Yii::$app->security->generatePasswordHash($password);
            $model->auth_key = \Yii::$app->security->generateRandomString(20);
            $model->password_reset_token = null;
            $model->access_token = \Yii::$app->security->generateRandomString();
            $model->access_token_time = time();
            $model->status = User::STATUS_ACTIVE;

            if ($model->save()) {
                //**parolni shifrlab saqlaymiz */
                $model->savePassword($password, $model->id);

                $profile->user_id = $model->id;
                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {
                    // avatarni saqlaymiz
                    $model->avatar = UploadedFile::getInstancesByName('avatar');
                    if ($model->avatar) {
                        $model->avatar = $model->avatar[0];
                        $avatarUrl = $model->upload();
                        if ($avatarUrl) {
                            $profile->image = $avatarUrl;
                        } else {
                            $errors[] = _e("An error occurred while inserting the image.");
                        }
                    }
                    $profile->save(false);
                    // role ni userga assign qilish
                    $auth = Yii::$app->authManager;
                    $roles = json_decode($post['role']);
                    if ($roles != null) {
                        foreach ($roles as $arrayRole) {
                            if (count($arrayRole) > 0) {
                                foreach ($arrayRole as $role) {
                                    $authorRole = $auth->getRole($role);
                                    if ($authorRole) {
                                        $auth->assign($authorRole, $model->id);
                                    } else {
                                        $errors[] = ['role' => [_e('Role not found.')]];
                                    }
                                }
                            } else {
                                $errors[] = ['role' => [_e('Role is invalid')]];
                            }
                        }
                    }
                }
            } else {
                $errors[] = $model->errors;
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateItem($model, $profile, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        if (isset($post['role'])) {
            $roles = $post['role'];
            if (is_array($roles)) {
                foreach ($roles as $role) {
                    if (!(isset($role) && !empty($role) && is_string($role))) {
                        $errors[] = ['role' => [_e('Role is not valid.')]];
                    }
                }
            } else {
                if (!(isset($roles) && !empty($roles) && is_string($roles))) {
                    $errors[] = ['role' => [_e('Role is not valid.')]];
                }
            }
        }

        if (count($errors) == 0) {
            /* * Password */
            if (isset($post['password']) && !empty($post['password'])) {
                if ($post['password'] != 'undefined' && $post['password'] != 'null' && $post['password'] != '') {
                    if (strlen($post['password']) < 6) {
                        $errors[] = [_e('Password is too short')];
                        $transaction->rollBack();
                        return simplify_errors($errors);
                    }
                    $password = $post['password'];
                    //**  */parolni shifrlab saqlaymiz */
                    $model->savePassword($password, $model->id);
                    //**** */
                    $model->password_hash = \Yii::$app->security->generatePasswordHash($password);
                }
            }

            if ($model->save()) {
                // avatarni saqlaymiz
                $model->avatar = UploadedFile::getInstancesByName('avatar');
                if ($model->avatar) {
                    $model->avatar = $model->avatar[0];
                    $avatarUrl = $model->upload();
                    if ($avatarUrl) {
                        $profile->image = $avatarUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }

                if (!$profile->save(false)) {
                    $errors[] = $profile->errors;
                } else {
                    if (isset($post['role'])) {
                        $auth = Yii::$app->authManager;
                        $roles = json_decode($post['role']);
                        if ($roles != null) {
                            foreach ($roles as $arrayRole) {
                                if (count($arrayRole) > 0) {
                                    $auth->revokeAll($model->id);
                                    foreach ($arrayRole as $role) {
                                        $authorRole = $auth->getRole($role);
                                        if ($authorRole) {
                                            $auth->assign($authorRole, $model->id);
                                        } else {
                                            $errors[] = ['role' => [_e('Role not found.')]];
                                        }
                                    }
                                } else {
                                    $errors[] = ['role' => [_e('Role is invalid')]];
                                }
                            }
                        }
                    }
                }
            } else {
                $errors[] = $model->errors;
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function deleteItem($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $model = User::findOne(['id' => $id, 'deleted' => 0]);
        if (!$model) {
            $errors[] = [_e('Data not found.')];
        }
        if (count($errors) == 0) {
            Profile::deleteAll(['user_id' => $model->id]);
            ExamStudentQuestion::deleteAll(['user_id' => $model->id]);
            ExamSubject::deleteAll(['user_id' => $model->id]);
            ExamStudent::deleteAll(['user_id' => $model->id]);
            Student::deleteAll(['user_id' => $model->id]);
            $model->delete();
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
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
            $folder_name = substr(STORAGE_PATH, 0, -1);
            if (!file_exists(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER))) {
                mkdir(\Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER), 0777, true);
            }

            $fileName = $this->id . \Yii::$app->security->generateRandomString(10) . '.' . $this->avatar->extension;
            $miniUrl = self::UPLOADS_FOLDER . $fileName;
            $url = \Yii::getAlias('@api/web'. $folder_name  ."/". self::UPLOADS_FOLDER. $fileName);
            $this->avatar->saveAs($url, false);
            return "storage/" . $miniUrl;
        } else {
            return false;
        }
    }


    //**parolni shifrlab saqlash */
    public function savePassword($password, $user_id)
    {
        // if exist delete and create new one 
        $oldPassword = PasswordEncrypts::find()->where(['user_id' => $user_id])->all();
        if (isset($oldPassword)) {
            foreach ($oldPassword as $pass) {
                $pass->delete();
            }
        }

        $uu = new EncryptPass();
        $max = Keys::find()->count();
        $rand = rand(1, $max);
        $key = Keys::findOne($rand);
        $enc = $uu->encrypt($password, $key->name);
        $save_password = new PasswordEncrypts();
        $save_password->user_id = $user_id;
        $save_password->password = $enc;
        $save_password->key_id = $key->id;
        if ($save_password->save(false)) {
            return true;
        } else {
            return false;
        }
    }
}

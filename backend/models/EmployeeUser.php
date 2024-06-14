<?php

namespace backend\models;

use common\models\AuthAssignment;
use common\models\Employee;
use common\models\LogsAdmin;
use common\models\LogsFrontend;
use common\models\Profile;
use common\models\Student;
use common\models\User as CommonUser;
use common\models\UsersField;
use common\models\UsersSession;
use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string|null $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $deleted
 * @property int $created_at
 * @property int $updated_at
 * @property string|null $verification_token
 */
class EmployeeUser extends CommonUser
{
    const ACTIVE = 10;
    const BANNED = 5;
    const PENDING = 0;

    public $password;
    public $password_repeat;

    public static $roleList = ['employee', 'dean', 'rector', 'vice_rector'];

    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['username', 'email'], 'required'],
            ['password', 'required', 'on' => 'isNewRecord'],
            [['status', 'deleted', 'created_at', 'updated_at'], 'integer'],
            [['username', 'password', 'password_reset_token', 'email', 'verification_token'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['status', 'deleted'], 'default', 'value' => 0],
            [['template', 'layout', 'view'], 'default', 'value' => ''],
            ['password_repeat', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'message' => _e("Passwords don't match")],
            [['roleName'], 'safe'],
        ];
    }

    /**
     * Attribute labels
     *
     * @return array
     */
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
            'roleName' => _e('Role'),
            'view' => _e('View'),
            'meta' => _e('Meta'),
            'status' => _e('Status'),
            'created_at' => _e('Created on'),
            'updated_at' => _e('Updated on'),
            'verification_token' => _e('Verification token'),
        ];
    }

    /**
     * Get items
     *
     * @param string $page_type
     * @param array $args
     * @return object
     */
    public static function getItems($page_type = '', $args = array())
    {
        $search = input_get('s');
        $sort = input_get('sort');
        $department = input_get('department');

        if (empty($sort) && array_value($args, 'sort')) {
            $sort = array_value($args, 'sort');
        }

        if (empty($department) && array_value($args, 'department')) {
            $department = array_value($args, 'department');
        }

        $query = self::find()
            ->with(['profile','employee.department.infoRelation'])
            ->leftJoin('profile', 'profile.user_id = users.id')
            ->leftJoin('employee', 'employee.user_id = users.id')
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            
            ->where(['in', 'auth_assignment.item_name', self::$roleList]);
        
            if (is_numeric($department) && $department > 0) {
            $query->andWhere(['employee.department_id' => $department]);
        }

        if ($search) {
            if (is_email($search)) {
                $query->andWhere(['like', 'users.email', $search]);
            } else {
                $query->andWhere([
                    'or',
                    ['like', 'profile.firstname', $search],
                    ['like', 'profile.middlename', $search],
                    ['like', 'profile.lastname', $search],
                    ['like', 'users.username', $search],
                    ['like', 'users.email', $search],
                ]);
            }
        }

        if ($sort == 'a-z') {
            $sort_query = ['profile.firstname' => SORT_ASC];
        } elseif ($sort == 'z-a') {
            $sort_query = ['profile.firstname' => SORT_DESC];
        } elseif ($sort == 'oldest') {
            $sort_query = ['users.created_at' => SORT_ASC];
        } else {
            $sort_query = ['users.created_at' => SORT_DESC];
        }

        $query->orderBy($sort_query);

        return $query;
    }

    /**
     * Page types
     *
     * @param boolean $active
     * @return void
     */
    public static function getPageTypes($active = '')
    {
        $page_types = array(
            '' => array(
                'name' => _e('All'),
                'active' => false,
                'count' => self::itemsCount(),
            ),
            'active' => array(
                'name' => _e('Active'),
                'active' => false,
                'count' => self::itemsCount(['users.deleted' => 0, 'users.status' => User::ACTIVE]),
            ),
            'pending' => array(
                'name' => _e('Pending'),
                'active' => false,
                'count' => self::itemsCount(['users.deleted' => 0, 'users.status' => User::PENDING]),
            ),
            'blocked' => array(
                'name' => _e('Blocked'),
                'active' => false,
                'count' => self::itemsCount(['users.deleted' => 0, 'users.status' => User::BANNED]),
            ),
            'deleted' => array(
                'name' => _e('Deleted'),
                'active' => false,
                'count' => self::itemsCount(['users.deleted' => 1]),
            ),
        );

        if (isset($page_types[$active])) {
            $page_types[$active]['active'] = true;
        }

        return $page_types;
    }

    /**
     * Count all
     *
     * @param array $where
     * @return int
     */
    public static function itemsCount($where = array(), $where_in = array())
    {
        $query = User::find()
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where(['in', 'auth_assignment.item_name', self::$roleList]);

        if (is_array($where) && $where) {
            $query->andWhere($where);
        }

        if (is_array($where_in) && $where_in) {
            $query->andWhere($where_in);
        }

        return $query->count();
    }

    /**
     * Status array
     *
     * @param int $key
     * @return array
     */
    public function statusArray($key = null)
    {
        $array = [
            self::ACTIVE => 'Active',
            self::PENDING => 'Pending',
            self::BANNED => 'Blocked',
        ];

        if (isset($array[$key])) {
            return $array[$key];
        }

        return $array;
    }

    /**
     * Ajax actions
     *
     * @param [type] $action
     * @param [type] $id
     * @param [type] $items
     * @return array
     */
    public static function ajaxAction($action, $id, $items)
    {
        $message = false;
        $output['error'] = true;
        $output['success'] = false;

        if (is_numeric($id) && $id > 0) {
            $model = self::findOne(['id' => $id]);

            if ($model) {
                $set_log = false;

                switch ($action) {
                    case 'activate':
                        $set_log = true;
                        $model->status = self::ACTIVE;
                        $model->update(false);
                        $message = _e('User activated successfully.');
                        break;
                    case 'block':
                        $set_log = true;
                        $model->status = self::BANNED;
                        $model->update(false);
                        $message = _e('User blocked successfully.');
                        break;
                    case 'trash':
                        $set_log = true;
                        $model->deleted = 1;
                        $model->update(false);
                        $message = _e('User moved to the trash successfully.');
                        break;
                    case 'restore':
                        $set_log = true;
                        $model->deleted = 0;
                        $model->update(false);
                        $message = _e('User restored successfully.');
                        break;
                    case 'delete':
                        self::deleteUser($model);
                        $message = _e('User deleted successfully.');
                        break;
                }

                // Set log
                if ($set_log) {
                    set_log('admin', ['res_id' => $model->id, 'type' => 'user', 'action' => $action]);
                }
            }
        } elseif (is_array($items) && $items) {
            foreach ($items as $item) {
                $model = self::findOne(['id' => $item]);

                if ($model) {
                    $run = false;

                    switch ($action) {
                        case 'activate':
                            $run = true;
                            $model->status = self::ACTIVE;
                            $model->update(false);
                            $message = _e('Selected users have been successfully activated.');
                            break;
                        case 'block':
                            $run = true;
                            $model->status = self::BANNED;
                            $model->update(false);
                            $message = _e('Selected users have been successfully blocked.');
                            break;
                        case 'trash':
                            $run = true;
                            $model->deleted = 1;
                            $model->update(false);
                            $message = _e('Selected users have been successfully moved to the trash.');
                            break;
                        case 'restore':
                            $run = true;
                            $model->deleted = 0;
                            $model->update(false);
                            $message = _e('Selected users have been successfully restored.');
                            break;
                        case 'delete':
                            self::deleteUser($model);
                            $message = _e('Selected users have been successfully deleted.');
                            break;
                    }

                    if ($run) {
                        // Set log
                        set_log('admin', ['res_id' => $model->id, 'type' => 'user', 'action' => $action]);
                    }
                }
            }
        }

        if ($message) {
            $output['error'] = false;
            $output['success'] = true;
            $output['message'] = $message;
        }

        return $output;
    }

    /**
     * Create user
     *
     * @param [type] $user
     * @param [type] $profile
     * @return int
     */
    public function createUser($user, $profile, $employee)
    {
        $now = time();
        $log_data = array();

        $user->password_hash = Yii::$app->security->generatePasswordHash($user->password);
        $user->auth_key = md5(_random_string('alnum', 40) . $now);
        $user->created_at = $now;
        $user->updated_at = $now;
        $user->deleted = 0;

        if ($user->save()) {
            $log_data['user']['attrs'] = $user->getAttributes();
            $log_data['user']['old_attrs'] = array();

            // profile
            $profile->user_id = $user->id;

            if ($profile->save()) {
                $log_data['profile']['attrs'] = $profile->getAttributes();
                $log_data['profile']['old_attrs'] = array();
            }else{
                dd($profile->errors);
            }

            // employee
            $employee->user_id = $user->id;

            $employee->languages = is_array($employee->languages) ? implode(',', $employee->languages) : null;
            $employee->rate = str_replace('\'','',$employee->rate);

            if ($employee->save()) {
                $log_data['employee']['attrs'] = $employee->getAttributes();
                $log_data['employee']['old_attrs'] = array();
            }else{
                dd($employee->errors);
            }

            // Role
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole($user->roleName);
            $auth->assign($authorRole, $user->id);

            // Set log
            set_log('admin', [
                'res_id' => $user->id,
                'type' => 'user',
                'action' => 'create',
                'data' => json_encode($log_data),
            ]);


        }else{
            dd($user->errors);
        }

        return $user->id;
    }

    /**
     * Update user
     *
     * @param [type] $user
     * @param [type] $profile
     * @param [type] $post_item
     * @return int
     */
    public function updateUser($user, $profile, $employee, $post_item)
    {
        $now = time();
        $log_data = array();

        if ($user->password) {
            $user->password_hash = Yii::$app->security->generatePasswordHash($user->password);
        }

        $user->updated_at = $now;
        $user_oldAttributes = $user->getOldAttributes();
        $profile_oldAttributes = $profile->getOldAttributes();
        $employee_oldAttributes = $employee->getOldAttributes();

        $employee->languages = is_array($employee->languages) ? implode(',', $employee->languages) : null;
        $employee->rate = str_replace('\'','',$employee->rate);

        if ($user->save(false) && $profile->save() && $employee->save()) {
            $log_data['user']['attrs'] = $user->getAttributes();
            $log_data['user']['old_attrs'] = $user_oldAttributes;

            $log_data['profile']['attrs'] = $profile->getAttributes();
            $log_data['profile']['old_attrs'] = $profile_oldAttributes;

            $log_data['employee']['attrs'] = $employee->getAttributes();
            $log_data['employee']['old_attrs'] = $employee_oldAttributes;

            $authAssignment = AuthAssignment::find()->where(['user_id' => $user->id])->one();

            if ($authAssignment) {
                $authAssignment->load($post_item);
                $authAssignment->item_name = $user->roleName;
                $authAssignment->save(false);
            } else {
                $authAssignment = new AuthAssignment();
                $authAssignment->item_name = $user->roleName;
                $authAssignment->user_id = $user->id;
                $authAssignment->created_at = strtotime('now');
                $authAssignment->save(false);
            }

            // Set log
            set_log('admin', [
                'res_id' => $user->id,
                'type' => 'user',
                'action' => 'update',
                'data' => json_encode($log_data),
            ]);
        }

        return $user->id;
    }

    /**
     * Delete user
     *
     * @param [type] $model
     * @return void
     */
    public static function deleteUser($model)
    {
        if ($model) {
            $id = $model->id;
            $trash_item['user'] = $model->getAttributes();

            if ($model->delete(false)) {
                $profile = Profile::findOne(['user_id' => $id]);
                $employee = Employee::findOne(['user_id' => $id]);
                $student = Student::findOne(['user_id' => $id]);
                $sessions = UsersSession::findOne(['user_id' => $id]);
                $fields = UsersField::find()->where(['user_id' => $id])->all();

                if ($profile) {
                    $trash_item['profile'][] = $profile->getAttributes();
                    $profile->delete();
                }

                if ($employee) {
                    $trash_item['employee'][] = $employee->getAttributes();
                    $employee->delete();
                }

                if ($student) {
                    $trash_item['student'][] = $student->getAttributes();
                    $student->delete();
                }

                if ($sessions) {
                    $trash_item['sessions'][] = $sessions->getAttributes();
                    $sessions->delete();
                }

                if ($fields) {
                    foreach ($fields as $field_item) {
                        $trash_item['fields'][] = $field_item->getAttributes();
                        $field_item->delete();
                    }
                }

                AuthAssignment::deleteAll(['user_id' => $id]);
                LogsAdmin::deleteAll(['user_id' => $id]);
                LogsFrontend::deleteAll(['user_id' => $id]);

                // Set trash
                set_trash(array(
                    'res_id' => $id,
                    'type' => 'user',
                    'data' => json_encode($trash_item),
                ));

                // Set log
                set_log('admin', [
                    'res_id' => $id,
                    'type' => 'user',
                    'action' => 'delete',
                    'data' => json_encode($trash_item),
                ]);
            }
        }
    }

}

<?php

namespace api\resources;

use Yii;
use api\resources\Profile;
use common\models\Employee;
use common\models\UserSubject;
use yii\web\UploadedFile;

class EmployeeUser extends ParentUser
{
    public static $roleList = ['employee', 'dean', 'rector', 'vice_rector'];

    public static function createItem($model, $profile, $employee, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // Validatin input data

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // role to'gri jo'natilganligini tekshirish
        if (!(isset($post['role']) && !empty($post['role']) && is_string($post['role']))) {
            $errors[] = ['role' => [_e('Role is not valid.')]];
        }

        if (isset($post['role'])) {
            // Role mavjudligini tekshirish
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole($post['role']);
            if (!$authorRole) {
                $errors[] = ['role' => [_e('Role not found.')]];
            }

            // rolening employee toifasidagi rollar tarkibidaligini tekshirish
            if (!in_array($post['role'], self::$roleList)) {
                $errors[] = ['role' => [_e('Role does not fit the type of staff.')]];
            }
        }

        // **********

        if (count($errors) == 0) {
            if (isset($post['password']) && !empty($post['password'])) {
                $model->password_hash = \Yii::$app->security->generatePasswordHash($post['password']);
            }
            $model->auth_key = \Yii::$app->security->generateRandomString(20);
            $model->password_reset_token = null;
            $model->access_token = \Yii::$app->security->generateRandomString();
            $model->access_token_time = time();
            if ($model->save()) {
                $profile->user_id = $model->id;

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
                // ***

                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {

                    $employee->user_id = $model->id;
                    if (!$employee->save()) {
                        $errors[] = $employee->errors;
                    } else {
                        // role ni userga assign qilish
                        $auth->assign($authorRole, $model->id);
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

    public static function updateItem($model, $profile, $employee, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        if (isset($post['role'])) {

            // role to'gri jo'natilganligini tekshirish
            if (empty($post['role']) || !is_string($post['role'])) {
                $errors[] = ['role' => [_e('Role is not valid.')]];
            }

            // Role mavjudligini tekshirish
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole($post['role']);
            if (!$authorRole) {
                $errors[] = ['role' => [_e('Role not found.')]];
            }

            // rolening employee toifasidagi rollar tarkibidaligini tekshirish
            if (!in_array($post['role'], self::$roleList)) {
                $errors[] = ['role' => [_e('Role does not fit the type of staff.')]];
            }
        }



        if (count($errors) == 0) {
            if (isset($post['password']) && !empty($post['password'])) {
                $model->password_hash = \Yii::$app->security->generatePasswordHash($post['password']);
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
                // ***

                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {
                    if ($employee->save()) {
                        if (isset($post['role'])) {
                            // user ning eski rolini o'chirish
                            $auth->revokeAll($model->id);
                            // role ni userga assign qilish
                            $auth->assign($authorRole, $model->id);
                        }
                    } else {
                        $errors[] = $employee->errors;
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

        $model = self::findEmployee($id);
        if (!$model || !$model->employee || !$model->profile) {
            $errors[] = [_e('Employee not found.')];
        }

        if (count($errors) == 0) {

            // remove profile image
            // $filePath = assets_url($model->profile->image);
            // if(file_exists($filePath)){
            //     unlink($filePath);
            // }

            // remove employee
            $employeeDeleted = Employee::deleteAll(['user_id' => $id]);
            if (!$employeeDeleted) {
                $errors[] = [_e('Error in employee deleting process.')];
            }

            // remove profile
            $profileDeleted = Profile::deleteAll(['user_id' => $id]);
            if (!$profileDeleted) {
                $errors[] = [_e('Error in profile deleting process.')];
            }

            // remove model
            $userDeleted = User::findOne($id)->delete();
            if (!$userDeleted) {
                $errors[] = [_e('Error in user deleting process.')];
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

    public static function bindSubject($model, $body)
    {

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // Validate data
        // check user_id
        $user = User::findOne($model->user_id);
        if (!$user) {
            $errors[] = [_e('User not found.')];
        }

        $bodyObj = json_decode($body);
        if (!$bodyObj) {
            $errors[] = [_e('Request body is not in valid JSON format.')];
        } else {
            foreach ($bodyObj as $obj) {
                // check subject_id
                $subject = Subject::findOne($obj->subject_id);
                if (!$subject) {
                    $errors[] = [_e('Subject with ID {subject_id} not found.', ['subject_id' => $obj->subject_id])];
                }

                // check language_ids
                $langs = Reference::find()->where(['type' => 'language', 'id' => $obj->language_ids])->all();
                if (!$langs || count($langs) != count($obj->language_ids)) {
                    $errors[] = [_e('Languages with ID {language_ids} not found.', ['language_ids' => implode(',', $obj->language_ids)])];
                }
            }
            //

            // delete old bindings
            UserSubject::deleteAll(['user_id' => $model->user_id]);

            foreach ($bodyObj as $obj) {
                foreach ($obj->language_ids as $lang) {
                    $userSubject = new UserSubject();
                    $userSubject->user_id = $model->user_id;
                    $userSubject->subject_id = $obj->subject_id;
                    $userSubject->language_id = $lang;
                    if (!$userSubject->save()) {
                        $errors[] = $userSubject->getErrorSummary(true);
                    }
                }
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

    public static function getSubjects($employee_id)
    {

        // check user_id
        $errors = [];
        $data = [];
        $user = User::findOne($employee_id);
        if (!$user) {
            $errors[] = [_e('User not found.')];
        }

        if (count($errors) == 0) {
            $userSubjects = UserSubject::find()->where(['user_id' => $employee_id])->all();

            $subjects = [];
            foreach ($userSubjects as $one) {
                $subjects[] = $one->subject_id;
            }
            $subjects = array_unique($subjects);

            foreach ($subjects as $subj) {
                $langs = [];
                foreach ($userSubjects as $one) {
                    if ($subj == $one->subject_id) {
                        $langs[] = $one->language_id;
                    }
                }
                $data[] = [
                    'subject_id' => $subj,
                    'language_ids' => $langs
                ];
            }
        }

        $data = [
            'user_id' => $user->id,
            'subjects' => $data
        ];

        if (count($errors) > 0) {
            return ['is_ok' => false, 'errors' => $errors];
        } else {
            return ['is_ok' => true, 'data' => $data];
        }
    }

    public static function findEmployee($id)
    {
        return self::find()
            ->with(['profile', 'employee'])
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where(['and', ['id' => $id], ['in', 'auth_assignment.item_name', self::$roleList]])
            ->one();
    }
}

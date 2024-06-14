<?php

namespace api\resources;

use common\models\model\EduSemestr;
use common\models\model\Group;
use common\models\model\StudentGroup;
use common\models\model\TimeTable;
use common\models\model\TimeTableGroup;
use Yii;
use api\resources\Profile;
use common\models\User;
use common\models\Student;
use yii\web\UploadedFile;

class StudentUser extends ParentUser
{
    public static $roleList = ['student', 'master'];

    const TYPE_AUTUMN = 1;
    const TYPE_WINTER = 2;











    public static function studentGroupUpdate($student, $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $studentGroup = StudentGroup::findOne([
            'student_id' => $student->id,
            'status' => 1,
            'is_deleted' => 0
        ]);

        if (isset($post['type'])) {
            if ($post['type'] == 1) {
                $user = User::findOne(['user_id' => $student->user_id]);
                $user->status = 0;
                $user->status_n = 0;
                $user->deleted = 0;
                $user->save(false);
                $student->status = 0;
                $student->save(false);
                $studentGroup->end_date = $post['end_date'];
                $studentGroup->status = 0;
                $studentGroup->save(false);
            } elseif ($post['type'] == 2) {
                if (isset($post['group_id'])) {
                    if ($student->group_id == $post['group_id']) {
                        $errors[] = ['group_id' => _e("The new group must be different from the student's current group!")];
                    }
                } else {
                    $errors[] = ['group_id' => _e("Group Id required")];
                }

                if (count($errors) > 0) {
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }

                $student->group_id = $post['group_id'];
                $student->faculty_id = $student->group->faculty_id;
                $student->direction_id = $student->group->direction_id;
                $student->edu_plan_id = $student->group->edu_plan_id;

                $studentGroup->status = 0;
                $studentGroup->end_date = $post['end_date'];
                $studentGroup->save(false);

                $newStudentGroup = new StudentGroup();
                $newStudentGroup->group_id = $student->group_id;
                $newStudentGroup->student_id = $student->student_id;
                $newStudentGroup->start_date = $student->group->activeEduSemestr->start_date;
                $endEduSemestr = EduSemestr::find()
                    ->where([
                        'edu_plan_id'
                    ])->orderBy([
                        'course_id' => SORT_DESC,
                        'semestr_id' => SORT_DESC,
                    ])->one();
                $newStudentGroup->end_date = $endEduSemestr->end_date;
                if (!$newStudentGroup->validate()) {
                    $errors[] = $newStudentGroup->errors;
                    $transaction->rollBack();
                    return simplify_errors($errors);
                }
                $newStudentGroup->save(false);
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

    public static function createItemImport($model, $profile, $student, $post)
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

            // rolening student toifasidagi rollar tarkibidaligini tekshirish
            if (!in_array($post['role'], self::$roleList)) {
                $errors[] = ['role' => [_e('Role does not fit the type of staff.')]];
            }
        }
        // **********

        if (count($errors) == 0) {
            if (isset($post['password']) && !empty($post['password'])) {
                $password = $post['password'];
            } else {
                $password = _passwordMK();
            }

            $model->password_hash = \Yii::$app->security->generatePasswordHash($password);

            $model->auth_key = \Yii::$app->security->generateRandomString(20);
            $model->password_reset_token = null;
            $model->access_token = \Yii::$app->security->generateRandomString();
            $model->access_token_time = time();
            // $model->save();

            if ($model->save()) {
                //**parolni shifrlab saqlaymiz */
                $model->savePassword($password, $model->id);
                //**** */
                $profile->user_id = $model->id;
                if (!$profile->save()) {
                    $errors[] = $profile->errors;
                } else {
                    $student->user_id = $model->id;
                    if (!$student->save()) {
                        $errors[] = $student->errors;
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


    public static function studentType($post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // A va B guruhga o'zgartirish
        if (isset($post['two_group']) && isset($post['type']) && ($post['type'] == 1 || $post['type'] == 2)) {

            $post['two_group'] = str_replace("'", "", $post['two_group']);
            $two_group = json_decode(str_replace("'", "", $post['two_group']));

            if (isset($two_group)) {
                foreach ($two_group as $groupIdTwo => $studentstwo) {
                    $groupTwo = Group::findOne([
                        'id' => $groupIdTwo,
                    ]);
                    if (!isset($groupTwo)) {
                        $errors[] = ['group' => [_e('Group id not found.')]];
                    } else {
                        foreach ($studentstwo as $studentTwo) {
                            $student = Student::findOne([
                                'id' => $studentTwo,
                                'is_deleted' => 0
                            ]);
                            if (isset($student)) {
                                if ($groupTwo->id != $student->group_id) {
                                    $errors[] = ['id:'.$student->group_id => [_e('The student does not study in this group.')]];
                                } else {
                                    $student->type = $post['type'];
                                    if (!$student->save()) {
                                        $errors[] = ['id:'.$student->id => [_e('Error saving data.')]];
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                $errors[] = ['two_group' => [_e('The data was sent incorrectly.')]];
            }
        } else {
            $errors[] = ['data' => [_e('Please send data.')]];
        }
        // A va B guruhga o'zgartirish

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

    }

    public static function studentTutor($post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (isset($post['user_id'])) {
            $userId = $post['user_id'];

            $post['groups'] = str_replace("'", "", $post['groups']);
            $groups = json_decode(str_replace("'", "", $post['groups']));


            foreach ($groups as $key => $group) {
                $user = User::findOne($userId);
                if ($user != null) {
                    Student::updateAll(['tutor_id' => null] , ['is_deleted' => 0 , 'tutor_id' => $user->id]);
                    foreach ($group as $groupId) {
                        $oneGroup = Group::findOne([
                            'id' => $groupId,
                            'status' => 1,
                            'is_deleted' => 0
                        ]);
                        if ($oneGroup != null) {
                            $students = $oneGroup->student;
                            if (count($students) > 0) {
                                foreach ($students as $student) {
                                    $student->tutor_id = $user->id;
                                    $student->save(false);
                                }
                            }
                        } else {
                            $errors[] = _e("Group not found.");
                        }
                    }
                } else {
                    $errors[] = _e("User not found.");
                }
            }

        } else {
            $errors[] = ['user_id' => _e('User input required.')];
        }

        if (count($errors) == 0) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }

    }

    public static function updateItemImport($model, $profile, $student, $post)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        if (!$post) {
            $errors[] = ['all' => [_e('Please send data.')]];
        }

        // if (!($model->validate())) {
        //     $errors[] = $model->errors;
        // }
        // if (!($profile->validate())) {
        //     $errors[] = $profile->errors;
        // }
        // if (!($student->validate())) {
        //     $errors[] = $student->errors;
        // }


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

            // rolening student toifasidagi rollar tarkibidaligini tekshirish
            if (!in_array($post['role'], self::$roleList)) {
                $errors[] = ['role' => [_e('Role does not fit the type of staff.')]];
            }
        }

        if (count($errors) == 0) {

            if (isset($post['password']) && !empty($post['password'])) {
                $password = $post['password'];
                $model->password_hash = \Yii::$app->security->generatePasswordHash($password);
                //**parolni shifrlab saqlaymiz */
                $model->savePassword($password, $model->id);
                //**** */
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

                // Passport file ni saqlaymiz
                $model->passport_file = UploadedFile::getInstancesByName('passport_file');
                if ($model->passport_file) {
                    $model->passport_file = $model->passport_file[0];
                    $passportUrl = $model->uploadPassport();
                    if ($passportUrl) {
                        $profile->passport_file = $passportUrl;
                    } else {
                        $errors[] = $model->errors;
                    }
                }
                // ***

                if (!$profile->save(false)) {
                    $errors[] = $profile->errors;
                } else {
                    if ($student->save()) {
                        if (isset($post['role'])) {
                            // user ning eski rolini o'chirish
                            $auth->revokeAll($model->id);
                            // role ni userga assign qilish
                            $auth->assign($authorRole, $model->id);
                        }
                    } else {
                        $errors[] = $student->errors;
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

        $model = Student::findOne($id);

        if (!isset($model)) {
            $errors[] = [_e('Student not found.')];
        } else {
            $userId = $model->user_id;
        }

        if (count($errors) == 0) {

            // remove student
            $studentDeleted = Student::findOne(['id' => $id]);
            if (!$studentDeleted) {
                $errors[] = [_e('Error in student deleting process.')];
            } elseif ($studentDeleted->is_deleted == 1) {
                $errors[] = [_e('Student not found')];
            } else {
                $studentDeleted->is_deleted = 1;
                $studentDeleted->save(false);
            }

            // remove profile
            $profileDeleted = Profile::findOne(['user_id' => $userId]);
            if (!$profileDeleted) {
                $errors[] = [_e('Error in profile deleting process.')];
            } else {
                $profileDeleted->is_deleted = 1;
                $profileDeleted->save(false);
            }

            // remove model
            $userDeleted = User::findOne($userId);
            if (!$userDeleted) {
                $errors[] = [_e('Error in user deleting process.')];
            } else {
                $userDeleted->status = User::STATUS_BANNED;
                $userDeleted->save(false);
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

    public static function findStudent($id)
    {
        return self::find()
            ->with(['profile', 'user'])
            ->leftJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where(['and', ['id' => $id], ['in', 'auth_assignment.item_name', self::$roleList]])
            ->one();
    }
}

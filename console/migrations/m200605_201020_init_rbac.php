<?php

use yii\db\Migration;

/**
 * Class m200605_201020_init_rbac
 */
class m200605_201020_init_rbac extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $auth = Yii::$app->authManager;

        // add "admin" role and give this role the "backendView" permission
        $admin = $auth->createRole('admin');
        $admin->description = 'Administrator';
        $auth->add($admin);

        // add "edu_admin" role
        $edu_admin = $auth->createRole('edu_admin');
        $edu_admin->description = 'edu_admin';
        $auth->add($edu_admin);

        // add "rector" role
        $rector = $auth->createRole('rector');
        $rector->description = 'Rector';
        $auth->add($rector);


        // add "dean" role
        $dean = $auth->createRole('dean');
        $dean->description = 'Dean of the faculty';
        $auth->add($dean);

        // add "hr" role
        $hr = $auth->createRole('hr');
        $hr->description = 'hr';
        $auth->add($hr);

        // add "teacher" role
        $teacher = $auth->createRole('teacher');
        $teacher->description = 'teacher';
        $auth->add($teacher);

        // add "student" role
        $student = $auth->createRole('student');
        $student->description = 'Student';
        $auth->add($student);

        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        // usually implemented in your User model.
        $auth->assign($admin, 1);
        $auth->assign($admin, 2);
        $auth->assign($admin, 3);
        $auth->assign($admin, 4);
    }

    public function down()
    {
        $auth = Yii::$app->authManager;
        $auth->removeAll();
    }
}

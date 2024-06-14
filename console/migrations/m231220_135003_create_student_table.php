<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%student}}`.
 */
class m231220_135003_create_student_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $tableName = Yii::$app->db->tablePrefix . 'student';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('student');
        }

        $this->createTable('{{%student}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'first_name' => $this->string(255)->null(),
            'last_name' => $this->string(255)->null(),
            'middle_name' => $this->string(255)->null(),
            'image' => $this->string(255)->null(),
            'passport_number' => $this->string(255)->null(),
            'passport_serial' => $this->string(255)->null(),
            'passport_pin' => $this->string(255)->null(),
            'passport_issued_date' => $this->string(255)->null(),
            'passport_given_date' => $this->string(255)->null(),
            'passport_given_by' => $this->string(255)->null(),

            'birthday' => $this->string(255)->null(),
            'gender' => $this->tinyInteger(1)->null(),
            'phone' => $this->string(50)->null(),

            'edu_type' => $this->integer()->null(),
            'edu_name' => $this->string(255)->null(),
            'diploma_type' => $this->integer()->defaultValue(0),
            'diploma_file' => $this->string(255)->null(),

            'certificate_type' => $this->integer()->defaultValue(0),
            'certificate_level' => $this->integer()->null(),
            'certificate_level_type' => $this->integer()->null(),
            'certificate_file' => $this->string(255)->null(),

            'general_edu_type' => $this->integer()->defaultValue(1),
            'edu_form_id' => $this->integer()->null(),
            'direction_id' => $this->integer()->null(),

            'exam_type' => $this->integer()->defaultValue(0),
            'dtm_file' => $this->string(255)->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_student_table_users_table', 'student', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%student}}');
    }
}

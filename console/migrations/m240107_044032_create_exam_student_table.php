<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student}}`.
 */
class m240107_044032_create_exam_student_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'exam_student';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_student');
        }

        $this->createTable('{{%exam_student}}', [
            'id' => $this->primaryKey(),
            'exam_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'edu_year_id' => $this->integer()->notNull(),
            'direction_id' => $this->integer()->notNull(),
            'start_time' => $this->integer()->notNull(),
            'finish_time' => $this->integer()->notNull(),

            'ball' => $this->float()->defaultValue(0),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_exam_student_table_exam_table', 'exam_student', 'exam_id', 'exam', 'id');
        $this->addForeignKey('mk_exam_student_table_user_table', 'exam_student', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_exam_student_table_student_table', 'exam_student', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_exam_student_table_edu_year_table', 'exam_student', 'edu_year_id', 'edu_year', 'id');
        $this->addForeignKey('mk_exam_student_table_direction_table', 'exam_student', 'direction_id', 'direction', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_student}}');
    }
}

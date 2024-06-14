<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%exam_student_question}}`.
 */
class m240108_054757_create_exam_student_question_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'exam_student_question';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('exam_student_question');
        }

        $this->createTable('{{%exam_student_question}}', [
            'id' => $this->primaryKey(),
            'exam_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'student_id' => $this->integer()->notNull(),
            'exam_student_id' => $this->integer()->notNull(),
            'exam_subject_id' => $this->integer()->notNull(),

            'question_id' => $this->integer()->notNull(),
            'options' => $this->json()->notNull(),
            'student_option' => $this->integer()->null(),

            'is_correct' => $this->integer()->defaultValue(0),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_exam_student_question_table_exam_table', 'exam_student_question', 'exam_id', 'exam', 'id');
        $this->addForeignKey('mk_exam_student_question_table_user_table', 'exam_student_question', 'user_id', 'users', 'id');
        $this->addForeignKey('mk_exam_student_question_table_student_table', 'exam_student_question', 'student_id', 'student', 'id');
        $this->addForeignKey('mk_exam_student_question_table_exam_student_table', 'exam_student_question', 'exam_student_id', 'exam_student', 'id');
        $this->addForeignKey('mk_exam_student_question_table_exam_subject_table', 'exam_student_question', 'exam_subject_id', 'exam_subject', 'id');
        $this->addForeignKey('mk_exam_student_question_table_question_table', 'exam_student_question', 'question_id', 'question', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%exam_student_question}}');
    }
}

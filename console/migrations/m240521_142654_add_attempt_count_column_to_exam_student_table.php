<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%exam_student}}`.
 */
class m240521_142654_add_attempt_count_column_to_exam_student_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('exam_student' , 'attempt_count' , $this->tinyInteger(1)->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}

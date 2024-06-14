<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%exam_student}}`.
 */
class m240322_082211_add_download_contract_column_to_exam_student_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('exam_student' , 'download_contract', $this->integer()->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}

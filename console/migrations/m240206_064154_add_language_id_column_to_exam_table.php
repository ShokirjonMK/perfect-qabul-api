<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%exam}}`.
 */
class m240206_064154_add_language_id_column_to_exam_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('exam' , 'language_id' , $this->integer()->null());
        $this->addForeignKey('mk_exam_table_language_table', 'exam', 'language_id', 'languages', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}

<?php

use yii\db\Migration;

/**
 * Class m240521_135705_add_exam_form_table
 */
class m240521_135705_add_exam_form_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('student' , 'exam_form' , $this->tinyInteger(1)->defaultValue(0));
        $this->addColumn('student' , 'attempt_count' , $this->tinyInteger(1)->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240521_135705_add_exam_form_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240521_135705_add_exam_form_table cannot be reverted.\n";

        return false;
    }
    */
}

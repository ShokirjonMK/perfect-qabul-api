<?php

use yii\db\Migration;

/**
 * Class m240521_135510_add_exam_form_table
 */
class m240521_135510_add_exam_form_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240521_135510_add_exam_form_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240521_135510_add_exam_form_table cannot be reverted.\n";

        return false;
    }
    */
}

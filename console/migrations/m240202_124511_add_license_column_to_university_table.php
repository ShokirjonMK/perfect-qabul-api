<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%university}}`.
 */
class m240202_124511_add_license_column_to_university_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('university' , 'license_url' , $this->text()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}

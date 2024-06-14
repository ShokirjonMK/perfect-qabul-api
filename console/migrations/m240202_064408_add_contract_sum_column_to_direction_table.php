<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%direction}}`.
 */
class m240202_064408_add_contract_sum_column_to_direction_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('direction' , 'contract_price' , $this->integer()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}

<?php

use yii\db\Migration;

/**
 * Class m211023_091910_translate
 */
class m211023_091910_translate extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'translate';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('translate');
        }

        $this->createTable('translate', [
        'id' => $this->primaryKey(),
        'model_id'=>$this->integer()->Null(),
        'name'=>$this->string(255)->notNull(),
        'description'=>$this->text()->Null(),
        'table_name'=>$this->string(255)->notNull(),
        'language'=>$this->string(2)->notNull(),


        'order'=>$this->tinyInteger(1)->defaultValue(1),
        'status' => $this->tinyInteger(1)->defaultValue(1),
        'created_at'=>$this->integer()->Null(),
        'updated_at'=>$this->integer()->Null(),
        'created_by' => $this->integer()->notNull()->defaultValue(0),
        'updated_by' => $this->integer()->notNull()->defaultValue(0),
        'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
    ], $tableOptions);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('translate');
    }

}

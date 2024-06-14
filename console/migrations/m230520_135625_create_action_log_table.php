<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%action_log}}`.
 */
class m230520_135625_create_action_log_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'action_log';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('action_log');
        }

        $this->createTable('{{%action_log}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->integer()->null(),
            'controller' => $this->string(255)->null(),
            'action' => $this->string(255)->null(),
            'method' => $this->string(255)->null(),
            'user_id' => $this->integer()->null(),
            'result' =>  $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->null(),
            'errors' => $this->text()->null(),
            'data' =>  $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->null(),
            'post_data' =>  $this->getDb()->getSchema()->createColumnSchemaBuilder('longtext')->null(),
            'get_data' => $this->text()->null(),
            'message' => $this->string(255)->null(),
            'browser' => $this->text()->null(),
            'ip_address' => $this->string(33)->null(),
            'host' => $this->text()->null(),
            'ip_address_data' => $this->text()->null(),
            'log_date' => $this->string(255)->null(),

            'status' => $this->tinyInteger(1)->defaultValue(1),

        ], $tableOptions);

        //        $this->addForeignKey('mk_action_log_table_users_table', 'action_log', 'user_id', 'users', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        //        $this->dropForeignKey('mk_action_log_table_users_table', 'action_log');
        $this->dropTable('{{%action_log}}');
    }
}

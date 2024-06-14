<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%login_history}}`.
 */
class m230521_070851_create_login_history_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'login_history';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('login_history');
        }

        $this->createTable('{{%login_history}}', [
            'id' => $this->primaryKey(),
            'created_on' => $this->dateTime()->null(),
            'ip' => $this->string(255)->null(),
            'user_id' => $this->integer()->null(),
            'log_in_out' => $this->tinyInteger(1)->null(),
            'ip_data' => $this->json()->null(),
            'device' => $this->string(255)->null(),
            'device_id' => $this->string(255)->null(),
            'type' => $this->string(255)->null(),
            'model_device' => $this->string(255)->null(),
            'data' => $this->text()->null(),
            'host' => $this->text()->null(),
            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->null(),
            'updated_at'=>$this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);

//        $this->addForeignKey('mk_login_history_table_users_table', 'login_history', 'user_id', 'users', 'id');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
//        $this->dropForeignKey('mk_login_history_table_users_table', 'login_history');
        $this->dropTable('{{%login_history}}');
    }
}

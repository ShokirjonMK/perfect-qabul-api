<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%reset_password}}`.
 */
class m240525_101300_create_reset_password_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'reset_password';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('reset_password');
        }


        $this->createTable('{{%reset_password}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unique(),
            'phone' => $this->string(255)->unique(),
            'sms_number' => $this->integer()->null(),
            'sms_time' => $this->integer()->null(),

            'sms_token' => $this->string()->null(),
            'sms_token_time' => $this->integer()->null(),

            'reset_token' => $this->string()->null(),
            'reset_token_time' => $this->integer()->null(),

            'limit_time' => $this->integer()->defaultValue(0),
            'limit_count' => $this->integer()->defaultValue(0),

            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);
        $this->addForeignKey('mk_reset_password_table_user_table', 'reset_password', 'user_id', 'users', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%reset_password}}');
    }
}

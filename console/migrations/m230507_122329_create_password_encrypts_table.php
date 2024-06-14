<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%password_encrypts}}`.
 */
class m230507_122329_create_password_encrypts_table extends Migration
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

        $this->createTable('{{%password_encrypts}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'password' => $this->string('255'),
            'key_id' => $this->integer()->notNull(),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null()
        ], $tableOptions);

        $this->addForeignKey('up_password_encrypts_user_id', 'password_encrypts', 'user_id', 'users', 'id');
        $this->addForeignKey('up_password_encrypts_key_id', 'password_encrypts', 'key_id', 'keys', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('up_password_encrypts_user_id', 'password_encrypts');
        $this->dropForeignKey('up_password_encrypts_key_id', 'password_encrypts');

        $this->dropTable('{{%password_encrypts}}');
    }
}

<?php

use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $tableName = Yii::$app->db->tablePrefix . 'users';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('users');
        }

        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull()->unique(),
            'attach_role' => $this->string()->null(),
            'auth_key' => $this->string(32)->notNull(),
            'password_hash' => $this->string()->notNull(),
            'password_reset_token' => $this->string()->unique(),
            'verification_token' => $this->string()->defaultValue(null),
            'access_token' => $this->string(100)->defaultValue(null),
            'access_token_time' => $this->integer()->null(),
            'email' => $this->string()->null()->unique(),

            'sms_number' => $this->integer()->null(),
            'sms_time' => $this->integer()->null(),
            'step' => $this->integer()->defaultValue(1),

            'meta' => $this->json(),
            'status' => $this->smallInteger()->null(),
            'status_n' => $this->smallInteger()->notNull()->defaultValue(10),
            'deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
            'cacheable' => $this->tinyInteger()->notNull()->defaultValue(0),
            'searchable' => $this->tinyInteger()->notNull()->defaultValue(0),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_changed' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->insert('{{%users}}', [
            'username' => 'ShokirjonMK',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("12300123"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'mkshokirjon@gmail.com',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => 'blackmoon',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("blackmoonuz"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'blackmoonuz@mail.ru',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);


        $this->insert('{{%users}}', [
            'username' => '10ikbol16',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("ikboljon2oo1"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'ikboljon@gmail.uz',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%users}}', [
            'username' => 'ahror',
            'auth_key' => \Yii::$app->security->generateRandomString(20),
            'password_hash' => \Yii::$app->security->generatePasswordHash("ahroruz"),
            'password_reset_token' => null,
            'access_token' => \Yii::$app->security->generateRandomString(),
            'access_token_time' => time(),
            'email' => 'a-user@asd.uz',
            'status' => 10,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

    }

    public function down()
    {
        $this->dropTable('{{%users}}');
    }
}

<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%profile}}`.
 */
class m230506_110930_create_profile_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'profile';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('profile');
        }

        $this->createTable('{{%profile}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'first_name' => $this->string(255)->null(),
            'last_name' => $this->string(255)->null(),
            'middle_name' => $this->string(255)->null(),
            'image' => $this->string(255)->null(),

            'address' => $this->text()->null(),
            'description' => $this->text()->null(),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->null(),
            'updated_at' => $this->integer()->null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),

        ], $tableOptions);

        $this->addForeignKey('mk_profile_table_users_table', 'profile', 'user_id', 'users', 'id');


        $this->insert('{{%profile}}', [
            'user_id' => 1,
            'first_name' => 'ShokirjonMK',
            'last_name' => 'ShokirjonMK_uz',
            'middle_name' => 'ShokirjonMKuz',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 2,
            'first_name' => 'blackmoon',
            'last_name' => 'blackmoon_uz',
            'middle_name' => 'blackmoonuz',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 3,
            'first_name' => 'Iqboljon',
            'last_name' => 'Uraimov',
            'middle_name' => 'Anvarjon o\'g\'li',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        $this->insert('{{%profile}}', [
            'user_id' => 4,
            'first_name' => 'Ahror',
            'last_name' => 'Ahror',
            'middle_name' => 'Ahror',
            'created_at' => time(),
            'updated_at' => time(),
        ]);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('mk_profile_table_users_table', 'profile');
        $this->dropForeignKey('mk_profile_table_countries_table', 'profile');
        $this->dropForeignKey('mk_profile_table_region_table', 'profile');
        $this->dropForeignKey('mk_profile_table_area_table', 'profile');
        $this->dropTable('{{%profile}}');
    }
}

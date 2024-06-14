<?php

use yii\db\Migration;

/**
 * Class m200608_124459_table_languages
 */
class m200608_124459_table_languages extends Migration
{
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            // https://stackoverflow.com/questions/51278467/mysql-collation-utf8mb4-unicode-ci-vs-utf8mb4-default-collation
            // https://www.eversql.com/mysql-utf8-vs-utf8mb4-whats-the-difference-between-utf8-and-utf8mb4/
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%languages}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(100),
            'lang_code' => $this->string(10),
            'locale' => $this->string(50),
            'rtl' => $this->smallInteger()->defaultValue(0),
            'default' => $this->smallInteger()->defaultValue(0),
            'sort' => $this->integer()->defaultValue(0),
            'status' => $this->integer()->defaultValue(0),
        ], $tableOptions);

        // creates index for column `lang_code`
        $this->createIndex(
            'idx-setting-lang_code',
            'languages',
            'lang_code'
        );

         $this->insert('{{%languages}}', [
            'name' => 'O\'zbekcha',
            'lang_code' => 'uz',
            'locale' => 'uz_UZ',
            'rtl' => 0,
            'status' => 1,
        ]);
        

        $this->insert('{{%languages}}', [
            'name' => 'English',
            'lang_code' => 'en',
            'locale' => 'en_GB',
            'rtl' => 0,
            'status' => 1,
        ]);

        $this->insert('{{%languages}}', [
            'name' => 'Русский',
            'lang_code' => 'ru',
            'locale' => 'ru_RU',
            'rtl' => 0,
            'status' => 1,
        ]);
        
        $this->insert('{{%languages}}', [
            'name' => 'Deutsch',
            'lang_code' => 'de',
            'locale' => 'de_DE',
            'rtl' => 0,
            'status' => 0,
        ]);
        
        $this->insert('{{%languages}}', [
            'name' => 'Español',
            'lang_code' => 'es',
            'locale' => 'es_ES',
            'rtl' => 0,
            'status' => 0,
        ]);

        $this->insert('{{%languages}}', [
            'name' => 'Français',
            'lang_code' => 'fr',
            'locale' => 'fr_FR',
            'rtl' => 0,
            'status' => 0,
        ]);

        $this->insert('{{%languages}}', [
            'name' => 'Italiano',
            'lang_code' => 'it',
            'locale' => 'it_IT',
            'rtl' => 0,
            'status' => 0,
        ]);

       

        $this->insert('{{%languages}}', [
            'name' => 'Türkçe',
            'lang_code' => 'tr',
            'locale' => 'tr_TR',
            'rtl' => 0,
            'status' => 0,
        ]);

       
    }

    public function down()
    {
        $this->dropTable('{{%languages}}');
    }
}

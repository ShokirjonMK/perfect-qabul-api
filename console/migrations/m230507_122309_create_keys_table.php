<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%keys}}`.
 */
class m230507_122309_create_keys_table extends Migration
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


        $this->createTable('{{%keys}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string('255'),

            'order' => $this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->Null()->defaultValue(0),
            'updated_by' => $this->integer()->Null()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ], $tableOptions);


        $this->insert(
            '{{%keys}}',
            [
                'name' => 'ShokirjonMK'
            ]
        );

        $this->insert(
            '{{%keys}}',
            [
                'name' => 'University'
            ]
        );

        $this->insert(
            '{{%keys}}',
            [
                'name' => 'NeverGiveUp'
            ]

        );

        $this->insert(
            '{{%keys}}',
            [
                'name' => 'E-UNIVERSITY'
            ]
        );
        
        $this->insert(
            '{{%keys}}',
            [
                'name' => 'ThisIsKey'
            ]
        );
        
        $this->insert(
            '{{%keys}}',
            [
                'name' => 'ENGEENE'
            ]
        );
        
        $this->insert(
            '{{%keys}}',
            [
                'name' => 'MasterKey'
            ]
        );
        
        $this->insert(
            '{{%keys}}',
            [
                'name' => 'CompyuterVISION'
            ]
        );
        
        $this->insert(
            '{{%keys}}',
            [
                'name' => 'Supervisor'
            ]
        );
        
        $this->insert(
            '{{%keys}}',
            [
                'name' => 'Administrator-U'
            ]
        );
        
        $this->insert(
            '{{%keys}}',
            [
                'name' => 'UserGuest'
            ]
        );
        
        $this->insert(
            '{{%keys}}',
            [
                'name' => 'Solutions'
            ]
        );
        
        $this->insert(
            '{{%keys}}',
            [
                'name' => 'Productor'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%keys}}');
    }
}

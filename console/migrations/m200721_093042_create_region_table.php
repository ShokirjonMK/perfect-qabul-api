<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%region}}`.
 */
class m200721_093042_create_region_table extends Migration
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

        $this->createTable('{{%region}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string('150'),
            'name_kirill' => $this->string('150'),
            'slug' => $this->string('150'),
            'country_id' => $this->integer()->defaultValue(229),
            'parent_id' => $this->integer(),
            'type' => $this->tinyInteger(1)->defaultValue(0),
            'postcode' => $this->string('150'),
            'lat' => $this->string('100'),
            'long' => $this->string('100'),
            'sort' => $this->integer()->defaultValue(0),
            'status' => $this->tinyInteger(1)->defaultValue(0),
            'created_on' => $this->timestamp()->defaultValue(null),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_on' => $this->timestamp()->defaultValue(null),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex(
            'idx-region-country_id',
            'region',
            'country_id'
        );

        $this->addForeignKey(
            'fk-region-country_id',
            'region',
            'country_id',
            'countries',
            'id',
            'CASCADE'
        );

        $this->createIndex(
            'idx-region-parent_id',
            'region',
            'parent_id'
        );

        $this->addForeignKey(
            'fk-region-parent_id',
            'region',
            'parent_id',
            'region',
            'id',
            'CASCADE'
        );

        $sql = file_get_contents(__DIR__ . '/../sql/region_insert.sql');
        \Yii::$app->db->pdo->exec($sql);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-region-country_id',
            'region'
        );

        $this->dropIndex(
            'idx-region-country_id',
            'region'
        );

        $this->dropForeignKey(
            'fk-region-parent_id',
            'region'
        );

        $this->dropIndex(
            'idx-region-parent_id',
            'region'
        );

        $this->dropTable('{{%region}}');
    }
}

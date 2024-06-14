<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%region}}`.
 */
class m200721_093043_create_area_table extends Migration
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
        $tableName = Yii::$app->db->tablePrefix . 'area';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('area');
        }

        $sql = file_get_contents(__DIR__ . '/../sql/area.sql');
        \Yii::$app->db->pdo->exec($sql);

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey(
            'fk-area-region_id',
            'area'
        );

        $this->dropTable('{{%area}}');
    }
}

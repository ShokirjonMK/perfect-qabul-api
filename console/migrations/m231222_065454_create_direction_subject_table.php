<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%direction_subject}}`.
 */
class m231222_065454_create_direction_subject_table extends Migration
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

        $tableName = Yii::$app->db->tablePrefix . 'direction_subject';
        if (!(Yii::$app->db->getTableSchema($tableName, true) === null)) {
            $this->dropTable('direction_subject');
        }

        $this->createTable('{{%direction_subject}}', [
            'id' => $this->primaryKey(),
            'direction_id' => $this->integer()->notNull(),
            'subject_id' => $this->integer()->notNull(),

            'question_count' => $this->integer()->notNull(),
            'ball' => $this->float()->notNull(),
            'level' => $this->json()->null(),

            'is_certificate' => $this->tinyInteger(1)->defaultValue(0),
            'max_ball' => $this->float()->notNull(),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'order' => $this->tinyInteger(1)->defaultValue(1),
            'created_at' => $this->integer()->Null(),
            'updated_at' => $this->integer()->Null(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ],$tableOptions);
        $this->addForeignKey('mk_direction_subject_table_direction_table', 'direction_subject', 'direction_id', 'direction', 'id');
        $this->addForeignKey('mk_direction_subject_table_subject_table', 'direction_subject', 'subject_id', 'subject', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%direction_subject}}');
    }
}

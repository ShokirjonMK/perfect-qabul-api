<?php

use yii\db\Migration;

/**
 * Class m211021_142749_profile_table
 */
class m211021_142749_profile_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('profile', [
            'id' => $this->primaryKey(),
            'user_id'=>$this->integer()->notNull(),
            'image'=>$this->string(255)->notNull(),
            'phone'=>$this->string(50)->notNull(),
            'phone_secondary'=>$this->string(50)->notNull(),
            'is_foreign'=>$this->integer()->notNull(),
            'last_name'=>$this->string(255)->notNull(),
            'first_name'=>$this->string(255)->notNull(),
            'middle_name'=>$this->string(255)->notNull(),
            'passport_seria'=>$this->string(255)->notNull(),
            'passport_number'=>$this->string(255)->notNull(),
            'passport_pin'=>$this->string(255)->notNull(),
            'birthday'=>$this->integer()->notNull(),
            'passport_file'=>$this->string(255)->notNull(),
            'country_id'=>$this->integer()->notNull(),
            'region_id'=>$this->integer()->notNull(),
            'area_id'=>$this->integer()->notNull(),
            'address'=>$this->string(255)->notNull(),
            'gender'=>$this->integer()->notNull(),
            'passport_given_date'=>$this->date()->notNull(),
            'passport_issued_date'=>$this->date()->notNull(),
            'passport_given_by'=>$this->string(255)->notNull(),
            'permanent_country_id'=>$this->integer()->notNull(),
            'permanent_region_id'=>$this->integer()->notNull(),
            'permanent_area_id'=>$this->integer()->notNull(),
            'permanent_address'=>$this->string(255)->notNull(),



            'order'=>$this->tinyInteger(1)->defaultValue(1),
            'status' => $this->tinyInteger(1)->defaultValue(1),
            'created_at'=>$this->integer()->notNull(),
            'updated_at'=>$this->integer()->notNull(),
            'created_by' => $this->integer()->notNull()->defaultValue(0),
            'updated_by' => $this->integer()->notNull()->defaultValue(0),
            'is_deleted' => $this->tinyInteger()->notNull()->defaultValue(0),
        ]);


        $this->addForeignKey('up_profile_user_id','profile','user_id','users','id');
        $this->addForeignKey('cp_profile_country_id','profile','country_id','countries','id');
        $this->addForeignKey('rp_profile_region_id','profile','region_id','region','id');
        $this->addForeignKey('ap_profile_area_id','profile','area_id','area','id');

        $this->addForeignKey('cp_profile_permanent_country_id','profile','permanent_country_id','countries','id');
        $this->addForeignKey('rp_profile_permanent_region_id','profile','permanent_region_id','region','id');
        $this->addForeignKey('ap_profile_permanent_area_id','profile','permanent_area_id','area','id');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('up_profile_user_id','profile');
        $this->dropForeignKey('cp_profile_country_id','profile');
        $this->dropForeignKey('rp_profile_region_id','profile');
        $this->dropForeignKey('ap_profile_area_id','profile');

        $this->dropForeignKey('cp_profile_permanent_country_id','profile');
        $this->dropForeignKey('rp_profile_permanent_region_id','profile');
        $this->dropForeignKey('ap_profile_permanent_area_id','profile');
        $this->dropTable('profile');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211021_115610_profile cannot be reverted.\n";

        return false;
    }
    */
}

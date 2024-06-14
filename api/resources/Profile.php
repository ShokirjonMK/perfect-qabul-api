<?php

namespace api\resources;

use common\models\Profile as CommonProfile;

class Profile extends CommonProfile
{
    
    /**
     * Rules
     *
     * @return array
     */
    public function rules()
    {
        return [
            [['last_name', 'first_name', 'gender'], 'required'],
            [['birthday', 'phone', 'phone_secondary'], 'safe'],
        ];
    }



    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields =  [
            'last_name',
            'first_name',
            'gender',
            'birthday' => function($model) {
                return date("Y-m-d", strtotime($model->birthday));
            }, 
            'phone',
            'phone_secondary',
            'avatar' => 'image'
        ];
        return $fields;
    }

    /**
     * Fields
     *
     * @return array
     */
    public function extraFields()
    {
        $extraFields =  [

        ];

        return $extraFields;
    }

}
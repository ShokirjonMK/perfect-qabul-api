<?php

namespace api\resources;

use common\models\model\AuthChild as CommonAuthChild;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Inflector;

class AuthChild extends CommonAuthChild
{
    use ResourceTrait;

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::class,
            ],
        ];
    }

    /**
     * Fields
     *
     * @return array
     */
    public function fields()
    {
        $fields = [
            'parent',
            'child',

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
        $extraFields = [
            'permissions',
            'parent',
        ];

        return $extraFields;
    }
}

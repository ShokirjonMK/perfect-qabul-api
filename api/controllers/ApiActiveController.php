<?php

namespace api\controllers;

use yii\rest\ActiveController;

class ApiActiveController extends ActiveController
{

    use ApiActionTrait;

    public $token_key = false;
    private $token_keys = array();
    
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];
}

<?php

namespace api\controllers;

use yii\rest\Controller;

class ApiController extends Controller
{
    use ApiActionTrait;

    public $token_key = false;
    private $token_keys = array();
    
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

}

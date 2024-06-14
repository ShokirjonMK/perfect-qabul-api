<?php

namespace app\components;

use yii\base\Behavior;
use yii\base\Controller;
use yii\web\ForbiddenHttpException;

class AuthorCheck extends Behavior
{
    public $permission;
    public $role;
    public $message;
    public $allowedRoles = [];

    public function init(){
        if(!$this->message ){
            $this->message = _e('You don\'t have access to do this action (author).');
        }
        if(empty($this->allowedRoles)){
            $this->allowedRoles = ['admin'];
        }
    }

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
        ];
    }

    public function beforeAction($event)
    {
        
        if (false) {
            throw new ForbiddenHttpException($this->message);
        }
        
    }
}
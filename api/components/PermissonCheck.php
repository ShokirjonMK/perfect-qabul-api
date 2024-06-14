<?php

namespace app\components;

use yii\base\Behavior;
use yii\base\Controller;
use yii\web\ForbiddenHttpException;

class PermissonCheck extends Behavior
{
    public $permission;
    public $role;
    public $message;
    public $allowedRoles = [];
    public $referenceControllers = [];

    public function init(){

        if(!$this->message ){
            $this->message = _e('You don\'t have access to do this action.');
        }
        list($controller, $action) = explode('_',$this->permission);
        if(!empty($this->referenceControllers) && in_array($controller, $this->referenceControllers)){
            $this->permission = 'reference' . '_' . $action;
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
        // \Yii::$app->user->identity->roleItem

//         dd(\Yii::$app->user->identity->attach_role);

        if (!in_array(\Yii::$app->user->identity->attach_role, $this->allowedRoles)) {
            if (!\Yii::$app->user->can($this->permission)) {
                throw new ForbiddenHttpException($this->message);
            }
        }
    }
}
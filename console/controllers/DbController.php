<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;
use yii\helpers\Inflector;

class DbController extends Controller
{
    public $controllersWithoutPermissions = [
        'auth'
    ];

    public function actionRbac()
    {
        // generate permission names from controller and action names
        $permissions = $this->generatePermissions('api');

        // create permissions and make a child them to admin role
        $auth = Yii::$app->authManager;
        $auth->removeAllPermissions();
        $admin = $auth->getRole('admin');
        $auth->removeChildren($admin);
        foreach ($permissions as $permission) {
            $autiItem = $auth->createPermission($permission);
            $autiItem->description = $this->permissonToWord($permission);
            $auth->add($autiItem);

            $auth->addChild($admin, $autiItem);
        }

        // adding permissions to other roles
        $wrongPermissions = [];
        $rolesPermissions = include 'api/config/roles-permissions.php';

        foreach ($rolesPermissions as $role => $permissions) {
            $itemRole = $auth->getRole($role); 
            if(!$itemRole){
                $itemRole = $auth->createRole($role);
                $itemRole->description = $role;
                $auth->add($itemRole);
            }else{
                $auth->removeChildren($itemRole);
            }  
            
            foreach ($permissions as $permission) {
                $autiItem = $auth->getPermission($permission);
                if ($autiItem) {
                    $auth->addChild($itemRole, $autiItem);
                }else{
                    $wrongPermissions[] = $permission;
                }
            }
        }

        $this->stdout("\n");
        if(count($wrongPermissions) > 0) {
            $this->stdout("Wrong permissions at api/config/roles-permissions.php", Console::FG_RED);
            $this->stdout("\n");
            print_r($wrongPermissions);
        }else{
            $this->stdout("Done ...", Console::FG_GREEN);
        }
        $this->stdout("\n\n");
    }

    protected function generatePermissions($folder)
    {
        $controllerlist = [];
        if ($handle = opendir($folder . '/controllers')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && substr($file, strrpos($file, '.') - 10) == 'Controller.php') {
                    $controllerlist[] = $file;
                }
            }
            closedir($handle);
        }
        asort($controllerlist);

        $fulllist = [];
        $permissions = [];
        foreach ($controllerlist as $controller) :
            $handle = fopen($folder . '/controllers/' . $controller, "r");
            if ($handle) {
                while (($line = fgets($handle)) !== false) {
                    if (preg_match('/public function action(.*?)\(/', $line, $display)) :
                        if (strlen($display[1]) > 0 && $display[1] != 's') :
                            $conrollerId = Inflector::camel2id(substr($controller, 0, -14));
                            if(!in_array($conrollerId, $this->controllersWithoutPermissions)) :
                                $actionId = Inflector::camel2id($display[1]);
                                $fulllist[$conrollerId][] = $actionId;
                                $permissions[] = $conrollerId . '_' . $actionId;
                            endif;
                        endif;
                    endif;
                }
            }
            fclose($handle);
        endforeach;
        return $permissions;
    }

    private function permissonToWord($permission){
        list($controller, $action) = explode('_', $permission);
        return ucfirst( strtolower(Inflector::camel2words(Inflector::id2camel($controller . '|' . $action))));
    }

}

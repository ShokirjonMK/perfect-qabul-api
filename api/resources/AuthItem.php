<?php

namespace api\resources;

use common\models\AuthAssignment;
use common\models\AuthItem as CommonAuthItem;
use common\models\AuthItemChild;
use api\resources\AuthChild as AuthChildRes;
use common\models\model\AuthChild;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Inflector;

class AuthItem extends CommonAuthItem
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
            'name',
            'category' => function ($model) {
                return $model->getParsedDesc('category');
            },
            'pretty_name' => function ($model) {
                return $model->getParsedDesc('description');
            },
            'description' => function ($model) {
                return $model->getParsedDesc('description');
            },
            'created_at' => function ($model) {
                return date('Y-m-d H:i', $model->created_at);
            },
            'updated_at' => function ($model) {
                return date('Y-m-d H:i', $model->updated_at);
            },
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
            'child',
        ];

        return $extraFields;
    }

    public function getParent()
    {
        return AuthChildRes::find()->where(['child' => $this->name])->all();
    }

    public function getChild()
    {
        return AuthChildRes::find()->where(['parent' => $this->name])->all();
    }

    public static function createPermission($model, $post) {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];
        if (!($model->validate())) {
            $errors[] = $model->errors;
            $transaction->rollBack();
            return simplify_errors($errors);
        }

        if ($model->save()) {
            $transaction->commit();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }
    public static function createRole($body)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        $bodyObj = json_decode($body);
        if (!$bodyObj) {
            $errors[] = [_e('Request body is not in valid JSON format.')];
        } else {
            foreach ($bodyObj as $obj) {
                $role = new AuthItem();
                $role->type = AuthItem::TYPE_ROLE;
                $role->name = $obj->role;
                $role->description = $obj->description;
                if (!$role->save()) {
                    $errors[] = $role->getErrorSummary(true);
                } else {
                    foreach ($obj->permissions as $permission) {
                        // check permission
                        $permissionItem = AuthItem::find()->where(['name' => $permission, 'type' => AuthItem::TYPE_PERMISSION])->one();
                        if (!$permissionItem) {
                            $errors[] = [_e('Permission \'{permission}\' not found.', ['permission' => $permission])];
                        } else {
                            $authItemChild = new AuthItemChild();
                            $authItemChild->parent = $obj->role;
                            $authItemChild->child = $permission;
                            if (!$authItemChild->save()) {
                                $errors[] = $authItemChild->getErrorSummary(true);
                            }
                        }
                    }

                    foreach ($obj->parents as $parent) {
                        $hasParent = AuthChild::findOne(['parent' => $parent, 'child' => $obj->role]);
                        $hasParentChild = AuthChild::findOne(['child' => $parent, 'parent' => $obj->role]);
                        if (!$hasParent && !$hasParentChild) {
                            $authChildModel = new AuthChild();
                            $authChildModel->parent = $parent;
                            $authChildModel->child = $obj->role;
                            $authChildModel->save();
                        }
                    }
                    if (isset($obj->childs)) {
                        foreach ($obj->childs as $child) {
                            $hasChild = AuthChild::findOne(['parent' => $obj->role, 'child' => $child]);
                            $hasChildParent = AuthChild::findOne(['child' => $obj->role, 'parent' => $child]);
                            if (!$hasChild && !$hasChildParent) {
                                $authChildModel = new AuthChild();
                                $authChildModel->parent = $obj->role;
                                $authChildModel->child = $child;
                                $authChildModel->save();
                            }
                        }
                    }
                }
            }
        }

        if (count($errors) == 0) {
            $transaction->commit();
            Yii::$app->authManager->invalidateCache();
            return true;
        } else {
            $transaction->rollBack();
            return simplify_errors($errors);
        }
    }

    public static function updateRole($body)
    {

        $transaction = Yii::$app->db->beginTransaction();
        $errors = [];

        // Validate data

        $bodyObj = json_decode($body);
        if (!$bodyObj) {
            $errors[] = [_e('Request body is not in valid JSON format.')];
        } else {
            foreach ($bodyObj as $obj) {
                // check role
                $role = AuthItem::find()->where(['name' => $obj->role, 'type' => AuthItem::TYPE_ROLE])->one();
                if (!$role) {
                    $errors[] = [_e('Role \'{role}\' not found.', ['role' => $obj->role])];
                } else {
                    $role->description = $obj->description;
                    AuthItemChild::deleteAll(['parent' => $obj->role]);
                    AuthChild::deleteAll(['parent' => $obj->role]);
                    AuthChild::deleteAll(['child' => $obj->role]);
                    foreach ($obj->permissions as $permission) {
                        // check permission
                        $permissionItem = AuthItem::find()->where(['name' => $permission, 'type' => AuthItem::TYPE_PERMISSION])->one();
                        if (!$permissionItem) {
                            $errors[] = [_e('Permission \'{permission}\' not found.', ['permission' => $permission])];
                        } else {
                            $authItemChild = new AuthItemChild();
                            $authItemChild->parent = $obj->role;
                            $authItemChild->child = $permission;
                            if (!$authItemChild->save()) {
                                $errors[] = $authItemChild->getErrorSummary(true);
                            }
                        }
                    }
                    foreach ($obj->parents as $parent) {
                        $hasParent = AuthChild::findOne(['parent' => $parent, 'child' => $obj->role]);
                        $hasParentChild = AuthChild::findOne(['child' => $parent, 'parent' => $obj->role]);
                        if (!$hasParent && !$hasParentChild) {
                            $authChildModel = new AuthChild();
                            $authChildModel->parent = $parent;
                            $authChildModel->child = $obj->role;
                            $authChildModel->save();
                        }
                    }
                    if (isset($obj->childs)) {
                        foreach ($obj->childs as $child) {
                            $hasChild = AuthChild::findOne(['parent' => $obj->role, 'child' => $child]);
                            $hasChildParent = AuthChild::findOne(['child' => $obj->role, 'parent' => $child]);
                            if (!$hasChild && !$hasChildParent) {
                                $authChildModel = new AuthChild();
                                $authChildModel->parent = $obj->role;
                                $authChildModel->child = $child;
                                $authChildModel->save();
                            }
                        }
                    }
                }
            }
        }

        if (count($errors) == 0) {
            if ($role->save()) {
                $transaction->commit();
                Yii::$app->authManager->invalidateCache();
                return true;
            }
        }
        $transaction->rollBack();
        return simplify_errors($errors);
    }

    public static function deleteRole($role)
    {

        $errors = [];

        // check role is exists
        $roleItem = AuthItem::find()->where(['name' => $role, 'type' => AuthItem::TYPE_ROLE])->one();
        if (!$roleItem) {
            $errors[] = [_e('Role \'{role}\' not found.', ['role' => $role])];
        } else {
            // check role assignments exists or not
            $itemChild = AuthAssignment::find()->where(['item_name' => $role])->one();
            if ($itemChild) {
                $errors[] = [_e('This role has assigned to user. Please remove this assignment first.')];
            } else {
                $roleItem->delete();
            }
        }

        if (count($errors) == 0) {
            Yii::$app->authManager->invalidateCache();
            return true;
        } else {
            return simplify_errors($errors);
        }
    }

    /**
     * Gets query for [[Children]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPermissions()
    {
        return $this->hasMany(AuthItem::className(), ['name' => 'child'])->viaTable('auth_item_child', ['parent' => 'name']);
    }

    public function getParsedDesc($field)
    {
        $exists = strpos($this->description, '|');
        $result = [
            'category' => $this->description,
            'description' => $this->description
        ];
        if ($exists) {
            list($category, $description) = explode('|', $this->description);
            $result = [
                'category' => Inflector::pluralize($category),
                'description' => ucfirst($description)
            ];
        }
        return $result[$field];
    }

    public static function getData($query)
    {
        $items = $query->all();
        $categories = [];
        foreach ($items as $one) {
            $categories[] = $one->getParsedDesc('category');
        }
        $categories = array_unique($categories);

        $data = [];
        foreach ($categories as $cate) {
            $permissions = [];
            foreach ($items as $one) {
                if ($cate == $one->getParsedDesc('category')) {
                    $permissions[] = [
                        'name' => $one->name,
                        'title' => $one->getParsedDesc('description'),
                    ];
                }
            }
            $data[] = [
                'category' => $cate,
                'permissions' => $permissions
            ];
        }

        return $data;
    }
}

<?php

namespace api\controllers;

use api\components\HttpBearerAuth;
use app\components\AuthorCheck;
use app\components\PermissonCheck;
use base\ResponseStatus;
use common\models\model\UserAccess;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Response;

trait ApiOpen
{
    /* public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        // User role ining joriy action uchun ruxsati bor yoki yo'qligini tekshiradi.
        $behaviors['permissionCheck'] = [
            'class' => PermissonCheck::class,
            'permission' => $this->getPermission(),
            'allowedRoles' => $this->getAllowedRoles(),
            'referenceControllers' => $this->getReferenceControllers(),
        ];

        // Userning joriy actionga ruxsati bor, lekin mualliflik huquqi bor yoki yo'qligini tekshiradi
        $behaviors['authorCheck'] = [
            'class' => AuthorCheck::class,
        ];

        return $behaviors;
    } */


    public function filterAll($query, $model)
    {
        $filter = Yii::$app->request->get('filter');
        $queryfilter = Yii::$app->request->get('filter-like');

        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $model->attributes())) {
                    $query = $query->andFilterWhere([$model->tableName() . '.' . $attribute => $id]);
                }
            }
        }

        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $model->attributes())) {
                    $query = $query->andFilterWhere(['like', $model->tableName() . '.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }
        return $query;
    }


    public function sort($query)
    {
        if (Yii::$app->request->get('sort')) {

            $sortVal = Yii::$app->request->get('sort');
            if (substr($sortVal, 0, 1) == '-') {
                $sortKey = SORT_DESC;
                $sortField = substr($sortVal, 1);
            } else {
                $sortKey = SORT_ASC;
                $sortField = $sortVal;
            }

            $query->orderBy([$sortField => $sortKey]);
        }

        return $query;
    }

    public function getData2($query, $perPage = 20, $validatePage = true) {
        return new ActiveDataProvider([
            'query' => $query,
            'totalCount' => count($query->all()),
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per-page') ?? $perPage,
                'validatePage' => $validatePage
            ],
        ]);
    }

    public function getData($query, $perPage = 20, $validatePage = true)
    {
        $get_data = Yii::$app->request->get('per-page');
        if ($get_data <= 0) {
            $get_data = $perPage;
        }

        $data = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $get_data ?? $perPage,
                'validatePage' => $validatePage
            ],
        ]);

        $dataRes = [];
        $dataRes['items'] = $data;
        $dataRes['_meta']['totalCount'] = (int) count($query->all());
        $dataRes['_meta']['currentPage'] = (int) $get_data ?? 1;
        $dataRes['_meta']['pageCount'] =
            (($dataRes['_meta']['totalCount'] / ($get_data ?? 20))
                >
                (int) ($dataRes['_meta']['totalCount'] / ($get_data ?? 20)))
            ?  (int) ($dataRes['_meta']['totalCount'] / ($get_data ?? 20)) + 1 :
            (int) ($dataRes['_meta']['totalCount'] / ($get_data ?? 20));

        $dataRes['_meta']['perPage'] = (int) $get_data ?? 20;

        if ($data) return $dataRes;
    }

    public function response($status, $message, $data = null, $errors = null, $responsStatusCode = 200)
    {
        Yii::$app->response->statusCode = $responsStatusCode;
        $response = [
            'status' => $status,
            'message' => $message
        ];
        if ($data) $response['data'] = $data;
        if ($errors) $response['errors'] = $errors;
        return $response;
    }

    public function load($model, $data)
    {
        return $model->load($data, '');
    }

    public function checkLead($model, $role)
    {
        $user_id = current_user_id();
        $roles = (object)\Yii::$app->authManager->getRolesByUser($user_id);

        if (property_exists($roles, $role)) {
            if ($model->user_id != $user_id) {
                return false;
            }
        }
        return true;
    }

    // public function isRole($roleName)
    // {
    //     $user_id = current_user_id();
    //     $roles = (object)\Yii::$app->authManager->getRolesByUser($user_id);

    //     if (property_exists($roles, $roleName)) {
    //         return true;
    //     } else {
    //         return false;
    //     }
    // }

    public function isSelf($userAccessTypeId)
    {
        $user_id = current_user_id();
        $roles = (object)\Yii::$app->authManager->getRolesByUser($user_id);

        $userAccess = UserAccess::findOne([
            'user_id' => $user_id,
            'user_access_type_id' => $userAccessTypeId,
        ]);

        $t['status'] = 3;


        foreach (_eduRoles() as $eduRole) {
            if (property_exists($roles, $eduRole)) {
                return $t;
            }
        }

        if ($userAccess && !(property_exists($roles, 'admin'))) {
            $t['status'] = 1;
            $t['UserAccess'] = $userAccess;
            return $t;
        } elseif (!property_exists($roles, 'admin')) {
            $t['status'] = 2;
            return $t;
        }

        return $t;
    }
}

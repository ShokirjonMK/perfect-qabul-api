<?php

namespace api\controllers;

use api\components\HttpBearerAuth;
use app\components\AuthorCheck;
use app\components\PermissonCheck;
use base\ResponseStatus;
use common\models\model\ActionLog;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Response;

trait ApiActionTrait
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        return $behaviors;
    }


    /**
     * After action
     *
     * @param $action
     * @return void
     */


//    public function beforeAction($action)
//    {
//        $this->generate_access_key();
//        Yii::$app->response->format = Response::FORMAT_JSON;
//
//        if (!$this->check_access_key()) {
//            $data = json_output();
//            $data['message'] = 'Incorrect token key!';
//            $this->asJson($this->response(0, _e('Incorrect token key! MK'), null, null, ResponseStatus::UNAUTHORIZED));
//            return false;
//        }
//
//        //   echo "Please wait!!"; die(); return 0;
//
//        $lang = Yii::$app->request->get('lang');
//
//        $languages = get_languages();
//        $langCodes = [];
//        foreach ($languages as $itemLang) {
//            $langCodes[] = $itemLang['lang_code'];
//        }
//        if (!in_array($lang, $langCodes)) {
//            $this->asJson($this->response(0, _e('Wrong language code selected (' . $lang . ').'), null, null, ResponseStatus::UPROCESSABLE_ENTITY));
//        } else {
//
//
//            // dd("asdasd");
//            // vdd(Yii::$app->request->get());
//            // vdd(Yii::$app->request->post());
//
//            // $action_log = new ActionLog();
//            // $action_log->user_id = current_user_id();
//            // $action_log->controller = Yii::$app->controller->id;
//            // $action_log->action = Yii::$app->controller->action->id;
//            // $action_log->method = $_SERVER['REQUEST_METHOD'];
//            // $action_log->get_data = json_encode(Yii::$app->request->get());
//            // $action_log->post_data = json_encode(Yii::$app->request->post());
//            // $action_log->save(false);
//            // Yii::$app->session->set('action_log', $action_log);
//
//            // dd(current_user_id());
//            Yii::$app->language = $lang;
//            return parent::beforeAction($action);
//        }
//    }


    public function generate_access_key()
    {
        $api_salt_key = API_SALT_KEY;
        $api_secret_key = API_SECRET_KEY;
        $api_token = $api_salt_key . '-' . $api_secret_key;

        $date1 = gmdate('Y-m-d H:i', strtotime('+1 min'));
        $date2 = gmdate('Y-m-d H:i', strtotime('+2 min'));

        $generated_key_1 = md5($api_token . $date1);
        $generated_key_2 = md5($api_token . $date2);

        $this->token_key = $generated_key_1;
        $this->token_keys = array($generated_key_1, $generated_key_2);
    }

    /**
     * Check api access key
     *
     * @return void
     */

    private function check_access_key()
    {
        return true;
        $token = '';
        $headers = Yii::$app->request->headers;
        $header_token = $headers->get('api-token');
        $param_token = Yii::$app->request->get('token');

        if ($header_token && is_string($header_token)) {
            $token = $header_token;
        }

        if ($param_token && is_string($param_token)) {
            $token = $param_token;
        }

        if (YII_DEBUG && $token == API_MASTER_KEY) {
            return true;
        } elseif ($token && in_array($token, $this->token_keys)) {
            return true;
        }
        return false;
    }

    public function filterAll($query, $model)
    {
        $filter = Yii::$app->request->get('filter');
        $queryfilter = Yii::$app->request->get('filter-like');

        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $model->attributes())) {
                    if (!($attribute == "status" && $id == "all")) {
                        $query = $query->andFilterWhere([$model->tableName() . '.' . $attribute => $id]);
                    }
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

    public function filterAnotherTable($query, $model)
    {
        $filter = Yii::$app->request->get('group-filter');

        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $id) {
                if (in_array($attribute, $model->attributes())) {
                    $query = $query->andFilterWhere([$model->tableName() . '.' . $attribute => $id]);
                }
            }
        }

        return $query;
    }

    public function filter($query, $model)
    {
        $filterEduYear = Yii::$app->request->get('edu_year_id');
        $filterType = Yii::$app->request->get('type');
//        $filterCourse = Yii::$app->request->get('course');

        if (isset($filterEduYear)) {
            $query = $query->andFilterWhere([$model->tableName().'.edu_year_id' => $filterEduYear]);

            if (isset($filterType)) {
                $query = $query->andWhere(['fall_spring' => $filterType]);
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
        };

        return $query;
    }

    public function getData($query, $perPage = 20, $validatePage = true)
    {
        return new ActiveDataProvider([
            'query' => $query,
            'totalCount' => count($query->all()),
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per-page') ?? $perPage,
                'validatePage' => $validatePage
            ],
        ]);
    }

    public function getDataNoPage($query, $perPage = 0, $validatePage = true)
    {

        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->request->get('per-page') ?? $perPage,
                'validatePage' => $validatePage
            ],
        ]);
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

    public function teacher_access($type = null, $select = [], $user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = current_user_id();
        }

        if (is_null($type)) {
            $type = 1;
        }

        if (empty($select)) {
            $select = ['id'];
        }

        if ($type == 1) {
            return TeacherAccess::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])
                ->andFilterWhere(['in', 'subject_id', Subject::find()
                    ->where(['is_deleted' => 0])
                    ->select('id')
                ])
                ->select($select);
        } elseif ($type == 2) {
            return TeacherAccess::find()
                ->asArray()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])
                ->andWhere(['in', 'subject_id', Subject::find()
                    ->where(['is_deleted' => 0])
                    ->select('id')
                ])
                ->select($select)
                ->all();
        }
    }

    public function subject_ids($type = null, $select = [], $user_id = null)
    {
        if (is_null($user_id)) {
            $user_id = current_user_id();
        }

        if (is_null($type)) {
            $type = 1;
        }

        if (empty($select)) {
            $select = ['id'];
        }

        if (isRole("mudir", $user_id)) {
            return Subject::find()
                ->where(['is_deleted' => 0])
                ->where(['in', 'kafedra_id', Kafedra::find()
                    ->where(['is_deleted' => 0, 'user_id' => $user_id])
                    ->select('id')])
                ->select($select);
        }

        if (isRole("teacher", $user_id)) {
            return Subject::find()
                ->where(['is_deleted' => 0])
                ->andWhere(['in', 'subject_id', TeacherAccess::find()
                    ->where(['user_id' => $user_id, 'is_deleted ' => 0])
                    ->andWhere(['in', 'subject_id', Subject::find()
                        ->where(['is_deleted' => 0])
                        ->select('id')])
                    ->select(['subject_id'])])
                ->select($select);
        }


        return Subject::find()
            ->where(['is_deleted' => 0])
            ->select($select);


        if ($type == 1) {
            return TeacherAccess::find()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])
                ->andWhere(['in', 'subject_id', Subject::find()
                    ->where(['is_deleted' => 0])
                    ->select('id')])
                ->select($select);
        } elseif ($type == 2) {
            return TeacherAccess::find()
                ->asArray()
                ->where(['user_id' => $user_id, 'is_deleted' => 0])
                ->andWhere(['in', 'subject_id', Subject::find()
                    ->where(['is_deleted' => 0])
                    ->select('id')])
                ->select($select)

                ->all();
        }

        return null;
    }

    public function isSelf($userAccessTypeId, $type = null)
    {
        if (is_null($type)) {
            $type = 1;
        }

        $user_id = current_user_id();
        $roles = (object)\Yii::$app->authManager->getRolesByUser($user_id);

        if ($type == 2) {
            $userAccess = UserAccess::find()
                ->select('table_id')
                ->where([
                    'user_id' => $user_id,
                    'user_access_type_id' => $userAccessTypeId,
                    'is_leader' => UserAccess::IS_LEADER_TRUE,
                    'status' => 1,
                    'is_deleted' => 0
                ])
                ->asArray()
                ->one();
        } else {
            $userAccess = [];
            $userAccessQuery = UserAccess::find()
                ->select('table_id')
                ->where([
                'user_id' => $user_id,
                'user_access_type_id' => $userAccessTypeId,
                'status' => 1,
                'is_deleted' => 0
            ])
                ->groupBy('table_id')
                ->asArray()->all();

            foreach ($userAccessQuery as $value) {
                $userAccess[] = $value['table_id'];
            }
        }

        $t['status'] = 3;

        foreach (_eduRoles() as $eduRole) {
            if (property_exists($roles, $eduRole)) {
                return $t;
            }
        }

        if (property_exists($roles, 'hr')) {
            return $t;
        }

        if (property_exists($roles, 'kpi_check')) {
            return $t;
        }

        if (property_exists($roles, 'hostel')) {
            return $t;
        }

        if (property_exists($roles, 'rector')) {
            return $t;
        }


        if (property_exists($roles, 'corruption')) {
            return $t;
        }

        if (property_exists($roles, 'justice')) {
            return $t;
        }

        if (property_exists($roles, 'prorector')) {
            return $t;
        }

        if (count($userAccess) > 0 && !(property_exists($roles, 'admin'))) {
            $t['status'] = 1;
            $t['UserAccess'] = $userAccess;
            return $t;
        } elseif (!property_exists($roles, 'admin')) {
            $t['status'] = 2;
            return $t;
        }

        return $t;
    }

    public static function student($type = null, $user_id = null)
    {
        if ($user_id == null) {
            $user_id = current_user_id();
        }
        if ($type == null) {
            $type = 1;
        }
        $student = Student::findOne(['user_id' => $user_id]);
        if ($type == 1) {
            return  $student->id ?? null;
        } elseif ($type == 2) {
            return  $student ?? null;
        }
    }
}

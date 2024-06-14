<?php

namespace api\controllers;

use api\components\MipServiceMK;
use api\forms\Login;
use Yii;
use api\resources\User;
use base\ResponseStatus;
use common\models\AuthAssignment;
use common\models\model\AuthChild;
use common\models\Profile;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class UserController extends ApiActiveController
{
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            'student',
                            'admin'
                        ],
                    ]
                ],
            ],
        ]);
    }

    public $modelClass = 'api\resources\User';

    public function actions()
    {
        return [];
    }

    public function actionMe()
    {
        $data = null;
        $errors = [];
        $user = User::findOne(current_user_id());

        if (isset($user)) {
            if ($user->status === User::STATUS_ACTIVE) {
                $profile = $user->profile;
                $isMain = Yii::$app->request->get('is_main') ?? 1;
                if ($isMain == 0) {
                    if (!$user->attach_role == 'student') {
                        Login::logout();
                        return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
                    }
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name ?? '',
                        'first_name' => $profile->first_name ?? '',
                        'role' => $user->attach_role,
                        'step' => $user->step,
                        'access_token' => $user->access_token,
                        'student' => $user->student,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                } elseif ($isMain == 1) {
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name ?? '',
                        'first_name' => $profile->first_name ?? '',
                        'profile' => $profile ?? '',
                        'role' => $user->attach_role,
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                } else {
                    $data = [
                        'user_id' => $user->id,
                        'username' => $user->username,
                        'last_name' => $profile->last_name ?? '',
                        'first_name' => $profile->first_name ?? '',
                        'access_token' => $user->access_token,
                        'expire_time' => date("Y-m-d H:i:s", $user->expireTime),
                    ];
                }
            } else {
                return $this->response(1, _e('User is not active'), $data, null, ResponseStatus::UNAUTHORIZED);
            }
            if (count($errors) == 0) {
                return $this->response(1, _e('User successfully refreshed'), $data, null, ResponseStatus::OK);
            } else {
                return ['is_ok' => false, 'errors' => simplify_errors($errors)];
            }
        } else {
            return ['is_ok' => false, 'errors' => simplify_errors($errors)];
        }
    }

    public function actionLogout()
    {
        if (Login::logout()) {
            return $this->response(1, _e('User successfully Log Out'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('User not found'), null, null, ResponseStatus::NOT_FOUND);
        }
    }

    public function actionIndex($lang)
    {
        if (isRole('student')) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
        }
        $model = new User();

        $query = $model->find()
            ->with(['profile'])
            ->andWhere(['users.deleted' => 0])
            ->join('LEFT JOIN', 'profile', 'profile.user_id = users.id')
            ->join('LEFT JOIN', 'auth_assignment', 'auth_assignment.user_id = users.id')
            ->andFilterWhere(['like', 'username', Yii::$app->request->get('query')]);

//         $query = $query->andFilterWhere(['!=', 'auth_assignment.item_name', currentRole()]);


        $profile = new Profile();
        $filter = Yii::$app->request->get('filter');
        $filter = json_decode(str_replace("'", "", $filter));
        if (isset($filter)) {
            foreach ($filter as $attribute => $value) {
                $attributeMinus = explode('-', $attribute);
                if (isset($attributeMinus[1])) {
                    if ($attributeMinus[1] == 'role_name') {
                        if (is_array($value)) {
                            $query = $query->andWhere(['not in', 'auth_assignment.item_name', $value]);
                        }
                    }
                }
                if ($attribute == 'role_name') {
                    if (is_array($value)) {
                        $query = $query->andWhere(['in', 'auth_assignment.item_name', $value]);
                    } else {
                        $query = $query->andFilterWhere(['like', 'auth_assignment.item_name', '%' . $value . '%', false]);
                    }
                }
                if (in_array($attribute, $profile->attributes())) {
                    $query = $query->andFilterWhere(['profile.' . $attribute => $value]);
                }
            }
        }
        $queryfilter = Yii::$app->request->get('filter-like');
        $queryfilter = json_decode(str_replace("'", "", $queryfilter));
        if (isset($queryfilter)) {
            foreach ($queryfilter as $attributeq => $word) {
                if (in_array($attributeq, $profile->attributes())) {
                    $query = $query->andFilterWhere(['like', 'profile.' . $attributeq, '%' . $word . '%', false]);
                }
            }
        }

        // filter
        $query = $this->filterAll($query, $model);

        // sort
        $query = $this->sort($query);

        // data
        $data = $this->getData($query);
        // $data = $query->all();

        return $this->response(1, _e('Success'), $data);
    }

    public function actionCreate()
    {
        if (isRole('student')) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
        }
        $model = new User();
        $profile = new Profile();
        $post = Yii::$app->request->post();

        $this->load($model, $post);
        $this->load($profile, $post);
        $result = User::createItem($model, $profile, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('User successfully created.'), $model, null, ResponseStatus::CREATED);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }

    public function actionUpdate($id)
    {
        if (isRole('student')) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
        }
        $model = User::findOne($id);
        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        $profile = $model->profile;
        $post = Yii::$app->request->post();
        $this->load($model, $post);
        $this->load($profile, $post);
        $result = User::updateItem($model, $profile, $post);
        if (!is_array($result)) {
            return $this->response(1, _e('User successfully updated.'), $model, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::UPROCESSABLE_ENTITY);
        }
    }


    public function actionView($id)
    {
        if (isRole('student')) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
        }

        $model = User::find()
            ->with(['profile'])
            ->join('INNER JOIN', 'profile', 'profile.user_id = users.id')
            ->andWhere(['users.id' => $id,'users.deleted' => 0])
            ->one();

        if (!$model) {
            return $this->response(0, _e('Data not found.'), null, null, ResponseStatus::NOT_FOUND);
        }
        return $this->response(1, _e('Success.'), $model, null, ResponseStatus::OK);
    }

    public function actionDelete($id)
    {
        if (isRole('student')) {
            return $this->response(0, _e('There is an error occurred while processing.'), null, null, ResponseStatus::UNAUTHORIZED);
        }
        $result = User::deleteItem($id);
        if (!is_array($result)) {
            return $this->response(1, _e('User successfully deleted.'), null, null, ResponseStatus::OK);
        } else {
            return $this->response(0, _e('There is an error occurred while processing.'), null, $result, ResponseStatus::BAD_REQUEST);
        }
    }

}

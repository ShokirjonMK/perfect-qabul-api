<?php

namespace backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\data\Pagination;
use backend\models\User;
use base\BackendController;
use common\models\Department;
use common\models\EducationWork;
use common\models\Employee;
use common\models\Profile;
use common\models\Reference;
use common\models\Subject;
use common\models\UserSubject;
/**
 * User controller
 */
class UsersController extends BackendController
{
    use ListActionsTrait;

    public $url = '/users';

    /**
     * Displays user subject page
     *
     * @return string
     */
    public function actionSubjects()
    {
        $main_url = Url::to([$this->url]);
        $where = ['users.deleted' => 0, 'users.status' => 10];
        $limit_default = 20;
        $sort_default = 'a-z';

        $limit = input_get('limit', $limit_default);

        $args = ['sort' => $sort_default];
        $query = User::getItems('', $args)->andWhere($where);
        $count = $query->count();

        if (!empty($limit)) {
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        } else {
            $pagination = new Pagination(['totalCount' => $count]);
        }

        $users = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $listDepartments = Department::listAll(null, $this->selected_language);
        $subjects = Subject::listAll($this->selected_language);
        $languages = Reference::listAll('language',$this->selected_language);

        return $this->render('subjects', array(
            'main_url' => $main_url,
            'limit_default' => $limit_default,
            'sort_default' => $sort_default,
            'users' => $users,
            'pagination' => $pagination,
            'listDepartments' => $listDepartments,
            'subjects' => $subjects,
            'languages' => $languages,
        ));

    }

    /**
     * Displays subject binding page
     *
     * @return string
     */
    public function actionBindsubject($id)
    {
        $model = User::findOne($id);
        $profile = Profile::find()->with(['permanentPlace.parent'])->where(['user_id' => $id])->one();
        $employee = Employee::find()->where(['user_id' => $id])->one();
        $main_url = Url::to([$this->url]);
        $post_item = Yii::$app->request->post();

        if (!$model || !$profile) {
            return $this->render('error', array(
                'main_url' => $main_url,
            ));
        }

        if ($post_item) {
            $data = json_decode($post_item['data']);
            
            if (is_array($data)) {
                $readyData = [];
                
                foreach ($data as $row) {
                    if (!$row->subject) {
                        continue;
                    }
                    foreach ($row->langs as $lang) {
                        if (!$lang) {
                            continue;
                        }
                        $readyData[] =  $row->subject . ',' . $lang;
                    }
                }
                $readyData = array_unique($readyData);
                
                // if (!empty($readyData)) {
                    $transaction = Yii::$app->db->beginTransaction();
                    $error = false;

                    UserSubject::deleteAll(['user_id' => $id]);

                    foreach ($readyData as $row) {
                        list($subject, $lang) = explode(',', $row);
                        $userSubject = new UserSubject();
                        $userSubject->user_id = $id;
                        $userSubject->subject_id = $subject;
                        $userSubject->language_id = $lang;
                        if (!$userSubject->save()) {
                            $error = true;
                            dd($userSubject->errors);
                        }
                    }
                        

                    if ($error) {
                        $transaction->rollBack();
                    } else {
                        $transaction->commit();
                    }
                // }
            }


            return $this->refresh();
        }

        $this->registerJs(array(
            'dist/libs/tinymce/tinymce.min.js',
            'theme/components/tinymce-editor.js',
            'js/custom.js',
        ));

        $subjects = Subject::listAll($this->selected_language);
        $languages = Reference::listAll('language',$this->selected_language);

        return $this->render('bind-subject', [
            'main_url' => $main_url,
            'model' => $model,
            'profile' => $profile,
            'employee' => $employee,
            'subjects' => $subjects,
            'languages' => $languages,
            'userSubjects' => $model->userSubjects,
        ]);
    }

    /**
     * Page
     *
     * @param string $type
     * @param array $where_query
     * @param array $bulk_actions
     * @param array $where_in
     * @return void
     */
    private function page($type, $where_query, $bulk_actions, $where_in = array())
    {
        $main_url = Url::to([$this->url]);

        $limit_default = 20;
        $sort_default = 'a-z';

        $ajax = input_post('ajax');
        $limit = input_get('limit', $limit_default);

        $args = ['sort' => $sort_default];
        $query = User::getItems($type, $args)->andWhere($where_query);

        if (is_array($where_in) && $where_in) {
            $query->andWhere($where_in);
        }

        $count = $query->count();

        if ($ajax == 'bulk-actions') {
            $ajax_action = input_post('action');
            $ajax_items = input_post('items');
            $ajax_item_id = input_post('id');
            $items = explode(',', $ajax_items);

            $output = User::ajaxAction($ajax_action, $ajax_item_id, $items);

            echo json_encode($output);
            exit();
        }

        if (!empty($limit)) {
            $pagination = new Pagination(['totalCount' => $count, 'pageSize' => $limit]);
        } else {
            $pagination = new Pagination(['totalCount' => $count]);
        }

        $users = $query->offset($pagination->offset)
            ->limit($pagination->limit)
            ->all();

        $page_types = User::getPageTypes($type);

        return $this->render('index', array(
            'main_url' => $main_url,
            'page_types' => $page_types,
            'bulk_actions' => $bulk_actions,
            'limit_default' => $limit_default,
            'sort_default' => $sort_default,
            'users' => $users,
            'pagination' => $pagination,
        ));
    }

    /**
     * Displays create page
     *
     * @return string
     */
    public function actionCreate($id = null)
    {
        $model = new User();
        $profile = new Profile();
        $main_url = Url::to([$this->url]);
        $post_item = Yii::$app->request->post();

        if($id){

            $modelCopy = User::findOne($id);
            $profileCopy = Profile::find()->where(['user_id' => $id])->one();

            if (!$modelCopy || !$profileCopy) {
                return $this->render('error', [
                    'error_info' => [
                        'title' => _e('Not found'),
                        'text' => _e('User not found!'),
                        'desc' => _e('The user you were looking for does not exist, unavailable for you or deleted.'),
                    ],
                    'main_url' => $main_url,
                ]);
            }

            $model->setAttributes($modelCopy->getAttributes());
            $profile->setAttributes($profileCopy->getAttributes());

            $model->roleName = $modelCopy->role->item_name;

            $profile->dob = date('Y-m-d',strtotime($profile->dob));

        }

        if ($post_item) {

            $submit_button = input_post('submit_button');

            if ($submit_button == 'create_and_add_new' && $model->load($post_item) && $profile->load($post_item)) {
                
                $model->createUser($model, $profile);
                Yii::$app->session->setFlash('success-alert', _e("The user was created successfully."));
                return $this->redirect(['create']);

            } elseif ($model->load($post_item) && $profile->load($post_item)) {

                $user_id = $model->createUser($model, $profile);
                Yii::$app->session->setFlash('success-alert', _e("The user was created successfully."));
                return $this->redirect(['edit', 'id' => $user_id]);

            }
        }

        $this->registerJs(array(
            'dist/libs/tinymce/tinymce.min.js',
            'theme/components/tinymce-editor.js',
            'js/custom.js',
        ));

        return $this->render('create', [
            'main_url' => $main_url,
            'model' => $model,
            'profile' => $profile,
        ]);
    }

    /**
     * Displays edit page
     *
     * @return string
     */
    public function actionEdit($id)
    {
        $model = User::findOne($id);
        $profile = Profile::find()->with(['permanentPlace.parent'])->where(['user_id' => $id])->one();
        $main_url = Url::to([$this->url]);
        $post_item = Yii::$app->request->post();

        if (!$model || !$profile) {
            return $this->render('error', array(
                'main_url' => $main_url,
            ));
        }

        $model->roleName = $model->role->item_name;

        $profile->dob = date('Y-m-d',strtotime($profile->dob));

        if ($post_item) {
            $submit_button = input_post('submit_button');

            if ($submit_button == 'create_and_add_new' && $model->load($post_item) && $profile->load($post_item)) {
                $model->updateUser($model, $profile, $post_item);

                Yii::$app->session->setFlash('success-alert', _e("The user has been successfully updated."));
                return $this->redirect(['create']);
            } elseif ($model->load($post_item) && $profile->load($post_item)) {
                $model->updateUser($model, $profile, $post_item);

                Yii::$app->session->setFlash('success-alert', _e("The user has been successfully updated."));
                return $this->refresh();
            }
        }

        $this->registerJs(array(
            'dist/libs/tinymce/tinymce.min.js',
            'theme/components/tinymce-editor.js',
            'js/custom.js',
        ));

        return $this->render('update', [
            'main_url' => $main_url,
            'model' => $model,
            'profile' => $profile,
        ]);
    }

    /**
     * Displays edit page
     *
     * @return string
     */
    public function actionInfo($id)
    {
        $main_url = Url::to([$this->url]);
        $user = User::findOne($id);
        $profile = Profile::findOne(['user_id' => $id]);

        $tabs = array(
            ['link' => 'profile', 'name' => _e('Profile'), 'icon' => 'ri-information-line'],
            ['link' => 'activity', 'name' => _e('Activity'), 'icon' => 'ri-file-paper-line'],
            ['link' => 'sessions', 'name' => _e('Sessions'), 'icon' => 'ri-bar-chart-horizontal-line'],
        );

        $this->registerCss(array(
            'dist/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
            'dist/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css',
            'dist/libs/datatables.net-select-bs4/css/select.bootstrap4.min.css',
            'dist/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css',
        ));

        $this->registerJs(array(
            'dist/libs/datatables.net/js/jquery.dataTables.min.js',
            'dist/libs/datatables.net-buttons/js/dataTables.buttons.min.js',
            'dist/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
            'dist/libs/datatables.net-responsive/js/dataTables.responsive.min.js',
            'dist/js/pages/datatables.init.js',
        ));

        return $this->render('info', array(
            'main_url' => $main_url,
            'tabs' => $tabs,
            'user' => $user,
            'profile' => $profile,
        ));
    }

}

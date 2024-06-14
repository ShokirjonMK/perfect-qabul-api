<?php

namespace backend\controllers\hr;

use backend\controllers\ListActionsTrait;
use Yii;
use yii\helpers\Url;
use yii\data\Pagination;
use backend\models\EmployeeUser;
use common\models\enums\YesNo;
use backend\models\User;
use base\BackendController;
use common\models\Countries;
use common\models\Department;
use common\models\Employee;
use common\models\Job;
use common\models\Profile;
use common\models\Rank;
use common\models\Reference;
use common\models\Regions;
use common\models\Speciality;
use common\models\University;
use yii\helpers\VarDumper;

/**
 * Employees controller
 */
class EmployeesController extends BackendController
{
    
    use ListActionsTrait;
    
    public $url = 'hr/employees';
    private $perm_country_id = null;
    private $temp_country_id = null;
    private $birth_country_id = null;
    private $perm_region_id = null;
    private $temp_region_id = null;
    private $birth_region_id = null;
    private $allRegions = [];
    private $allDistricts = [];
    private $allReferences = [];

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
        $query = EmployeeUser::getItems($type, $args)->andWhere($where_query);

        if (is_array($where_in) && $where_in) {
            $query->andWhere($where_in);
        }

        $count = $query->count();

        if ($ajax == 'bulk-actions') {
            $ajax_action = input_post('action');
            $ajax_items = input_post('items');
            $ajax_item_id = input_post('id');
            $items = explode(',', $ajax_items);

            $output = EmployeeUser::ajaxAction($ajax_action, $ajax_item_id, $items);

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

        $page_types = EmployeeUser::getPageTypes($type);

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

        $main_url = Url::to([$this->url]);
        $post_item = Yii::$app->request->post();
        $model = new EmployeeUser();
        $profile = new Profile();
        $employee = new Employee();
        $isUpdate = false;
        if($id){
            $isUpdate = true;
            $modelCopy = EmployeeUser::findOne($id);
            $profileCopy = Profile::find()
                                    ->with([
                                        'permanentPlace.parent',
                                        'temporaryPlace.parent',
                                        'birthPlace.parent'
                                    ])->where(['user_id' => $id])
                                    ->one();
            $employeeCopy = Employee::find()->where(['user_id' => $id])->one();

            if (!$modelCopy || !$profileCopy || !$employeeCopy) {
                return $this->render('error', [
                    'error_info' => [
                        'title' => _e('Not found'),
                        'text' => _e('Employee not found!'),
                        'desc' => _e('The employee you were looking for does not exist, unavailable for you or deleted.'),
                    ],
                    'main_url' => $main_url,
                ]);
            }

            if(!in_array($modelCopy->role->item_name, EmployeeUser::$roleList)){
                return $this->render('error', [
                    'error_info' => [
                        'title' => _e('Wrong role name.'),
                        'text' => _e('Wrong role name.'),
                        'desc' => _e('Wrong role name.'),
                    ],
                    'main_url' => $main_url,
                ]);
            }

            $model->setAttributes($modelCopy->getAttributes());
            $profile->setAttributes($profileCopy->getAttributes());
            $employee->setAttributes($employeeCopy->getAttributes());

            $model->roleName = $modelCopy->role->item_name;

            $profile->dob = date('Y-m-d',strtotime($profile->dob));

            $permanentPlace = $profile->permanentPlace;
            $permanentRegion = ($permanentPlace) ? $profile->permanentPlace->parent : null;
            $profile->region_id = ($permanentPlace) ? $permanentPlace->parent_id : null;
            $profile->country_id = ($permanentRegion) ? $permanentRegion->country_id : null;

            $temporaryPlace = $profile->temporaryPlace;
            $temporaryRegion = ($temporaryPlace) ? $profile->temporaryPlace->parent : null;
            $profile->temporary_region_id = ($temporaryPlace) ? $temporaryPlace->parent_id : null;
            $profile->temporary_country_id = ($temporaryRegion) ? $temporaryRegion->country_id : null;

            $birthPlace = $profile->birthPlace;
            $birthRegion = ($birthPlace) ? $profile->birthPlace->parent : null;
            $profile->birth_region_id = ($birthPlace) ? $birthPlace->parent_id : null;
            $profile->birth_country_id = ($birthRegion) ? $birthRegion->country_id : null;

            $profile->passport_given_date = date('Y-m-d',strtotime($profile->passport_given_date));
            $profile->passport_validity_date = date('Y-m-d',strtotime($profile->passport_validity_date));
            $profile->residence_permit_date = date('Y-m-d',strtotime($profile->residence_permit_date));
            $profile->residence_permit_expire = date('Y-m-d',strtotime($profile->residence_permit_expire));

            $employee->rate = "'" . ($employee->rate + 0) . "'";
            $employee->languages = explode(',',$employee->languages);

            $this->perm_country_id = $profile->country_id;
            $this->temp_country_id = $profile->temporary_country_id;
            $this->birth_country_id = $profile->birth_country_id;

            $this->perm_region_id = $profile->region_id;
            $this->temp_region_id = $profile->temporary_region_id;
            $this->birth_region_id = $profile->birth_region_id;

        }else{

            // Defaults
            $model->roleName = 'employee';
            $profile->is_stateless = YesNo::NO;
            $profile->is_foreign = YesNo::NO;
            $profile->residence_permit = YesNo::NO;
            $employee->out_staff = YesNo::NO;
        }
        

        if ($post_item) {

            $submit_button = input_post('submit_button');

            if ($submit_button == 'create_and_add_new' && $model->load($post_item) && $profile->load($post_item) && $employee->load($post_item)) {
                
                $model->createUser($model, $profile, $employee);
                Yii::$app->session->setFlash('success-alert', _e("The user was created successfully."));
                return $this->redirect(['create']);

            } elseif ($model->load($post_item) && $profile->load($post_item) && $employee->load($post_item)) {
                
                $user_id = $model->createUser($model, $profile, $employee);
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
            'employee' => $employee,
            'dirs' => $this->getDirectories($isUpdate, $employee->department_id)
        ]);
    }

    /**
     * Displays edit page
     *
     * @return string
     */
    public function actionEdit($id)
    {
        $model = EmployeeUser::findOne($id);
        $profile = Profile::find()
                        ->with([
                            'permanentPlace.parent',
                            'temporaryPlace.parent',
                            'birthPlace.parent'
                        ])
                        ->where(['user_id' => $id])
                        ->one();
        $employee = Employee::find()->where(['user_id' => $id])->one();
        $main_url = Url::to([$this->url]);
        $post_item = Yii::$app->request->post();

        if (!$model || !$profile || !$employee) {
            return $this->render('error', [
                'error_info' => [
                    'title' => _e('Not found'),
                    'text' => _e('Employee not found!'),
                    'desc' => _e('The employee you were looking for does not exist, unavailable for you or deleted.'),
                ],
                'main_url' => $main_url,
            ]);
        }

        if(!in_array($model->role->item_name, EmployeeUser::$roleList)){
            return $this->render('error', [
                'error_info' => [
                    'title' => _e('Wrong role name.'),
                    'text' => _e('Wrong role name.'),
                    'desc' => _e('Wrong role name.'),
                ],
                'main_url' => $main_url,
            ]);
        }

        $model->roleName = $model->role->item_name;

        $profile->dob = date('Y-m-d',strtotime($profile->dob));

        $permanentPlace = $profile->permanentPlace;
        $permanentRegion = ($permanentPlace) ? $profile->permanentPlace->parent : null;
        $profile->region_id = ($permanentPlace) ? $permanentPlace->parent_id : null;
        $profile->country_id = ($permanentRegion) ? $permanentRegion->country_id : null;

        $temporaryPlace = $profile->temporaryPlace;
        $temporaryRegion = ($temporaryPlace) ? $profile->temporaryPlace->parent : null;
        $profile->temporary_region_id = ($temporaryPlace) ? $temporaryPlace->parent_id : null;
        $profile->temporary_country_id = ($temporaryRegion) ? $temporaryRegion->country_id : null;

        $birthPlace = $profile->birthPlace;
        $birthRegion = ($birthPlace) ? $profile->birthPlace->parent : null;
        $profile->birth_region_id = ($birthPlace) ? $birthPlace->parent_id : null;
        $profile->birth_country_id = ($birthRegion) ? $birthRegion->country_id : null;

        $profile->passport_given_date = date('Y-m-d',strtotime($profile->passport_given_date));
        $profile->passport_validity_date = date('Y-m-d',strtotime($profile->passport_validity_date));
        $profile->residence_permit_date = date('Y-m-d',strtotime($profile->residence_permit_date));
        $profile->residence_permit_expire = date('Y-m-d',strtotime($profile->residence_permit_expire));

        $employee->rate = "'" . ($employee->rate + 0) . "'";
        $employee->languages = explode(',',$employee->languages);

        $this->perm_country_id = $profile->country_id;
        $this->temp_country_id = $profile->temporary_country_id;
        $this->birth_country_id = $profile->birth_country_id;

        $this->perm_region_id = $profile->region_id;
        $this->temp_region_id = $profile->temporary_region_id;
        $this->birth_region_id = $profile->birth_region_id;
        
        if ($post_item) {
            $submit_button = input_post('submit_button');

            if ($submit_button == 'create_and_add_new' && $model->load($post_item) && $profile->load($post_item) && $employee->load($post_item)) {
                $model->updateUser($model, $profile, $employee, $post_item);

                Yii::$app->session->setFlash('success-alert', _e("The user has been successfully updated."));
                return $this->redirect(['create']);
            } elseif ($model->load($post_item) && $profile->load($post_item) && $employee->load($post_item)) {
                $model->updateUser($model, $profile, $employee, $post_item);

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
            'employee' => $employee,
            'dirs' => $this->getDirectories(true, $employee->department_id)
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

    private function getDirectories1($isUpdate = false,$perm_country_id = null, $perm_region_id = null,$temp_country_id = null, $temp_region_id = null,$birth_country_id = null, $birth_region_id = null, $department_id = null)
    {

        return [
            'countries' => Countries::listAll(),
            
            'permRegions' => ($isUpdate) ? Regions::listRegions($perm_country_id) : [],
            'permDistricts' => ($isUpdate) ? Regions::listDistricts($perm_region_id) : [],
            'tempRegions' => ($isUpdate) ? Regions::listRegions($temp_country_id) : [],
            'tempDistricts' => ($isUpdate) ? Regions::listDistricts($temp_region_id) : [],
            'birthRegions' => ($isUpdate) ? Regions::listRegions($birth_country_id) : [],
            'birthDistricts' => ($isUpdate) ? Regions::listDistricts($birth_region_id) : [],

            'departments' => Department::listAll(null, $this->selected_language),
            'jobs' => ($isUpdate) ? Job::listAll($department_id, $this->selected_language) : [],
            'ranks' => Rank::listAll($this->selected_language),
            'specialities' => Speciality::listAll($this->selected_language),
            'universities' => University::listAll($this->selected_language),
            'nationalities' => Reference::listAll('nationality', $this->selected_language),
            'languages' => Reference::listAll('language', $this->selected_language),
            'scienceDegrees' => Reference::listAll('science-degree', $this->selected_language),
            'scientificTitles' => Reference::listAll('scientific-title', $this->selected_language),
            'specialTitles' => Reference::listAll('special-title', $this->selected_language),
        ];
        
    } 

    private function getDirectories($isUpdate = false, $department_id = null)
    {
        $this->setRegions();
        $this->setDistricts();
        $this->setReferences();

        return [
            'countries' => Countries::listAll(),
            
            'permRegions' => ($isUpdate) ? $this->getListRegions($this->perm_country_id) : [],
            'tempRegions' => ($isUpdate) ? $this->getListRegions($this->temp_country_id) : [],
            'birthRegions' => ($isUpdate) ? $this->getListRegions($this->birth_country_id) : [],

            'permDistricts' => ($isUpdate) ? $this->getListDistricts($this->perm_region_id) : [],
            'tempDistricts' => ($isUpdate) ? $this->getListDistricts($this->temp_region_id) : [],
            'birthDistricts' => ($isUpdate) ? $this->getListDistricts($this->birth_region_id) : [],

            'departments' => Department::listAll(null, $this->selected_language),
            'jobs' => ($isUpdate) ? Job::listAll($department_id, $this->selected_language) : [],
            'ranks' => Rank::listAll($this->selected_language),
            'specialities' => Speciality::listAll($this->selected_language),
            'universities' => University::listAll($this->selected_language),

            'nationalities' => $this->getListReferences('nationality'),
            'languages' => $this->getListReferences('language'),
            'scienceDegrees' => $this->getListReferences('science-degree'),
            'scientificTitles' => $this->getListReferences('scientific-title'),
            'specialTitles' => $this->getListReferences('special-title'),
        ];
        
    }
    
    private function setRegions(){
        $country = [
            $this->perm_country_id, 
            $this->temp_country_id, 
            $this->birth_country_id
        ];
        $this->allRegions = Regions::listRegionsWithCountry($country);
    }

    private function getListRegions($parent_id = null){
        $list = [];
        foreach ($this->allRegions as $one) {
            if($one['country_id'] == $parent_id){
                $list[$one['id']] = $one['name'];    
            }
        } 
        return $list;  
    }

    private function setDistricts(){
        $region = [
            $this->perm_region_id, 
            $this->temp_region_id, 
            $this->birth_region_id
        ];
        $this->allDistricts = Regions::listDistrictsWithRegion($region);
    }

    private function getListDistricts($parent_id = null){
        $list = [];
        foreach ($this->allDistricts as $one) {
            if($one['parent_id'] == $parent_id){
                $list[$one['id']] = $one['name'];    
            }
        } 
        return $list;  
    }

    private function setReferences(){
        $type = [
            'nationality',    
            'language',    
            'science-degree',    
            'scientific-title',    
            'special-title'    
        ];
        $this->allReferences = Reference::listAllWithType($type, $this->selected_language);
    }

    private function getListReferences($type){
        $list = [];
        foreach ($this->allReferences as $one) {
            if($one['type'] == $type){
                $list[$one['id']] = $one['name'];    
            }
        } 
        return $list;  
    }


}

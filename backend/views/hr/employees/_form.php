<?php

use backend\models\EmployeeUser;
use common\models\enums\DisabilityGroup;
use common\models\enums\Education;
use common\models\enums\EducationDegree;
use common\models\enums\FamilyStatus;
use common\models\enums\Gender;
use common\models\enums\Rates;
use common\models\enums\YesNo;
use backend\widgets\StorageWidget;
use common\models\AuthItem;
use common\models\Countries;
use common\models\UsersSession;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

if ($model->isNewRecord) {
    $lastSession = array();
} else {
    $lastSession = UsersSession::getLog($model->id, 'last_session');
}

// Begin form
$form = ActiveForm::begin([
    'options' => [
        'class' => 'full-form'
    ]
]);

// dd(Rates::list());
?>


<!-- Nav tabs -->
<ul class="nav nav-tabs nav-tabs-custom nav-justified mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#general" role="tab">
            <span class=""><i class="far fa-user"></i></span>
            <span class="d-none d-sm-block"><?= _e('General'); ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#profile" role="tab">
            <span class=""><i class="far fa-address-card"></i></span>
            <span class="d-none d-sm-block"><?= _e('Profile'); ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#employee" role="tab">
            <span class=""><i class="fas fa-briefcase"></i></span>
            <span class="d-none d-sm-block"><?= _e('Work'); ?></span>
        </a>
    </li>
</ul>

<div class="tab-content">


    <!-- Tab item -->
    <div class="tab-pane active" id="general" role="tabpanel">
        <div class="row">

            <!-- Left column -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group required-field">
                                <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'required' => 'required']) ?>
                            </div>

                            <div class="col-md-6 form-group required-field">
                                <?= $form->field($model, 'email')->textInput(['type' => 'email', 'required' => 'required']) ?>
                            </div>

                            <div class="col-md-6 form-group required-field">
                                <?= $form->field($profile, 'firstname')->textInput(['required' => 'required']) ?>
                            </div>

                            <div class="col-md-6 form-group required-field">
                                <?= $form->field($profile, 'lastname')->textInput(['class' => 'form-control', 'required' => 'required']) ?>
                            </div>

                            <div class="col-md-6 form-group">
                                <?= $form->field($profile, 'middlename')->textInput([]) ?>
                            </div>
                            <div class="col-md-6 form-group">

                            </div>

                            <?php if ($model->isNewRecord) : ?>
                                <div class="col-md-6 form-group">
                                    <div class="form-group required-field">
                                        <?= $form->field($model, 'password')->passwordInput(['required' => 'required']) ?>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <div class="form-group required-field">
                                        <?= $form->field($model, 'password_repeat')->passwordInput(['required' => 'required']) ?>
                                    </div>
                                </div>
                            <?php else : ?>
                                <div class="col-md-6 form-group">
                                    <div class="form-group">
                                        <?= $form->field($model, 'password')->passwordInput() ?>
                                    </div>
                                </div>
                                <div class="col-md-6 form-group">
                                    <div class="form-group">
                                        <?= $form->field($model, 'password_repeat')->passwordInput() ?>
                                    </div>
                                </div>
                            <?php endif; ?>


                        </div>
                    </div>
                </div>
            </div>
            <!-- /Left column -->

            <!-- Right column -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <div class="form-group required-field">
                                    <?php
                                    $listdata = ArrayHelper::map(AuthItem::find()
                                        ->where([
                                            'and',
                                            ['type' => 1],
                                            ['in', 'name', EmployeeUser::$roleList]
                                        ])
                                        ->all(), 'name', 'description');
                                    foreach ($listdata as $key => $row) {
                                        $listdata[$key] = _e($row);
                                    }

                                    echo $form->field($model, 'roleName')->dropDownList(
                                        $listdata,
                                        [
                                            'class' => 'form-control custom-select',
                                            'required' => 'required',
                                        ]
                                    ) ?>
                                </div>
                            </div>
                            <div class="col-md-12 form-group">
                                <div class="form-group required-field">
                                    <?= $form->field($model, 'status')->dropDownList([
                                        10 => _e('Active'),
                                        0 => _e('Pending'),
                                        5 => _e('Blocked'),
                                    ], [
                                        'class' => 'form-control custom-select',
                                        'required' => 'required',
                                    ]) ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">

                        <div class="form-group">
                            <label><?= _e('Register date'); ?></label>
                            <?php $created_at = $model->created_at > 0 ? date('d/m/Y H:i', $model->created_at) : '-'; ?>
                            <input type="text" class="form-control" value="<?= $created_at; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label><?= _e('Updated on'); ?></label>
                            <?php $updated_at = $model->updated_at > 0 ? date('d/m/Y H:i', $model->updated_at) : '-'; ?>
                            <input type="text" class="form-control" value="<?= $updated_at; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="last_login"><?= _e('Last login'); ?></label>
                            <input type="text" class="form-control" id="last_login" value="<?= $lastSession ? $lastSession->date : ''; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="last_ip"><?= _e('Last IP address'); ?></label>
                            <input type="text" class="form-control" id="last_ip" value="<?= $lastSession ? $lastSession->ip_address : ''; ?>" disabled>
                        </div>
                        <div class="form-group">
                            <label for="last_session"><?= _e('Last session'); ?></label>
                            <input type="text" class="form-control" id="last_session" value="<?= $lastSession ? $lastSession->session : ''; ?>" disabled>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Right column -->

        </div>
    </div>
    <!-- /Tab item -->

    <!-- Tab item -->
    <div class="tab-pane" id="profile" role="tabpanel">
        <div class="row">
            <!-- Left column -->
            <div class="col-md-12">

                <div class="card">
                    <div class="card-body">
                        <div class="form-group position-relative">
                            <?php
                            $form_template = StorageWidget::widget([
                                'format' => 'form',
                                'select_type' => 'single',
                                'action' => 'image_action',
                            ]);

                            echo $form->field($profile, 'image', ['template' => $form_template])
                                ->textInput(['type' => 'hidden', 'storage-browser-value' => 'image']) ?>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 form-group required-field">
                                <?= $form->field($profile, 'gender')->dropDownList(Gender::list(), [
                                    'class' => 'form-control',
                                    'prompt' => _e('...')
                                ]) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'phone')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'phone_secondary')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'dob')->textInput(['class' => 'form-control', 'type' => 'date']) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($employee, 'inps')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($employee, 'family_status')->dropDownList(FamilyStatus::list(), [
                                    'class' => 'form-control custom-select',
                                    'prompt' => _e('...'),
                                ]) ?>
                            </div>

                            <div class="col-md-12 form-group">
                                <?= $form->field($employee, 'children')->textInput(['class' => 'form-control']) ?>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <span id="url-regions" data-url="<?= Url::to(['json/get-regions']) ?>"></span>
                        <span id="url-jobs" data-url="<?= Url::to(['json/get-jobs']) ?>"></span>
                        <span id="url-districts" data-url="<?= Url::to(['json/get-districts']) ?>"></span>
                        <div class="row">

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'is_stateless')->dropDownList(YesNo::list(), ['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'is_foreign')->dropDownList(YesNo::list(), ['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'nationality_id')->dropDownList($nationalities, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...'),
                                ]) ?>
                            </div>

                            <div class="col-md-12 mt-3">        
                                <h5 class="card-title"><?= _e('Permanent residence')?></h5>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'country_id')->dropDownList($countries, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...'),
                                ]) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'region_id')->dropDownList($permRegions, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...'),
                                ]) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'permanent_place_id')->dropDownList($permDistricts, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...'),
                                ]) ?>
                            </div>

                            <div class="col-md-12 form-group">
                                <?= $form->field($profile, 'permanent_address')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-12 mt-3">        
                                <h5 class="card-title"><?= _e('Temporary residence')?></h5>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'temporary_country_id')->dropDownList($countries, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...'),
                                ]) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'temporary_region_id')->dropDownList($tempRegions, [
                                     'class' => 'form-control select2',
                                     'prompt' => _e('...'),
                                ]) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'temporary_place_id')->dropDownList($tempDistricts, [
                                     'class' => 'form-control select2',
                                     'prompt' => _e('...'),
                                ]) ?>
                            </div>

                            <div class="col-md-12 form-group">
                                <?= $form->field($profile, 'temporary_address')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-12 mt-3">        
                                <h5 class="card-title"><?= _e('Birth place')?></h5>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'birth_country_id')->dropDownList($countries, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...'),
                                ]) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'birth_region_id')->dropDownList($birthRegions, [
                                     'class' => 'form-control select2',
                                     'prompt' => _e('...'),
                                ]) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($profile, 'birth_place_id')->dropDownList($birthDistricts, [
                                     'class' => 'form-control select2',
                                     'prompt' => _e('...'),
                                ]) ?>
                            </div>

                           

                            


                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 form-group">
                                <?= $form->field($profile, 'passport_serial')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-2 form-group">
                                <?= $form->field($profile, 'passport_number')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-2 form-group">
                                <?= $form->field($profile, 'passport_pinip')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-6 form-group">
                                <?= $form->field($profile, 'passport_given_place')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-6 form-group">
                                <?= $form->field($profile, 'passport_given_date')->textInput(['class' => 'form-control', 'type' => 'date']) ?>
                            </div>

                            <div class="col-md-6 form-group">
                                <?= $form->field($profile, 'passport_validity_date')->textInput(['class' => 'form-control', 'type' => 'date']) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <?= $form->field($profile, 'residence_permit')->dropDownList(YesNo::list(), ['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-6 form-group">
                                <?= $form->field($profile, 'residence_permit_no')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-6 form-group">
                                <?= $form->field($profile, 'residence_permit_date')->textInput(['class' => 'form-control', 'type' => 'date']) ?>
                            </div>

                            <div class="col-md-6 form-group">
                                <?= $form->field($profile, 'residence_permit_expire')->textInput(['class' => 'form-control', 'type' => 'date']) ?>
                            </div>
                        </div>
                    </div>
                </div>




            </div>
            <!-- /Left column -->

        </div>
    </div>
    <!-- /Tab item -->

    <!-- Tab item -->
    <div class="tab-pane" id="employee" role="tabpanel">
        <div class="row">

            <!-- Left column -->
            <div class="col-md-8">

                <div class="card">


                    <div class="card-body">
                        <div class="row">




                            <div class="col-md-6 form-group">
                                <?= $form->field($employee, 'languages')->dropDownList($languages, [
                                    'class' => 'form-control select2',
                                    'multiple' => true,
                                    'prompt' => _e('...')
                                ]) ?>
                            </div>

                            <div class="col-md-6 form-group">
                                <?= $form->field($employee, 'lang_certs')->textInput(['class' => 'form-control']) ?>
                            </div>






                            <div class="col-md-12 form-group">
                                <?= $form->field($employee, 'scientific_work')->textInput(['class' => 'form-control']) ?>
                            </div>





                            <div class="col-md-4 form-group">
                                <?= $form->field($employee, 'science_degree_id')->dropDownList($scienceDegrees, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...')
                                ]) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($employee, 'scientific_title_id')->dropDownList($scientificTitles, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...')
                                ]) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($employee, 'special_title_id')->dropDownList($specialTitles, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...')
                                ]) ?>
                            </div>





                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="row">



                            <div class="col-md-3 form-group">
                                <?= $form->field($employee, 'is_convicted')->dropDownList(YesNo::list(), ['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-3 form-group">
                                <?= $form->field($employee, 'party_membership')->dropDownList(YesNo::list(), ['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-6 form-group">
                                <?= $form->field($employee, 'disability_group')->dropDownList(DisabilityGroup::list(), [
                                    'class' => 'form-control custom-select',
                                    'prompt' => _e('...'),
                                ]) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($employee, 'awords')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($employee, 'depuities')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-4 form-group">
                                <?= $form->field($employee, 'military_rank')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-12 form-group">
                                <?= $form->field($employee, 'other_info')->textarea(['class' => 'form-control']) ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- /Left column -->

            <!-- Right column -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <?= $form->field($employee, 'department_id')->dropDownList($departments, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...')
                                ]) ?>
                            </div>

                            <div class="col-md-12 form-group">
                                <?= $form->field($employee, 'job_id')->dropDownList($jobs, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...')
                                ]) ?>
                            </div>

                            <div class="col-md-12 form-group">
                                <?= $form->field($employee, 'rate')->dropDownList(Rates::list(), [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...')
                                ]) ?>
                            </div>

                            <div class="col-md-12 form-group">
                                <?= $form->field($employee, 'rank_id')->dropDownList($ranks, [
                                    'class' => 'form-control select2',
                                    'prompt' => _e('...')
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 form-group">
                                <?= $form->field($employee, 'reception_time')->textInput(['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-12 form-group">
                                <?= $form->field($employee, 'out_staff')->dropDownList(YesNo::list(), ['class' => 'form-control']) ?>
                            </div>

                            <div class="col-md-12 form-group">
                                <?= $form->field($employee, 'basic_job')->dropDownList(YesNo::list(), ['class' => 'form-control']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Right column -->

        </div>
    </div>
    <!-- /Tab item -->



</div>


<div class="mb-3 full-form-btns">
    <?php
    if ($model->isNewRecord) {
        echo Html::submitButton('<i class="ri-check-line mr-1"></i> ' . _t('app', 'Create & open'), ['class' => 'btn btn-success waves-effect btn-with-icon']);
        echo Html::submitButton('<i class="ri-add-circle-line mr-1"></i> ' . _t('app', 'Create & add another'), ['class' => 'btn btn-primary waves-effect btn-with-icon', 'name' => 'submit_button', 'value' => 'create_and_add_new']);
    } else {
        echo Html::submitButton('<i class="ri-check-line mr-1"></i> ' . _t('app', 'Save'), ['class' => 'btn btn-success waves-effect btn-with-icon']);
        echo Html::submitButton('<i class="ri-add-fill mr-1"></i> ' . _t('app', 'Save & add another'), ['class' => 'btn btn-primary waves-effect btn-with-icon', 'name' => 'submit_button', 'value' => 'create_and_add_new']);
    } ?>
</div>
<?php ActiveForm::end(); ?>
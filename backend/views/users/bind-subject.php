<?php

use common\models\Profile;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = _e('Attaching subject to teacher');
$this->breadcrumb_title = $this->title;
$this->breadcrumbs[] = ['label' => _e('Teacher subjects'), 'url' => $main_url . '/subjects'];

$form = ActiveForm::begin([
    'options' => [
        'class' => 'full-form'
    ]
]);

?>

<div class="user-bind-subject">
    <div class="card">
        <div class="row no-gutters">
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title" style="font-size: 24px;"><?= Profile::getFullname($profile) ?></h5>
                    <p class="card-text" style="font-size: 16px;">
                        <?= _e('Department')?>: <b><?= $employee->department->info->name ?? '-'?></b>
                        <br>
                        <?= _e('Job')?>: <b><?= $employee->job->info->name ?? '-'?></b>                        
                    </p>
                </div>
            </div>
            <div class="col-md-4 text-right">
                <img class="card-img img-fluid" style="width: 145px;" src="<?= Profile::getAvatar($profile) ?>" alt="<?= Profile::getFullname($profile) ?>">
            </div>

        </div>
    </div>
    <div class="card">
        <div class="card-body">

            <div class="dropdown float-right">
                <div class="btn btn-primary btn-sm btn-add-option">
                    <i class="mdi mdi-plus"></i> <?= _e('Add subject') ?>
                </div>

            </div>

            <h4 class="card-title"><?= _e('Subjects') ?></h4>
            <p class="card-title-desc"><?= _e('You can select one or more subjects and languages for this teacher') ?></p>

            <div class="table-responsive">
                <table class="table mb-0" id="tbl-subjects">

                    <thead class="table-light">
                        <tr>
                            <th style="width: 20%;"><?= _e('Subject') ?></th>
                            <th><?= _e('Languages') ?></th>
                            <th style="width: 120px;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr id="template" style="display: none;">
                            <td>
                                <select name="subjects[]" class="form-control">
                                    <option value="">...</option>
                                    <?php
                                    foreach ($subjects as $id => $name) {
                                    ?>
                                        <option value="<?= $id ?>"><?= $name ?></option>
                                    <?php } ?>
                                </select>
                            </td>

                            <td>
                                <select name="languages[]" multiple="multiple" class="form-control" style="width: 100%;">
                                    <option value="">...</option>
                                    <?php
                                    foreach ($languages as $id => $name) {
                                    ?>
                                        <option value="<?= $id ?>"><?= $name ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                            <td class="text-center">
                                <a href="javascript:void(0);" class="text-danger"><i class="mdi mdi-trash-can font-size-18 btn-remove-option"></i></a>
                            </td>
                        </tr>
                        <?php if ($userSubjects) { ?>
                            <?php foreach ($userSubjects as $userSubject) { ?>
                                <tr class="tr_item">
                                    <td>
                                        <select name="subjects[]" class="form-control select-subject select-two">
                                            <option value="">...</option>
                                            <?php foreach ($subjects as $id => $name) { ?>
                                                <option value="<?= $id ?>" <?php if ($userSubject['subject'] == $id) echo 'selected'; ?>><?= $name ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>

                                    <td>
                                        <select name="languages[]" multiple="multiple" class="form-control select-lang select-two" style="width: 100%;">
                                            <option value="">...</option>
                                            <?php
                                            foreach ($languages as $id => $name) {
                                            ?>
                                                <option value="<?= $id ?>" <?php if (in_array($id, $userSubject['langs'])) echo 'selected'; ?>><?= $name ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:void(0);" class="text-danger"><i class="mdi mdi-trash-can font-size-18 btn-remove-option"></i></a>
                                    </td>
                                </tr>
                            <? } ?>
                        <? } ?>

                    </tbody>
                </table>
            </div>

            <input type="hidden" id="selected_data" name="data" value="{}"/>

            <div class="mt-3 full-form-btns float-right">
                <?= Html::submitButton('<i class="mdi mdi-save"></i> ' . _e('Save'), ['class' => 'btn btn-success waves-effect']); ?>
                <?= Html::a(_e('Close'), ['subjects'], ['class' => 'btn btn-secondary waves-effect']); ?>
            </div>

        </div>
    </div>
</div>

<?php ActiveForm::end();  ?>
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

// Begin form
$form = ActiveForm::begin([
    'options' => [
        'class' => 'full-form',
    ],
]); ?>

<style>
    .permissions-table-custom-controls {
        display: flex;
        display: -ms-flexbox;
        align-items: center;
        justify-content: flex-start;
        flex-wrap: wrap;
    }

    .permissions-table-custom-controls .custom-control {
        margin: 5px 15px 5px 0;
    }
</style>

<div class="card">
    <div class="card-body">
        <div class="form-group required-field">
            <label class="control-label">Role name</label>
            <input type="text" class="form-control" required="required" ep-bind-action="title" value="Administrator">
        </div>

        <div class="row">
            <div class="col-md-6 required-field">
                <label class="control-label">Role key</label>
                <input type="text" class="form-control" required="required" value="admin">
            </div>
            <div class="col-md-6 required-field">
                <label class="control-label">Sort</label>
                <input type="number" class="form-control" required="required" value="1">
            </div>
        </div>
    </div>
</div>

<!-- Nav tabs -->
<ul class="nav nav-tabs nav-tabs-custom nav-justified mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#controller-permissions" role="tab">
            <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
            <span class="d-none d-sm-block">Default permissions</span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#core-permissions" role="tab">
            <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
            <span class="d-none d-sm-block">Core permissions</span>
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- Tab item -->
    <div class="tab-pane active" id="controller-permissions" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered mb-0" style="table-layout: fixed;">
                    <thead>
                        <th>Name</th>
                        <th width="75%">Permissions</th>
                    </thead>

                    <?php
                    $roles = ['Orders', 'Products', 'Fields / Brands', 'Fields / Categories', 'Content / Pages', 'Content / Posts', 'Content / Categories', 'Content / Tags', 'Customers', 'Shops', 'Sellers']; ?>

                    <tbody>
                        <?php foreach ($roles as $role) : ?>
                            <tr>
                                <td>
                                    <b><?= $role; ?></b>
                                </td>
                                </td>
                                <td>
                                    <div class="permissions-table-custom-controls">
                                        <div class="text-center custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="role-type-create-<?= $role; ?>">
                                            <label class="custom-control-label" for="role-type-create-<?= $role; ?>">Create</label>
                                        </div>

                                        <div class="text-center custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="role-type-update-<?= $role; ?>">
                                            <label class="custom-control-label" for="role-type-update-<?= $role; ?>">Update</label>
                                        </div>

                                        <div class="text-center custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="role-type-delete-<?= $role; ?>">
                                            <label class="custom-control-label" for="role-type-delete-<?= $role; ?>">Delete</label>
                                        </div>

                                        <div class="text-center custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="role-type-own-<?= $role; ?>">
                                            <label class="custom-control-label" for="role-type-own-<?= $role; ?>">Only Assigned</label>
                                        </div>

                                        <div class="text-center custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="role-type-rule-1-<?= $role; ?>">
                                            <label class="custom-control-label" for="role-type-rule-1-<?= $role; ?>">Rule 1</label>
                                        </div>

                                        <div class="text-center custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="role-type-rule-2-<?= $role; ?>">
                                            <label class="custom-control-label" for="role-type-rule-2-<?= $role; ?>">Rule 2</label>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- /Tab item -->

    <!-- Tab item -->
    <div class="tab-pane " id="core-permissions" role="tabpanel">
        <div class="card">
            <div class="card-body">
                <table class="table table-bordered mb-0" style="table-layout: fixed;">
                    <thead>
                        <th>Name</th>
                        <th width="75%">Permissions</th>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <b>Admin area</b>
                            </td>
                            <td>
                                <div class="permissions-table-custom-controls">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Has access</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Seller area</b>
                            </td>
                            <td>
                                <div class="permissions-table-custom-controls">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-seller-area">
                                        <label class="custom-control-label" for="role-type-seller-area">Has access</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Media</b>
                            </td>
                            <td>
                                <div class="permissions-table-custom-controls mb-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Upload file</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Rename file</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Delete file</label>
                                    </div>
                                </div>
                                <div class="permissions-table-custom-controls">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Create folder</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Rename folder</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Delete folder</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>System</b>
                            </td>
                            <td>
                                <div class="permissions-table-custom-controls mb-1">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Settings</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Languages</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Locations</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Options</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Payments</label>
                                    </div>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="role-type-admin-area">
                                        <label class="custom-control-label" for="role-type-admin-area">Roles & Permissions</label>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div> <!-- /Tab item -->
</div>

<div class="mb-3 full-form-btns">
    <?php
    if (isset($model->isNewRecord) && $model->isNewRecord) {
        echo Html::submitButton('<i class="ri-check-line mr-1"></i> Создать и открыть', ['class' => 'btn btn-success waves-effect btn-with-icon']);
        echo Html::submitButton('<i class="ri-add-circle-line mr-1"></i> Создать и добавить еще', ['class' => 'btn btn-primary waves-effect btn-with-icon', 'name' => 'submit_button', 'value' => 'create_and_add_new']);
    } else {
        echo Html::submitButton('<i class="ri-check-line mr-1"></i> Сохранить ', ['class' => 'btn btn-success waves-effect btn-with-icon']);
        echo Html::submitButton('<i class="ri-add-fill mr-1"></i>Сохранить и добавить еще', ['class' => 'btn btn-primary waves-effect btn-with-icon', 'name' => 'submit_button', 'value' => 'create_and_add_new']);
    } ?>
</div>
<?php ActiveForm::end(); ?>

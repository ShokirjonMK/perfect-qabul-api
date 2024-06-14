<?php

use yii\helpers\Url;

?>

<div class="row">
    <div class="col-md-6 col-aligment-left">
        <?php if ($actions) : ?>
            <div class="btn-group col-in card-top-btn-actions">
                <?php if (in_array('activate', $actions)) : ?>
                    <button action-btn="activate" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Activate'); ?>">
                        <i class="ri-check-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('block', $actions)) : ?>
                    <button action-btn="block" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Block'); ?>">
                        <i class="ri-indeterminate-circle-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('publish', $actions)) : ?>
                    <button action-btn="publish" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Activate'); ?>">
                        <i class="ri-check-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('unpublish', $actions)) : ?>
                    <button action-btn="unpublish" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Inactivate'); ?>">
                        <i class="ri-eye-off-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('trash', $actions)) : ?>
                    <button action-btn="trash" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Move to trash'); ?>">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('restore', $actions)) : ?>
                    <button action-btn="restore" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Restore'); ?>">
                        <i class="ri-refresh-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('delete', $actions)) : ?>
                    <button action-btn="delete" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Delete'); ?>">
                        <i class="ri-delete-bin-2-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('new-order', $actions)) : ?>
                    <button action-btn="new-order" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Set as new'); ?>">
                        <i class="ri-flashlight-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('complete-order', $actions)) : ?>
                    <button action-btn="complete-order" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Set as completed'); ?>">
                        <i class="ri-checkbox-circle-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('cancel-order', $actions)) : ?>
                    <button action-btn="cancel-order" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Cancel order'); ?>">
                        <i class="ri-close-circle-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('trash-order', $actions)) : ?>
                    <button action-btn="trash-order" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Delete order'); ?>">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('restore-order', $actions)) : ?>
                    <button action-btn="restore-order" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Restore order'); ?>">
                        <i class="ri-refresh-line"></i>
                    </button>
                <?php endif; ?>
                <?php if (in_array('delete-order', $actions)) : ?>
                    <button action-btn="delete-order" class="btn btn-light" data-toggle="tooltip" data-placement="bottom" title="<?= _e('Delete order permanently'); ?>">
                        <i class="ri-delete-bin-2-line"></i>
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($sort_array) : ?>
            <div class="btn-group col-in">
                <?php $sort = input_get('sort', $sort_default); ?>
                <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= _e('Sort'); ?> <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <?php
                    foreach ($sort_array as $sort_array_key => $sort_array_value) {
                        $sort_array_url = set_query_var('sort', $sort_array_key);

                        if ($sort_array_key == $sort) {
                            echo '<a class="dropdown-item active" href="' . $sort_array_url . '">' . $sort_array_value . '</a>';
                        } else {
                            echo '<a class="dropdown-item" href="' . $sort_array_url . '">' . $sort_array_value . '</a>';
                        }
                    } ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($limit_array) : ?>
            <div class="btn-group col-in">
                <?php $limit = input_get('limit', $limit_default); ?>
                <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= _e('Limit'); ?> <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <?php
                    foreach ($limit_array as $limit_array_key => $limit_array_value) {
                        $limit_array_url = set_query_var('limit', $limit_array_key);

                        if ($limit_array_key == $limit) {
                            echo '<a class="dropdown-item active" href="' . $limit_array_url . '">' . $limit_array_value . '</a>';
                        } else {
                            echo '<a class="dropdown-item" href="' . $limit_array_url . '">' . $limit_array_value . '</a>';
                        }
                    } ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($countries) : ?>
            <div class="btn-group col-in">
                <?php $country = input_get('country', $countries); ?>
                <select class="form-control select-linked" data-param="country">
                    <option value="-"><?= _e('All countries'); ?></option>
                    <?php
                    foreach ($countries as $country_key => $country_value) {
                        $country_value_url = set_query_var('country', $country_key);

                        if ($country_key == $country) {
                            echo '<option value="' . $country_value_url . '" selected>' . $country_value . '</option>';
                        } else {
                            echo '<option value="' . $country_value_url . '">' . $country_value . '</option>';
                        }
                    } ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if ($cities) : ?>
            <div class="btn-group col-in">
                <?php $city = input_get('city', $cities); ?>
                <button type="button" class="btn btn-light dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= _e('Region2'); ?> <i class="mdi mdi-chevron-down"></i>
                </button>
                <div class="dropdown-menu">
                    <?php
                    foreach ($cities as $city_key => $city_value) {
                        $city_value_url = set_query_var('city', $city_key);

                        if ($city_key == $city) {
                            echo '<a class="dropdown-item active" href="' . $city_value_url . '">' . $city_value . '</a>';
                        } else {
                            echo '<a class="dropdown-item" href="' . $city_value_url . '">' . $city_value . '</a>';
                        }
                    } ?>
                </div>
            </div>
        <?php endif; ?>

        

        <?php if ($departments) : ?>
            <div class="btn-group col-in">
                <?php $department = input_get('department', $departments); ?>
                <select class="form-control select-linked" data-param="department">
                    <option value="-"><?= _e('All departments'); ?></option>
                    <?php
                    foreach ($departments as $department_key => $department_value) {
                        $department_value_url = set_query_var('department', $department_key);

                        if ($department_key == $department) {
                            echo '<option value="' . $department_value_url . '" selected>' . $department_value . '</option>';
                        } else {
                            echo '<option value="' . $department_value_url . '">' . $department_value . '</option>';
                        }
                    } ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if ($subjects) : ?>
            <div class="btn-group col-in">
                <?php $subject = input_get('subject', $subjects); ?>
                <select class="form-control select-linked" data-param="subject">
                    <option value="-"><?= _e('All subjects'); ?></option>
                    <?php
                    foreach ($subjects as $subject_key => $subject_value) {
                        $subject_value_url = set_query_var('subject', $subject_key);

                        if ($subject_key == $subject) {
                            echo '<option value="' . $subject_value_url . '" selected>' . $subject_value . '</option>';
                        } else {
                            echo '<option value="' . $subject_value_url . '">' . $subject_value . '</option>';
                        }
                    } ?>
                </select>
            </div>
        <?php endif; ?>

        <?php if ($departmentTypes) : ?>
            <div class="btn-group col-in">
                <?php $type = input_get('type', $departmentTypes); ?>
                <select class="form-control select-linked" data-param="type">
                    <option value="-"><?= _e('All types'); ?></option>
                    <?php
                    foreach ($departmentTypes as $type_key => $type_value) {
                        $type_value_url = set_query_var('type', $type_key);

                        if ($type_key == $type) {
                            echo '<option value="' . $type_value_url . '" selected>' . $type_value . '</option>';
                        } else {
                            echo '<option value="' . $type_value_url . '">' . $type_value . '</option>';
                        }
                    } ?>
                </select>
            </div>
        <?php endif; ?>


    </div>

    <div class="col-md-6 col-aligment-right">
        

        <div class="btn-group col-in">
            <?php $s = input_get('s'); ?>

            <form method="GET" class="card-top-search">
                <input type="text" class="form-control" name="s" value="<?= $s; ?>" placeholder="<?= _e('Search'); ?>...">
                <?php if ($s) : ?>
                    <button type="button" class="card-top-search-clear">
                        <i class="ri-close-line"></i>
                    </button>
                <?php else : ?>
                    <button type="submit">
                        <i class="ri-search-line"></i>
                    </button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>
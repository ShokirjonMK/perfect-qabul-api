<?php $this->title = 'Roles'; ?>

<div class="card-top-links row">
    <div class="col-md-7">
        <div class="card-listed-links">
            <?php foreach ($page_group as $page_group_key => $page_group_value) : ?>
                <a href="<?= set_query_var('group', $page_group_key, $main_url); ?>" <?= $page_group_value['active'] ? 'class="active"' : ''; ?>>
                    <?= $page_group_value['name']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card-listed-links-right">
            <a href="<?= $main_url; ?>/create" class="btn btn-info waves-effect">
                <?= _e('Create role') ?>
            </a>
            <a href="<?= admin_url(); ?>" class="btn btn-secondary waves-effect">
                 <?= _t('app/product','Close')?>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th> <?= _t('app/product','name')?></th>
                        <th> <?= _t('app','Key')?></th>
                        <th class="text-center" width="100px"> <?= _t('app','Count')?></th>
                        <th width="100px"></th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td> <?= _t('app','Administrator')?></td>
                        <td>admin </td>
                        <td class="text-center">2</td>
                        <td class="ta-icons-block">
                            <div class="ta-icons-in">
                                <a href="<?= $main_url; ?>/update?id=1">
                                    <i class="ri-edit-2-line" data-toggle="tooltip" data-placement="top" title="Edit role"></i>
                                </a>
                            </div>
                            <div class="ta-icons-in">
                                <a href="javascript:void(0);" ta-single-action="delete" ta-single-id="1">
                                    <i class="ri-delete-bin-2-line" data-toggle="tooltip" data-placement="top" title="Delete permanently"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td> <?= _t('app','Department admin')?></td>
                        <td>department_admin </td>
                        <td class="text-center">12</td>
                        <td class="ta-icons-block">
                            <div class="ta-icons-in">
                                <a href="<?= $main_url; ?>/update?id=1">
                                    <i class="ri-edit-2-line" data-toggle="tooltip" data-placement="top" title="Edit role"></i>
                                </a>
                            </div>
                            <div class="ta-icons-in">
                                <a href="javascript:void(0);" ta-single-action="delete" ta-single-id="1">
                                    <i class="ri-delete-bin-2-line" data-toggle="tooltip" data-placement="top" title="Delete permanently"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td> <?= _t('app','Rector')?></td>
                        <td>rector</td>
                        <td class="text-center">1</td>
                        <td class="ta-icons-block">
                            <div class="ta-icons-in">
                                <a href="<?= $main_url; ?>/update?id=1">
                                    <i class="ri-edit-2-line" data-toggle="tooltip" data-placement="top" title="Edit role"></i>
                                </a>
                            </div>
                            <div class="ta-icons-in">
                                <a href="javascript:void(0);" ta-single-action="delete" ta-single-id="1">
                                    <i class="ri-delete-bin-2-line" data-toggle="tooltip" data-placement="top" title="Delete permanently"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td> <?= _t('app','Vice rector')?></td>
                        <td>vice_rector</td>
                        <td class="text-center">4</td>
                        <td class="ta-icons-block">
                            <div class="ta-icons-in">
                                <a href="<?= $main_url; ?>/update?id=1">
                                    <i class="ri-edit-2-line" data-toggle="tooltip" data-placement="top" title="Edit role"></i>
                                </a>
                            </div>
                            <div class="ta-icons-in">
                                <a href="javascript:void(0);" ta-single-action="delete" ta-single-id="1">
                                    <i class="ri-delete-bin-2-line" data-toggle="tooltip" data-placement="top" title="Delete permanently"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td> <?= _t('app','Dean of the faculty')?></td>
                        <td>dean</td>
                        <td class="text-center">3</td>
                        <td class="ta-icons-block">
                            <div class="ta-icons-in">
                                <a href="<?= $main_url; ?>/update?id=1">
                                    <i class="ri-edit-2-line" data-toggle="tooltip" data-placement="top" title="Edit role"></i>
                                </a>
                            </div>
                            <div class="ta-icons-in">
                                <a href="javascript:void(0);" ta-single-action="delete" ta-single-id="1">
                                    <i class="ri-delete-bin-2-line" data-toggle="tooltip" data-placement="top" title="Delete permanently"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td> <?= _t('app','Employee')?></td>
                        <td>employee</td>
                        <td class="text-center">562</td>
                        <td class="ta-icons-block">
                            <div class="ta-icons-in">
                                <a href="<?= $main_url; ?>/update?id=1">
                                    <i class="ri-edit-2-line" data-toggle="tooltip" data-placement="top" title="Edit role"></i>
                                </a>
                            </div>
                            <div class="ta-icons-in">
                                <a href="javascript:void(0);" ta-single-action="delete" ta-single-id="1">
                                    <i class="ri-delete-bin-2-line" data-toggle="tooltip" data-placement="top" title="Delete permanently"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td> <?= _t('app','Student')?></td>
                        <td>student</td>
                        <td class="text-center">3 547</td>
                        <td class="ta-icons-block">
                            <div class="ta-icons-in">
                                <a href="<?= $main_url; ?>/update?id=1">
                                    <i class="ri-edit-2-line" data-toggle="tooltip" data-placement="top" title="Edit role"></i>
                                </a>
                            </div>
                            <div class="ta-icons-in">
                                <a href="javascript:void(0);" ta-single-action="delete" ta-single-id="1">
                                    <i class="ri-delete-bin-2-line" data-toggle="tooltip" data-placement="top" title="Delete permanently"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td> <?= _t('app','Master')?></td>
                        <td>master</td>
                        <td class="text-center">987</td>
                        <td class="ta-icons-block">
                            <div class="ta-icons-in">
                                <a href="<?= $main_url; ?>/update?id=1">
                                    <i class="ri-edit-2-line" data-toggle="tooltip" data-placement="top" title="Edit role"></i>
                                </a>
                            </div>
                            <div class="ta-icons-in">
                                <a href="javascript:void(0);" ta-single-action="delete" ta-single-id="1">
                                    <i class="ri-delete-bin-2-line" data-toggle="tooltip" data-placement="top" title="Delete permanently"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                   
                </tbody>
            </table>
        </div>
    </div>
</div>

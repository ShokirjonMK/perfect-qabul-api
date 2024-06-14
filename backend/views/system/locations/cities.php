<?php

use \backend\widgets\BulkActions;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;

$this->title = _e('Regions'); ?>

<div class="card-top-links row">
    <div class="col-md-7">
        <div class="card-listed-links">
            <a href="<?= $main_url; ?>">
                <?= _e('Countries') ?>
            </a>
            <a href="<?= $main_url . '/cities'; ?>" class="active">
                <?= _e('Regions') ?>
            </a>
            <a href="<?= $main_url . '/regions'; ?>">
                <?= _e('Cities') ?>
            </a>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card-listed-links-right">
            <a href="<?= $main_url; ?>/create?type=city" class="btn btn-info waves-effect">
                <?= _e('Add new') ?>
            </a>
            <a href="<?= admin_url(); ?>" class="btn btn-secondary waves-effect">
                <?= _e('Close') ?>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="card-body-top">
            <?php
            $countries = ArrayHelper::map(common\models\Countries::find()->asArray()->all(), 'id', 'name');
            $bulk_actions_args = ['actions' => false, 'show_clang' => false, 'countries' => $countries];
            echo BulkActions::widget($bulk_actions_args); ?>
        </div>

        <div class="table-responsive">
            <table class="table mb-0">

                <thead class="thead-light">
                    <tr>
                        <th><?= _e('Name') ?></th>
                        <th><?= _e('Country') ?></th>
                        <th width="100px" class="text-center"><?= _e('Status') ?></th>
                        <th width="100px" class="text-center"><?= _e('Sort') ?></th>
                        <th width="100px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($city) : ?>
                        <?php foreach ($city as $key => $one) : ?>
                            <tr>
                                <td>
                                    <strong><?= $one->name; ?></strong>
                                </td>
                                <td>
                                    <strong><?= $one->country->name; ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php if ($one->status == 1) : ?>
                                        <span class="text-secondary"><?= _e('Active'); ?></span>
                                    <?php else : ?>
                                        <span class="text-danger"><?= _e('Disabled'); ?></span>
                                    <?php endif ?>
                                </td>
                                <td class="text-center">
                                    <strong><?= $one->sort; ?></strong>
                                </td>
                                <td class="ta-icons-block">
                                    <div class="ta-icons-in">
                                        <a href="<?= $main_url; ?>/edit?type=city&id=<?= $one->id; ?>">
                                            <i class="ri-edit-2-fill" data-toggle="tooltip" data-placement="top" title="<?= _e('Edit region'); ?>"></i>
                                        </a>
                                    </div>
                                    <div class="ta-icons-in">
                                        <a href="javascript:void(0);" ta-single-action="delete" ta-single-id="<?= $one->id; ?>">
                                            <i class="ri-delete-bin-2-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Delete'); ?>"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center table-not-found">
                                <i class="ri-error-warning-line"></i>
                                <div class="h5">
                                    <?= _e('Regions not found!'); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<nav>
    <?php echo LinkPager::widget([
        'pagination' => $pagination,
        'options' => ['class' => 'pagination pagination-rounded'],
        'linkContainerOptions' => ['class' => 'page-item'],
        'linkOptions' => ['class' => 'page-link'],
        'prevPageLabel' => 'previous',
        'nextPageLabel' => 'next',
        'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link']
    ]); ?>
</nav>
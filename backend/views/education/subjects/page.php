<?php

use backend\widgets\BulkActions;
use common\models\DirectionInfo;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use yii\helpers\Url;

$lexicon = admin_content_lexicon('lang_code');
$admin_current_lang = admin_current_lang('lang_code');

$controller = Yii::$app->controller;

$this->title = _e('Subjects'); 
?>

<div class="card-top-links row">
    <div class="col-md-7">
        <div class="card-listed-links">
            <?php foreach ($page_types as $page_type_key => $page_type) : ?>
                <a href="<?= $main_url . '/' . $page_type_key; ?>" <?= $page_type['active'] ? 'class="active"' : ''; ?>>
                    <?= $page_type['name']; ?>
                    <span>(<?= $page_type['count']; ?>)</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card-listed-links-right">
            <a href="<?= $main_url; ?>/create" class="btn btn-info waves-effect">
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

            <?= BulkActions::widget(array(
                'actions' => $bulk_actions,
                'limit_default' => $limit_default,
                'sort_default' => $sort_default
            )); ?>
        </div>

        <div class="table-responsive table-with-actions">
            <input type="hidden" id="table-selected-items" ta-selected-items>

            <table class="table mb-0">
                <thead class="thead-light">
                    <tr>
                        <th width="30px" class="ta-select-icon">
                            <i class="ri-checkbox-blank-line" data-ta-select-all></i>
                        </th>
                        <th><?= _e('Name') ?></th>
                        <th><?= _e('Hours') ?></th>
                        <th class="text-center" width="80px"><?= _e('Status'); ?></th>
                        <th width="180px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($content) : ?>
                        <?php foreach ($content as $key => $one) : ?>
                            <?php
                            $status_text = _e('Active');
                            $status_class = 'dot-status-success';

                            if ($one->deleted) {
                                $status_text = _e('Deleted');
                                $status_class = 'dot-status-danger';
                            } elseif ($one->status == 0) {
                                $status_text = _e('Inactive');
                                $status_class = 'dot-status-warning';
                            }

                            if ($one->info) {
                                $info = $one->info;
                            } else {
                                $info = DirectionInfo::find()
                                    ->where(['direction_id' => $one->id])
                                    ->one();
                            }

                            $edit_url = $main_url . "/edit/?id={$one->id}";
                            $created_on = date_create($one->created_on); ?>
                            <tr>
                                <td class="ta-select-icon">
                                    <i class="ri-checkbox-blank-line" data-ta-select="<?= $one->id ?>"></i>
                                </td>
                                <td>
                                    <a href="<?= $edit_url; ?>" class="products-table-title " title="<?= $info->name ?>">
                                        <?= $info->name ? $info->name : '-'; ?>
                                    </a>
                                    <nav class="nav products-table-nav">
                                        
                                        <li class="text-secondary align-items-center" title="<?= _e('Created date'); ?>">
                                            <i class="ri-calendar-2-line mr-1"></i><?= date_format($created_on, 'd/m/y H:i'); ?>
                                        </li>
                                    </nav>
                                </td>
                                <td>
                                    <?= $one->hoursText ?>
                                </td>
                                <td class="text-center">
                                    <span class="<?= $status_class; ?>" data-toggle="tooltip" data-placement="bottom" title="<?= $status_text; ?>"></span>
                                </td>
                                <td class="ta-icons-block">
                                    <?php if ($one->status == 1) : ?>
                                        <div class="ta-icons-in">
                                            <a href="javascript:void(0);" ta-single-action="unpublish" ta-single-id="<?= $one->id ?>">
                                                <i class="ri-eye-off-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Inactivate'); ?>"></i>
                                            </a>
                                        </div>
                                    <?php elseif ($one->status == 0) : ?>
                                        <div class="ta-icons-in">
                                            <a href="javascript:void(0);" ta-single-action="publish" ta-single-id="<?= $one->id ?>">
                                                <i class="ri-checkbox-circle-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Activate'); ?>"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($one->deleted) : ?>
                                        <div class="ta-icons-in">
                                            <a href="javascript:void(0);" ta-single-action="restore" ta-single-id="<?= $one->id ?>">
                                                <i class="ri-refresh-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Restore'); ?>"></i>
                                            </a>
                                        </div>
                                        <div class="ta-icons-in">
                                            <a href="javascript:void(0);" ta-single-action="delete" ta-single-id="<?= $one->id ?>">
                                                <i class="ri-delete-bin-2-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Delete permanently'); ?>"></i>
                                            </a>
                                        </div>
                                    <?php else : ?>
                                        <div class="ta-icons-in">
                                            <a href="javascript:void(0);" ta-single-action="trash" ta-single-id="<?= $one->id ?>">
                                                <i class="ri-delete-bin-6-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Move to trash'); ?>"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center table-not-found">
                                <i class="ri-error-warning-line"></i>
                                <div class="h5">
                                    <?= _e('Subjects not found'); ?>
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
        'prevPageLabel' => '<i class="ri-arrow-left-s-line"></i>',
        'nextPageLabel' => '<i class="ri-arrow-right-s-line"></i>',
        'disabledListItemSubTagOptions' => ['tag' => 'a', 'class' => 'page-link'],
    ]); ?>
</nav>
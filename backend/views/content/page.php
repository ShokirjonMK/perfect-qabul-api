<?php

use backend\widgets\BulkActions;
use common\models\ContentInfos;
use yii\widgets\LinkPager;
use yii\helpers\Url;

$langs = admin_active_langs('content_lexicon');
$lexicon = admin_content_lexicon('lang_code');

$controller = Yii::$app->controller;
$content_lexicon = array_value($controller->settings, 'lexicon');
$items_with_parent = array_value($controller->settings, 'items_with_parent');

$this->title = array_value($content_lexicon, 'title'); ?>

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
                        <?php if ($items_with_parent) : ?>
                            <th><?= _e('Parent'); ?></th>
                        <?php endif; ?>
                        <?php
                        if ($langs) {
                            foreach ($langs as $lang) {
                                echo '<th width="50" class="text-center">';
                                echo '<img src="' . $lang['flag'] . '" alt="' . $lang['name'] . '" height="10">';
                                echo '</th>';
                            }
                        } ?>
                        <th class="text-center" width="80px"><?= _e('Status'); ?></th>
                        <th width="180px"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($content) : ?>
                        <?php foreach ($content as $key => $one) : ?>
                            <?php
                            $status_text = _e('Published');
                            $status_class = 'dot-status-success';

                            if ($one->deleted) {
                                $status_text = _e('Deleted');
                                $status_class = 'dot-status-danger';
                            } elseif ($one->status == 0) {
                                $status_text = _e('Unpublished');
                                $status_class = 'dot-status-warning';
                            }

                            if ($one->info) {
                                $info = $one->info;
                            } else {
                                $info = ContentInfos::find()
                                    ->where(['content_id' => $one->id])
                                    ->one();
                            }

                            $edit_url = $main_url . "/edit/?id={$one->id}";
                            $created_on = date_create($one->created_on); ?>
                            <tr>
                                <td class="ta-select-icon">
                                    <i class="ri-checkbox-blank-line" data-ta-select="<?= $one->id ?>"></i>
                                </td>
                                <td>
                                    <a href="<?= $edit_url; ?>&lang=<?= $lexicon; ?>" class="products-table-title " title="<?= $info->title ?>">
                                        <?= $info->title ? $info->title : '-'; ?>
                                    </a>
                                    <nav class="nav products-table-nav">
                                        <?php if ($items_with_parent) : ?>
                                            <li class="text-secondary align-items-center">
                                                <?= _e('Child items'); ?>: <?= count_content_childs($one, 'child_count'); ?>
                                            </li>
                                        <?php endif; ?>
                                        <li class="text-secondary align-items-center" title="<?= _e('Created date'); ?>">
                                            <i class="ri-calendar-2-line mr-1"></i><?= date_format($created_on, 'd/m/y H:i'); ?>
                                        </li>
                                    </nav>
                                </td>
                                <?php if ($items_with_parent) : ?>
                                    <td>
                                        <?= (isset($one->parentInfo->title) && $one->parentInfo->title) ? $one->parentInfo->title : '-' ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($langs) : ?>
                                    <?php
                                    $translations_array = array();
                                    $translations = ContentInfos::find()->where(['content_id' => $one->id])->all();

                                    if ($translations) {
                                        foreach ($translations as $translation_item) {
                                            $translations_array[$translation_item->language] = $translation_item->title;
                                        }
                                    } ?>
                                    <?php foreach ($langs as $lang) : ?>
                                        <?php $lang_code = $lang['lang_code'] ?>
                                        <td width="20" class="text-center">
                                            <a href="<?= $edit_url; ?>&lang=<?= $lang_code; ?>" class="table-lang-icon">
                                                <?php if (isset($translations_array[$lang_code])) : ?>
                                                    <i class="ri-edit-2-fill" data-toggle="tooltip" data-placement="bottom" title="<?= $translations_array[$lang_code]; ?>"></i>
                                                <?php else : ?>
                                                    <i class="ri-menu-add-line" data-toggle="tooltip" data-placement="bottom" title="<?= _e('No translation'); ?>"></i>
                                                <?php endif; ?>
                                            </a>
                                        </td>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <td class="text-center">
                                    <span class="<?= $status_class; ?>" data-toggle="tooltip" data-placement="bottom" title="<?= $status_text; ?>"></span>
                                </td>
                                <td class="ta-icons-block">
                                    <?php if ($items_with_parent) : ?>
                                        <div class="ta-icons-in">
                                            <a href="<?= $main_url; ?>/create/?parent=<?= $one->id ?>">
                                                <i class="ri-add-circle-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Add child page'); ?>"></i>
                                            </a>
                                        </div>
                                        <div class="ta-icons-in">
                                            <a href="<?= set_query_var('parent', $one->id); ?>">
                                                <i class="ri-list-unordered" data-toggle="tooltip" data-placement="top" title="<?= _e('Child pages'); ?>"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                    <div class="ta-icons-in">
                                        <a href="<?= get_content_url($info, 'admin_lexicon'); ?>" target="_blank">
                                            <i class="ri-share-box-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Open on the site'); ?>"></i>
                                        </a>
                                    </div>
                                    <?php if ($one->status == 1) : ?>
                                        <div class="ta-icons-in">
                                            <a href="javascript:void(0);" ta-single-action="unpublish" ta-single-id="<?= $one->id ?>">
                                                <i class="ri-eye-off-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Unpublish'); ?>"></i>
                                            </a>
                                        </div>
                                    <?php elseif ($one->status == 0) : ?>
                                        <div class="ta-icons-in">
                                            <a href="javascript:void(0);" ta-single-action="publish" ta-single-id="<?= $one->id ?>">
                                                <i class="ri-checkbox-circle-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Publish'); ?>"></i>
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
                            <td colspan="<?= (5 + count($langs)); ?>" class="text-center table-not-found">
                                <i class="ri-error-warning-line"></i>
                                <div class="h5">
                                    <?= array_value($content_lexicon, 'not_found_message'); ?>
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
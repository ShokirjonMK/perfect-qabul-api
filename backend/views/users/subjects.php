<?php

use backend\models\User;
use common\models\Profile;
use backend\widgets\BulkActions;
use yii\widgets\LinkPager;

$this->title = _e('Teacher subjects'); ?>

<div class="card-top-links row">
    <div class="col-md-12 mb-2">
        <div class="card-listed-links-right">
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
                'actions' => [],
                'limit_default' => $limit_default,
                'sort_default' => $sort_default,
                'show_clang' => false,
                'departments' => $listDepartments
            )); ?>
        </div>

        <div class="table-responsive table-with-actions">
            <input type="hidden" id="table-selected-items" ta-selected-items>

            <table class="table mb-0">
                <thead class="thead-light">
                    <tr>
                        <th width="30px" class="ta-select-icon">
                        </th>
                        <th><?= _e('Fullname'); ?></th>
                        <th><?= _e('Student department'); ?></th>
                        <th><?= _e('Subjects'); ?></th>
                        <th class="text-center" width="80px"><?= _e('Status') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    <?php if ($users) : ?>
                        <?php foreach ($users as $user) : ?>
                            <?php
                            $i++;
                            $fullname = Profile::getFullname($user->profile);
                            ?>
                            <tr>
                                <td class="ta-select-icon">
                                <?=$i?>
                                </td>
                                <td>
                                    <a href="<?= $main_url; ?>/bindsubject/<?= $user->id ?>" class="products-table-title" title="<?= $fullname ?>">
                                        <?= $fullname; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                        $employee = $user->employee ?? null;
                                        $department = ($user->employee) ? $employee->department : null;
                                    ?>
                                    <?= ($department) ? $department->info->name : '-' ?>
                                </td>
                                <td>
                                    <?php
                                        $userSubjects = $user->userSubjects;
                                        $text = '';
                                        foreach ($userSubjects as $userSubject) {
                                            $text .= '<b>'.$subjects[$userSubject['subject']] . '</b>';
                                            $langs = [];
                                            foreach($userSubject['langs'] as $lang){
                                                $langs[] = $languages[$lang];    
                                            }

                                            $text .= ' ( ' . implode(', ', $langs) .  ' ) ';
                                        }

                                    ?>
                                    <?= ($text) ? $text : '-'; ?>
                                    
                                </td>
                                <td class="ta-icons-block">
                            
                                    <div class="ta-icons-in">
                                        <a href="<?= $main_url; ?>/info/<?= $user->id ?>">
                                            <i class="ri-information-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Informations'); ?>"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center table-not-found">
                                <i class="ri-error-warning-line"></i>
                                <div class="h5">
                                    <?= _e('Teacher subjects not found!'); ?>
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
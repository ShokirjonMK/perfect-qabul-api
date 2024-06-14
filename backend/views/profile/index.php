<?php

use backend\models\Locations;
use common\models\Logs;
use common\models\Profile;
use common\models\UsersSession;
use yii\helpers\Url;

$this->title = _e('Profile: {email}', [
    'email' => $user->username,
]);

$this->breadcrumb_title = _e('Profile');

$active_tab = input_get('tab', 'profile');

$this->registerCss(
    <<<CSS
    .table-user-infos td {
        width: 50%;
    }
CSS
); ?>

<!-- Nav tabs -->
<?php if ($tabs) : ?>
    <ul class="nav nav-tabs nav-tabs-custom nav-justified mb-3">
        <?php foreach ($tabs as $tab) : ?>
            <?php
            $tab_class = 'nav-link';

            if ($active_tab == $tab['link']) {
                $tab_class = 'nav-link active';
            } ?>
            <li class="nav-item">
                <a class="<?= $tab_class; ?>" href="<?= Url::current(['tab' => $tab['link']]); ?>">
                    <span class="d-block d-sm-none"><i class="<?= $tab['icon']; ?>"></i></span>
                    <span class="d-none d-sm-block"><?= $tab['name']; ?></span>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<div class="tab-content">
    <?php if ($active_tab == 'activity') : ?>
        <?php
        $usersActivitiesArgs = array(
            'where' => array('user_id' => $user->id),
            'order_by' => array('created_on' => 'DESC'),
        );
        $usersActivities = Logs::getAdminLogs($usersActivitiesArgs); ?>
        <!-- Tab item -->
        <div class="tab-pane active">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title"><?= _e('Activity list'); ?></h3>

                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th><?= _e('Name'); ?></th>
                                    <th><?= _e('Action'); ?></th>
                                    <th><?= _e('Session'); ?></th>
                                    <th width="200px"><?= _e('Date'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($usersActivities) : ?>
                                    <?php foreach ($usersActivities as $usersActivity) : ?>
                                        <?php
                                        $activityItem = Logs::logItemView($usersActivity);
                                        $browser_session = json_decode($usersActivity->browser); ?>
                                        <tr>
                                            <td><?= $activityItem->type_name ?></td>
                                            <td><?= $activityItem->action_name ?></td>
                                            <td>
                                                <?php
                                                if ($browser_session) {
                                                    echo '<span>IP: ' . $browser_session->ip_address . '</span>';
                                                    echo '<br>';
                                                    echo '<span>' . $browser_session->session . '</span>';
                                                } else {
                                                    echo '-';
                                                } ?>
                                            </td>
                                            <td><?= $activityItem->created_on ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr>
                                        <td colspan="4" class="text-center table-not-found">
                                            <i class="ri-error-warning-line"></i>
                                            <div class="h5">
                                                <?= _e('Data not found!'); ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div> <!-- /Tab item -->
    <?php elseif ($active_tab == 'sessions') : ?>
        <?php $usersSession = UsersSession::getLog($user->id); ?>
        <!-- Tab item -->
        <div class="tab-pane active">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title"><?= _e('User sessions'); ?></h3>

                    <div class="table-responsive">
                        <?php if ($usersSession && $usersSession['history']) : ?>
                            <table class="table table-bordered dt-responsive nowrap" data-table>
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th><?= _e('Browser'); ?></th>
                                        <th width="200"><?= _e('OS'); ?></th>
                                        <th width="200"><?= _e('IP address'); ?></th>
                                        <th width="200"><?= _e('Date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usersSession['history'] as $key => $session) : ?>
                                        <?php $i = $key + 1; ?>
                                        <tr>
                                            <th scope="row"><?= $i; ?></th>
                                            <td><?= $session->browser_name . ' ' . $session->browser_version ?></td>
                                            <td><?= $session->platform ?></td>
                                            <td><?= $session->ip_address ?></td>
                                            <td><?= $session->date; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else : ?>
                            <table class="table mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50">#</th>
                                        <th><?= _e('Browser'); ?></th>
                                        <th width="200"><?= _e('OS'); ?></th>
                                        <th width="200"><?= _e('IP address'); ?></th>
                                        <th width="200"><?= _e('Date'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center table-not-found">
                                            <i class="ri-error-warning-line"></i>
                                            <div class="h5">
                                                <?= _e('Sessions not found!'); ?>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div> <!-- /Tab item -->
    <?php else : ?>
        <!-- Tab item -->
        <div class="tab-pane active">
            <div class="profile-info-box">
                <div class="profile-info-left">
                    <div class="card">
                        <img class="card-img-top img-fluid profile-info-box-img" src="<?= Profile::getAvatar($profile); ?>" alt="Profile image">
                        <div class="card-body text-center">
                            <h3 class="card-title mt-0 mb-2"><?= $profile->firstname . ' ' . $profile->lastname . ' ' . $profile->middlename ?></h3>
                            <p class="mb-3"><?= $user->email ?></p>
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <a href="<?= Url::to(['/profile/settings']); ?>"><?= _e('Edit profile'); ?></a>
                                </li>
                                <li class="list-group-item">
                                    <a href="<?= Url::to(['/profile/password']); ?>"><?= _e('Change password'); ?></a>
                                </li>
                                <li class="list-group-item">
                                    <a href="<?= Url::to(['/auth/logout']); ?>"><?= _e('Log out'); ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="profile-info-right">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-user-infos table-bordered mb-0">
                                    <tbody>
                                        <tr>
                                            <td><b><?= _e('Username'); ?></b>: <?= $user->username ?></td>
                                            <td><b><?= _e('Email'); ?></b>: <?= $user->email ?></td>
                                        </tr>
                                        <tr>
                                            <td><b><?= _e('Phone'); ?></b>: <?= $profile->phone ? $profile->phone : '-' ?></td>
                                            <td><b><?= _e('Mobile'); ?></b>: </td>
                                        </tr>
                                        <tr>
                                            <td><b><?= _e('Gender'); ?></b>: <?= gender((is_numeric($profile->gender) ? $profile->gender : '0')) ?></td>
                                            <td><b><?= _e('Birthday'); ?></b>: <?= $profile->dob ?></td>
                                        </tr>
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> <!-- /Tab item -->
    <?php endif; ?>
</div>
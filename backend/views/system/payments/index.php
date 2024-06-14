<?php

use backend\models\Currency;

$this->title = _e('Currencies'); ?>

<div class="card-top-links row">
    <div class="col-md-7">
        <div class="card-listed-links">
            <a href="<?= $main_url; ?>" class="active">
                <?= _e('Rates') ?>
            </a>
            <a href="<?= $main_url . '/settings'; ?>">
                <?= _e('Settings') ?>
            </a>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card-listed-links-right">
            <a href="<?= $main_url; ?>/refresh-rate" class="btn btn-success waves-effect">
                <?= _e('Refresh currency rates') ?>
            </a>
            <a href="<?= admin_url(); ?>" class="btn btn-secondary waves-effect">
                <?= _e('Close') ?>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="thead-light">
                    <tr>
                        <th width="300px"><?= _e('Currency') ?></th>
                        <th class="text-center" width="150px"> <?= _e('Price') ?></th>
                        <th class="text-center" width="150px"> <?= _e('Previous') ?></th>
                        <th class="text-center" width="150px"> <?= _e('Change %') ?></th>
                        <th class="text-center" width="150px"> <?= _e('Change') ?></th>
                        <th class="text-center" width="200px"> <?= _e('Updated date') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($rates) : ?>
                        <?php foreach ($rates as $rate) : ?>
                            <?php
                            if ($rate->cvalue > $rate->cvbefore) {
                                $class = "success";
                                $title_class = "success";
                            } elseif ($rate->cvalue == $rate->cvbefore) {
                                $class = "secondary";
                                $title_class = "primary";
                            } else {
                                $class = "danger";
                                $title_class = "danger";
                            }
                            $ratePercentage = ($rate->cvalue / $rate->cvbefore) * 100;
                            $ratePercentage = (100 - $ratePercentage);
                            $rateDifference = ($rate->cvalue - $rate->cvbefore); ?>
                            <tr>
                                <td>
                                    <strong style="font-size:17px;"><?= $rate->ckey ?></strong>
                                    <p class="m-0"><?= $rate->cname ?></p>
                                </td>
                                <td class="text-center">
                                    <span class="text-<?= $title_class ?>"><?= Currency::valueFormat($rate->cvalue) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="text-secondary"><?= Currency::valueFormat($rate->cvbefore) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="text-<?= $class ?>"><?= Currency::valueFormat($ratePercentage) ?> %</span>
                                </td>
                                <td class="text-center">
                                    <span class="text-<?= $class ?>"><?= Currency::valueFormat($rateDifference) ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="text-secondary"><?= $rate->update_on ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center table-not-found">
                                <i class="ri-error-warning-line"></i>
                                <div class="h5">
                                    <?= _e('Currency rates are not available.<br>Please click the <b>Refresh rates</b> button!'); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $this->title = _e('Countries'); ?>

<div class="card-top-links row">
    <div class="col-md-12">
        <div class="card-listed-links">
            <a href="<?= $main_url; ?>" class="active">
                <?= _e('Countries') ?>
            </a>
            <a href="<?= $main_url . '/cities'; ?>">
                <?= _e('Regions') ?>
            </a>
            <a href="<?= $main_url . '/regions'; ?>">
                <?= _e('Cities') ?>
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
                        <th><?= _e('Name') ?></th>
                        <th class="text-center" width="150px"><?= _e('ISO'); ?></th>
                        <th class="text-center" width="150px"><?= _e('ISO3'); ?></th>
                        <th class="text-center" width="200px"> <?= _e('Number code') ?></th>
                        <th class="text-center" width="150px"> <?= _e('Phone number') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($countries) : ?>
                        <?php foreach ($countries as $key => $one) : ?>
                            <tr>
                                <td>
                                    <strong><?= $one->name; ?></strong>
                                </td>
                                <td class="text-center">
                                    <span class="text-primary"><?= $one->ISO; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="text-success"><?= $one->ISO3 ? $one->ISO3 : '-'; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="text-success"><?= $one->num_code ? $one->num_code : '-'; ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="text-secondary"><?= $one->phone_code > 0 ? $one->phone_code : '-'; ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center table-not-found">
                                <i class="ri-error-warning-line"></i>
                                <div class="h5">
                                    <?= _e('Countries not found!'); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
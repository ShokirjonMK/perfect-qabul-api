<?php
$site_currency = \backend\models\System::getSettingValue('site_currency');

$this->title = _e('Currencies'); ?>

<div class="card-top-links row">
    <div class="col-md-7">
        <div class="card-listed-links">
            <a href="<?= $main_url; ?>">
                <?= _e('Rates') ?>
            </a>
            <a href="<?= $main_url . '/settings'; ?>" class="active">
                <?= _e('Settings') ?>
            </a>
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
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th><?= _e('Name') ?></th>
                        <th width="200px"><?= _e('Code') ?></th>
                        <th class="text-center" width="100px"><?= _e('Active') ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($currencyNames) : ?>
                        <?php foreach ($currencyNames as $item) : ?>
                            <tr>
                                <td>
                                    <strong><?= $item->currency_name ?></strong>
                                </td>
                                <td>
                                    <strong class="text-primary"><?= $item->currency_code ?></strong>
                                </td>
                                <td class="text-center">
                                    <?php $checkbox_id = 'switch_' . $item->id; ?>
                                    <?php $checked = $item->status == 1 ? 'checked' : ''; ?>
                                    <?php $disabled = ($checked && $site_currency == $item->currency_code) ? 'disabled' : ''; ?>
                                    <div class="custom-control custom-switch" dir="ltr">
                                        <input type="checkbox" class="custom-control-input currency-switcher" value="<?= $item->id; ?>" id="<?= $checkbox_id; ?>" <?= $checked; ?> <?= $disabled; ?>>
                                        <label class="custom-control-label" for="<?= $checkbox_id; ?>"></label>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="4" class="text-center table-not-found">
                                <i class="ri-error-warning-line"></i>
                                <div class="h5">
                                    <?= _e('Currencies not found!'); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$this->registerJs(
    <<<JS

$(document).ready(function() {
    $(document).on('click', '.currency-switcher', function () {
        var id = parseInt($(this).val());
        var checked = $(this).prop('checked');

        if (!isNaN(id) && id > 0) {
            $.ajax({
                type: 'POST',
                url: window.location.href,
                data: {
                    ajax: 'update-action',
                    id: id,
                    checked: checked,
                },
                dataType: 'json',
                error: function () {
                    alert(ajax_error_msg);
                },
            });
        }
    });
});

JS
); ?>
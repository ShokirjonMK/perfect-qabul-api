<?php

$this->title = _e('Translations');

$trs_array = array();
$formatter = \Yii::$app->formatter;

if ($models) {
    foreach ($models as $item) {
        $trs_key = "{$item->path_key}-{$item->lang_key}";
        $trs_array[$trs_key] = $item->attributes;
    }
}

require('js-vars.php'); ?>

<style>
    .modal-translation-logs .modal-dialog {
        max-width: 1000px;
    }
</style>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th width="40%"><?= _e('Name') ?></th>
                        <th><?= _e('Words'); ?></th>
                        <th><?= _e('Last change'); ?></th>
                        <th width="140"></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($translations as $path_key => $path) : ?>
                        <?php
                        $trs_key = "{$path_key}-core";
                        $trs_item = array_value($trs_array, $trs_key);
                        $trs_date = array_value($trs_item, 'updated_on');
                        $trs_items = array_value($trs_item, 'translations', array()); ?>
                        <tr>
                            <td>
                                <a href="<?= $main_url; ?>/list-languages?id=<?= $path_key; ?>">
                                    <?= array_value($path, 'name'); ?>
                                </a>
                            </td>
                            <td>
                                <span translation-count="<?= $path_key; ?>"><?= count($trs_items); ?></span>
                            </td>
                            <td>
                                <span translation-date="<?= $path_key; ?>"><?= $trs_date ? $formatter->asDate($trs_date, 'php:d/m/Y H:i:s') : '-'; ?></span>
                            </td>
                            <td class="ta-icons-block">
                                <div class="ta-icons-in">
                                    <a href="javascript:void(0);" data-scan-translations="<?= $path_key; ?>">
                                        <i class="ri-refresh-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Scan translations'); ?>"></i>
                                    </a>
                                </div>
                                <div class="ta-icons-in">
                                    <a href="javascript:void(0);" data-toggle="modal" data-target="#translationModal<?= $path_key; ?>">
                                        <i class="ri-information-fill" data-toggle="tooltip" data-placement="top" title="<?= _e('Informations'); ?>"></i>
                                    </a>
                                </div>
                                <div class="ta-icons-in">
                                    <a href="javascript:void(0);">
                                        <i class="ri-upload-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Import'); ?>"></i>
                                    </a>
                                </div>
                                <div class="ta-icons-in">
                                    <a href="javascript:void(0);">
                                        <i class="ri-download-line" data-toggle="tooltip" data-placement="top" title="<?= _e('Download'); ?>"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php foreach ($translations as $path_key => $path) : ?>
    <?php
    $modal_key = "translationModal{$path_key}";

    $trs_key = "{$path_key}-core";
    $trs_item = array_value($trs_array, $trs_key);
    $trs_logs = array_value($trs_item, 'logs', array()); ?>
    <div class="modal modal-translation-logs fade" id="<?= $modal_key; ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <?= array_value($path, 'name'); ?>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <?php if ($trs_logs) : ?>
                        <div class="modal-logs-block">
                            <?php foreach ($trs_logs as $trs_log) : ?>
                                <p><?= $trs_log; ?></p>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <p><?= _e('Logs not found'); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
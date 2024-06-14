<?php
$this->title = _e('Translation languages: {name}', [
    'name' => $translations['name'],
]);

$this->page_title = _e('Translation languages: {name}', [
    'name' => $translations['name'],
]);

$this->breadcrumb_title = _e('Languages');
$this->breadcrumbs[] = ['label' => _e('Translations'), 'url' => $main_url];

$logs = array();
$trs_array = array();
$translations = array();
$formatter = \Yii::$app->formatter;

if ($model) {
    $logs = $model->logs;
    $translations = $model->translations;
}

if ($translations_list) {
    foreach ($translations_list as $item) {
        $trs_key = "{$item->path_key}-{$item->lang_key}";
        $trs_array[$trs_key] = $item->attributes;
    }
}

require('js-vars.php'); ?>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="thead-light">
                    <tr>
                        <th width="40%"><?= _e('Languages name') ?></th>
                        <th><?= _e('Progress'); ?></th>
                        <th><?= _e('Last change'); ?></th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($languages as $language) : ?>
                        <?php
                        $trs_key = "{$path_key}-{$language->lang_code}";
                        $trs_item = array_value($trs_array, $trs_key);
                        $trs_date = array_value($trs_item, 'updated_on');
                        $trs_items = array_value($trs_item, 'translations', array()); ?>
                        <tr>
                            <td>
                                <a href="<?= $main_url; ?>/edit?id=<?= $path_key; ?>&lang=<?= $language->lang_code; ?>"><?= $language->name; ?></a>
                            </td>
                            <td>
                                <?php
                                $percent = 0;
                                $org_count = count($translations);
                                $trs_count = count($trs_items);

                                if (($trs_count == $org_count) || $trs_count > $org_count) {
                                    $percent = 100;
                                } elseif ($org_count > 0 && $trs_count > 0 && $trs_count < $org_count) {
                                    $x = ($trs_count / $org_count);
                                    $percent = number_format($x, 2, '.', ',');
                                    $percent = (int) substr($percent, 2);
                                } ?>
                                <span>[<?= $trs_count; ?> / <?= $org_count; ?>] - <?= $percent; ?>%</span>
                            </td>
                            <td><?= $trs_date ? $formatter->asDate($trs_date, 'php:d/m/Y H:i:s') : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
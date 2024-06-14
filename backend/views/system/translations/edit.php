<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = _e('Edit translation: {name} - {lang_name}', [
    'name' => $system_translations['name'],
    'lang_name' => $language->name,
]);

$this->page_title = _e('Edit translation');

$this->breadcrumb_title = _e('Edit translation');
$this->breadcrumbs[] = ['label' => _e('Translations'), 'url' => $main_url];
$this->breadcrumbs[] = ['label' => _e('Languages'), 'url' => $main_url . '/list-languages?id=' . $path_key];

$logs = array();
$translations = array();

if ($model) {
    $logs = $model->logs;
    $translations = $model->translations;
}

if ($lang_model && $lang_model->translations) {
    foreach ($lang_model->translations as $translation_key => $translation_item) {
        if (isset($translations[$translation_key])) {
            $translations[$translation_key] = $translation_item;
        }
    }
}

require('js-vars.php');

$form = ActiveForm::begin([
    'options' => [
        'class' => 'full-form translation-form',
    ],
]); ?>

<style>
    .translation-origin-text {
        display: flex;
        display: -ms-flexbox;
        align-items: center;
    }

    .translation-origin-text>i {
        cursor: pointer;
        margin-left: 5px;
    }
</style>

<div class="card">
    <div class="card-body">
        <h4 class="mb-4">
            <?= "{$system_translations['name']} - {$language->name}"; ?>
        </h4>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="data-translations-table">
                <thead class="thead-light">
                    <tr>
                        <th width="50%"><?= _e('Source text') ?></th>
                        <th width="50%"><?= _e('Translation'); ?></th>
                    </tr>
                </thead>

                <tbody id="translations-tbody">
                    <?php if ($translations) : ?>
                        <?php foreach ($translations as $original => $item) : ?>
                            <tr>
                                <td>
                                    <?php $original_enc = \yii\helpers\Html::encode($original); ?>
                                    <div class="translation-origin-text">
                                        <span><?= $original_enc; ?></span>
                                        <i class="ri-file-copy-line" data-toggle="tooltip" data-placement="left" title="<?= _e('Copy text'); ?>" data-copy-text="<?= $original_enc; ?>"></i>
                                    </div>
                                </td>
                                <td>
                                    <span class="d-none"><?= $original; ?></span>
                                    <textarea data-translation-touch="<?= htmlentities($original); ?>" class="translation-input-text" onkeyup="adjustHeight(this)"><?= htmlentities($item); ?></textarea>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="2" class="text-center table-not-found">
                                <i class="ri-error-warning-line"></i>
                                <div class="h5">
                                    <?= _e('Translations not found'); ?>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($translations) : ?>
    <div class="d-none translations-input-list">
        <?php foreach ($translations as $original => $item) : ?>
            <input type="text" value="<?= htmlentities($item); ?>" name="translation[<?= htmlentities($original); ?>]" data-translation-id="<?= htmlentities($original); ?>" class="d-none">
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="mb-3 full-form-btns">
    <?php
    echo Html::submitButton('<i class="ri-check-line mr-1"></i> ' . _e('Save'), ['class' => 'btn btn-success waves-effect btn-with-icon']); ?>
</div>

<?php ActiveForm::end(); ?>

<script>
    function adjustHeight(element) {
        element.style.height = "32px";

        if (element.scrollHeight > 32) {
            element.style.height = element.scrollHeight + "px";
        }
    }
</script>

<?php
$this->registerJs(
    <<<JS
    $(document).ready(function () {
        $(document).on('click', '[data-copy-text]', function () {
            var text = $(this).attr('data-copy-text');
            var temp = $("<input>");

            $("body").append(temp);
            temp.val(text).select();
            document.execCommand("copy");
            temp.remove();

            $('.tooltip').fadeOut(300);
        });

        $(document).on('resize', '.translation-input-text', function () {
            adjustHeight(this);
        });

        var dataTable = $('#data-translations-table').DataTable({
            "lengthMenu": [20, 40, 60, 80, 100],
            "pageLength": 20,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Russian.json"
            },
            "initComplete": function(settings, json) {
                $('.translation-input-text').trigger('resize');
            }
        });

        $(document).on('change', '.translation-input-text', function () {
            dataTable.cell($(this).closest('td')).invalidate();
        });

        dataTable.on('draw.dt', function () {
            $('.translation-input-text').trigger('resize');
        });
    });
JS
);

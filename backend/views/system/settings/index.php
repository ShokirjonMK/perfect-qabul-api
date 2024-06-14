<?php

use backend\models\Currency;
use base\libs\Utils;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;

$this->title = _e('Settings');

// Begin form
$form = ActiveForm::begin(); ?>

<input type="hidden" name="settings_form" value="save">

<div class="card-top-links row">
    <div class="col-md-7">
        <div class="card-listed-links">
            <?php foreach ($settings_group as $settings_group_key => $settings_group_value) : ?>
                <a href="<?= set_query_var('group', $settings_group_key, $main_url); ?>" <?= $settings_group_value['active'] ? 'class="active"' : ''; ?>>
                    <?= $settings_group_value['name']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-md-5">
        <div class="card-listed-links-right">
            <?php
            $languages = admin_active_langs();
            $active_lang = input_get('lang');
            $active_lang_item = array_value($languages, $active_lang); ?>

            <?php if ($languages && count($languages) > 1) : ?>
                <div class="btn-group col-in">
                    <div class="dropdown lang-top-group-dropdown d-none d-sm-inline-block">
                        <button type="button" class="btn btn-outline-light waves-effect cl-button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <?php
                            if ($active_lang_item) {
                                echo '<img src="' . $active_lang_item['flag'] . '" alt="Flag" height="16">';
                            } else {
                                echo '<i class="ri-earth-line mr-1"></i>';
                                echo '<span class="align-middle">Global</span>';
                            } ?>
                        </button>

                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="<?= remove_query_var('lang', $main_url); ?>" class="dropdown-item notify-item">
                                <i class="ri-earth-line"></i>
                                <span class="align-middle"> <?= _e('Global') ?></span>
                            </a>
                            <?php foreach ($languages as $language) : ?>
                                <a href="<?= set_query_var('lang', $language['lang_code'], $main_url); ?>" class="dropdown-item notify-item">
                                    <img src="<?= $language['flag']; ?>" alt="user-image" width="18" height="12">
                                    <span class="align-middle"><?= $language['name']; ?></span>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <button type="submit" class="btn btn-info waves-effect">
                <?= _e('Save changes') ?>
            </button>
            <a href="<?= admin_url(); ?>" class="btn btn-secondary waves-effect">
                <?= _e('Close') ?>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <?php if ($model) : ?>
            <div class="row">
                <?php foreach ($model as $one) : ?>
                    <div class="col-md-6 form-group">
                        <?php
                        $settings_type = $one->settings_type;

                        if ($settings_type == 'textarea') {
                            echo settings_item_form_textarea_field($one, ['active_lang' => $active_lang_item]);
                        } elseif ($settings_type == 'file' || $settings_type == 'image') {
                            echo settings_item_form_file_field($one, $active_lang_item);
                        } elseif ($settings_type == 'yes/no') {
                            $array = array('0' => 'No', '1' => 'Yes');
                            echo settings_item_form_select_field($one, $array, ['active_lang' => $active_lang_item]);
                        } elseif ($settings_type == 'currency_dropdown') {
                            $array = Currency::getListToSettings();
                            echo settings_item_form_select_field($one, $array, ['active_lang' => $active_lang_item]);
                        } elseif ($settings_type == 'languages_dropdown') {
                            $languages = admin_active_langs();
                            $array = ArrayHelper::map($languages, 'lang_code', 'name');
                            echo settings_item_form_select_field($one, $array, ['active_lang' => $active_lang_item]);
                        } elseif ($settings_type == 'price_format_dropdown') {
                            $array = Utils::priceFormatTypes();
                            echo settings_item_form_select_field($one, $array, ['active_lang' => $active_lang_item]);
                        } elseif ($settings_type == 'locale_dropdown') {
                            $array = Utils::getLocaleList();
                            echo settings_item_form_select_field($one, $array, ['active_lang' => $active_lang_item]);
                        } elseif ($settings_type == 'timezone_dropdown') {
                            $array = Utils::getTimezoneList();
                            echo settings_item_form_select_field($one, $array, ['class' => 'form-control select2', 'active_lang' => $active_lang_item]);
                        } else {
                            echo settings_item_form_input_field($one, ['active_lang' => $active_lang_item]);
                        } ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
<?php

use backend\models\Menu;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$menu_items = array();
$translations = array();
$languages = admin_active_langs();
$current_language = admin_current_lang();
$langs_array = ArrayHelper::map($languages, 'lang_code', 'name');

// Languages
$lang = input_get('lang');

if (is_string($lang) && $lang) {
    $model->language = $lang;
}

if ($model->isNewRecord) {
    $model->sort = 0;
    $model->created_by = Yii::$app->user->id;
    $model->updated_by = Yii::$app->user->id;
} else {
    if (!is_null($model->name) && $model->name) {
        $translations = json_decode($model->name, true);

        if (isset($translations[$lang])) {
            $model->title = $translations[$lang];
        } elseif ($translations) {
            $model->title = array_values($translations)[0];
        }
    }

    $menu_items_where = array(
        'language' => $model->language,
        'group_key' => $model->group_key,
        'parent_id' => 0,
    );

    $menu_items = Menu::getMenuItems(['where' => $menu_items_where]);
}

// Begin form
$form = ActiveForm::begin([
    'options' => [
        'class' => 'full-form',
    ],
]); ?>

<div class="row">
    <div class="col-sm-8">
        <div class="card">
            <div class="card-body">
                <div class="form-group required-field">
                    <?= $form->field($model, 'title')->textInput(['required' => 'required', 'ep-bind-action' => 'title']) ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'description')->textarea(['style' => 'height: 118px;']) ?>
                </div>
            </div>
        </div>

        <div class="card" style="min-height: 300px;">
            <div class="card-body">
                <div class="menu-items-box-title">
                    <span class="h5 m-0">
                        <?= _e('Menu items') ?>
                    </span>
                    <div class="menu-items-box-add">
                        <span class="menu-items-box-add-b">
                            <i class="ri-add-circle-line"></i> <?= _e('Add item') ?>
                        </span>

                        <div class="menu-items-box-add-dropdown">
                            <button type="button" data-add-menu="link"> <?= _e('Link') ?></button>
                            <button type="button" data-add-menu="page"> <?= _e('Page') ?></button>
                            <button type="button" data-add-menu="post"> <?= _e('Post') ?></button>
                            <button type="button" data-add-menu="post_category"> <?= _e('Post category') ?></button>
                        </div>
                    </div>
                </div>
                <div class="menu-items-box">
                    <?php if ($menu_items) : ?>
                        <div class="menu-items-c menu-sortable menu-sortable-block">
                            <?php Menu::menuItemsForRender($menu_items, $model, $this); ?>
                        </div>
                        <div class="menu-items-notfound d-none"> <?= _e('This menu doesn’t have any items.') ?></div>
                    <?php else : ?>
                        <div class="menu-items-c menu-sortable menu-sortable-block"></div>
                        <div class="menu-items-notfound"> <?= _e('This menu doesn’t have any items.') ?></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-4">
        <div class="card">
            <div class="card-body">
                <div class="form-group required-field">
                    <?= $form->field($model, 'status')->dropDownList($menu->statusArray(), ['class' => 'form-control custom-select', 'required' => 'required']) ?>
                </div>

                <div class="form-group">
                    <?= $form->field($model, 'group_key')->textInput() ?>
                </div>

                <div class="form-group required-field">
                    <?= $form->field($model, 'sort')->textInput(['type' => 'number', 'required' => 'required']) ?>
                </div>
            </div>
        </div>

        <?php if ($languages && count($languages) > 1) : ?>
            <div class="card">
                <div class="card-body full-form-translations">
                    <div class="form-group required-field">
                        <?= $form->field($model, 'language')->dropDownList($langs_array, ['class' => 'form-control custom-select c-translation-select', 'required' => 'required']) ?>
                    </div>

                    <?php if (isset($translations) && $translations) : ?>
                        <div class="form-group">
                            <label for="brand_language">Translations</label>
                            <?php foreach ($languages as $language) : ?>
                                <?php
                                $translations_array = array();
                                $language_code = $language['lang_code'];

                                foreach ($translations as $translation_key => $translation_item) {
                                    $translations_array[$translation_key] = $translation_item;
                                } ?>
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" data-toggle="tooltip" data-placement="bottom" title="<?= $language['name']; ?>">
                                            <img src="<?= $language['flag']; ?>" alt="<?= $language_code; ?>" height="10" width="17">
                                        </span>
                                    </div>
                                    <?php if (isset($translations_array[$language_code])) : ?>
                                        <input type="text" class="form-control" placeholder="<?= $translations_array[$language_code]; ?>" disabled>
                                        <div class="input-group-append">
                                            <a href="<?= Url::current(['lang' => $language_code]); ?>" class="input-group-text">
                                                <i class="ri-edit-2-fill"></i>
                                            </a>
                                        </div>
                                    <?php else : ?>
                                        <input type="text" class="form-control" placeholder="No translation" disabled>
                                        <div class="input-group-append">
                                            <a href="<?= Url::current(['lang' => $language_code]); ?>" class="input-group-text">
                                                <i class="ri-add-fill"></i>
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else : ?>
                        <div class="form-group">
                            <label for="brand_language"><?= _e('Translations'); ?></label>
                            <?php foreach ($languages as $language) : ?>
                                <?php $language_code = $language['lang_code']; ?>
                                <div class="input-group mt-2">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" data-toggle="tooltip" data-placement="bottom" title="<?= $language['name']; ?>">
                                            <img src="<?= $language['flag']; ?>" alt="<?= $language_code; ?>" height="10" width="17">
                                        </span>
                                    </div>
                                    <input type="text" class="form-control" name="translations_title[<?= $language_code; ?>]" data-translation-code="<?= $language_code; ?>" placeholder="Title" required>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="mb-3 full-form-btns">
    <?php
    if ($model->isNewRecord) {
        echo Html::submitButton('<i class="ri-check-line mr-1"></i> ' . _e('Create & open'), ['class' => 'btn btn-success waves-effect btn-with-icon']);
        echo Html::submitButton('<i class="ri-add-circle-line mr-1"></i> ' . _e('Create & add another'), ['class' => 'btn btn-primary waves-effect btn-with-icon', 'name' => 'submit_button', 'value' => 'create_and_add_new']);
    } else {
        echo Html::submitButton('<i class="ri-check-line mr-1"></i> ' . _e('Save'), ['class' => 'btn btn-success waves-effect btn-with-icon']);
        echo Html::submitButton('<i class="ri-add-fill mr-1"></i> ' . _e('Save & add another'), ['class' => 'btn btn-primary waves-effect btn-with-icon', 'name' => 'submit_button', 'value' => 'create_and_add_new']);
    } ?>
</div>
<?php ActiveForm::end(); ?>


<div id="addMenuItemBox" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addMenuItemBoxLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title mt-0" id="addMenuItemBoxLabel"> <?= _e('New menu item') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-12 form-group">
                        <label> <?= _e('Search') ?></label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <label class="input-group-text" for="addMenuItemBoxSearch">
                                    <i class="ri-search-2-line"></i>
                                </label>
                            </div>
                            <input type="text" id="addMenuItemBoxSearch" class="form-control form-input">
                            <div id="addMenuItemBoxSearchClose">
                                <i class="ri-close-fill"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 col-12 form-group">
                        <div class="addMenuItemBoxResults">
                            <div class="addMenuItemBoxResultsIn"></div>
                        </div>
                        <div class="addMenuItemBoxPreloader">
                            <i class="ri-refresh-line fa-spin"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal"> <?= _e('Close') ?> </button>
                <button type="button" class="btn btn-primary waves-effect waves-light" id="addMenuItemBoxSubmit"> <?= _e('Add new') ?></button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div id="addMenuItemLink" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addMenuItemLinkLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form class="modal-content form-add-menu-item">
            <input type="hidden" name="ajax_action" value="add-menu-item">
            <input type="hidden" name="action_type" value="link">

            <div class="modal-header">
                <h5 class="modal-title mt-0" id="addMenuItemLinkLabel"> <?= _e('New menu item') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 col-12 form-group">
                        <label><?= _e('Title') ?></label>
                        <input type="text" name="name" class="form-control form-input" required>
                    </div>
                    <div class="col-md-12 col-12 form-group">
                        <label>URL</label>
                        <input type="text" name="link" class="form-control form-input" required>
                    </div>
                    <div class="col-md-6 col-12 form-group">
                        <label><?= _e('Image') ?></label>
                        <input type="text" name="image" class="form-control">
                    </div>
                    <div class="col-md-6 col-12 form-group">
                        <label><?= _e('Icon') ?></label>
                        <input type="text" name="icon" class="form-control">
                    </div>
                    <div class="col-md-6 col-12 form-group">
                        <label> <?= _e('Link target') ?></label>
                        <select name="link_target" class="form-control custom-select">
                            <option value="">-</option>
                            <option value="_blank">Blank</option>
                            <option value="_parent">Parent</option>
                            <option value="_top">Top</option>
                            <option value="_self">Self</option>
                        </select>
                    </div>
                    <div class="col-md-6 col-12 form-group">
                        <label> <?= _e('Menu type') ?></label>
                        <select name="menu_type" class="form-control custom-select" required>
                            <option value="mega-menu"> <?= _e('Mega menu') ?></option>
                            <option value="simple-menu" selected> <?= _e('Simple menu') ?></option>
                        </select>
                    </div>
                    <div class="col-md-6 col-12 form-group">
                        <label> <?= _e('Attributes') ?></label>
                        <input type="text" name="attributes" class="form-control form-input">
                    </div>
                    <div class="col-md-6 col-12 form-group">
                        <label> <?= _e('Class name') ?></label>
                        <input type="text" name="class_name" class="form-control form-input">
                    </div>
                    <div class="col-md-12 col-12 form-group">
                        <label> <?= _e('Snippet') ?></label>
                        <input type="text" name="snippet" class="form-control form-input">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal"><?= _e('Close') ?></button>
                <button type="submit" class="btn btn-primary waves-effect waves-light"> <?= _e('Add new') ?></button>
            </div>
        </form><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

</div>
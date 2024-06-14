<?php
$current_language = admin_current_lang();
$model->language = array_value($current_language, 'lang_code', 'en');

if (!is_null($model->name) && $model->name) {
    $lang = $model->language;
    $translations = json_decode($model->name, true);

    if (isset($translations[$lang])) {
        $model->title = $translations[$lang];
    } elseif ($translations) {
        $model->title = array_values($translations)[0];
    }
}

$this->title = _e('Edit menu: {title}', [
    'title' => $model->title,
]);

$this->page_title = _e('Edit menu: {title}', [
    'title' => '<span>' . $model->title . '</span>',
]);

$this->breadcrumb_title = _e('Edit menu');
$this->breadcrumbs[] = ['label' => _e('Menus'), 'url' => $main_url]; ?>

<div class="Menu-update">
    <?= $this->render('_form', [
        'menu' => $menu,
        'model' => $model,
    ]); ?>
</div>
<?php
$this->title = _e('New menu');
$this->page_title = _e('New menu') . ': ';
$this->breadcrumbs[] = ['label' => _e('Menus'), 'url' => $main_url]; ?>

<div class="Menu-create">
    <?= $this->render('_form', [
        'menu' => $menu,
        'model' => $model,
        'items' => $items,
    ]); ?>
</div>
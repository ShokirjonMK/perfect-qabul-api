<?php
/*
 * Available variables:
 * 
 * Queried object: $obj
 * Content info: $info
 * Content model: $content
 * Old attributes: $oldAttributes
 */

use common\models\Content;

$lang = get_current_lang();

$services = Content::find()
    ->alias('content')
    ->join('INNER JOIN', 'site_content_info info', 'content.id = info.content_id')
    ->where(['content.type' => 'service', 'content.status' => 1, 'content.deleted' => 0])
    ->andWhere(['info.language' => $lang])
    ->orderBy('info.title ASC')
    ->with('info')
    ->all();

$this->getPartial('header');

$this->getPartial('page-header', [
    'title' => $info->title,
]); ?>

<div class="page-section projects">
    <div class="container">
        <?php if ($services) : ?>
            <div class="row">
                <?php foreach ($services as $item) : ?>
                    <div class="col-md-4">
                        <?php theme_partial('services/item', ['item' => $item]) ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="items-not-found-block">
                <img src="<?= images_url('warning.svg'); ?>" width="100" alt="Warning">
                <strong><?= _e('Services not found.'); ?></strong>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= content_editor_sections($info->content_blocks); ?>

<?php $this->getPartial('footer'); ?>
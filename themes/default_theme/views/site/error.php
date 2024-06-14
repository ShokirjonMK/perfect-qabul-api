<?php
$this->getPartial('header');

$this->getPartial('page-header', [
    'title' => _e('404 Error'),
]); ?>

<div class="page-section">
    <div class="container text-center">
        <h1><?= _e('Oops :('); ?></h1>
        <p><?= _e('The page you are looking for does not exist!'); ?></p>
    </div>
</div>

<?php $this->getPartial('footer'); ?>
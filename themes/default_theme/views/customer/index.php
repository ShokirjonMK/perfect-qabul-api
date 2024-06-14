<?php
/*
 * Available variables:
 * 
 * Queried object: $obj
 * Segment customer: $customer
 * Segment profile: $profile
 * Old attributes: $oldAttributes
 */

$this->getPartial('header');

$this->getPartial('page-header', [
    'title' => 'User: ' . $customer->email,
]); ?>

<div class="page-section">
    <div class="container">
        <h1>Username: <?= $customer->username; ?></h1>
        <h4>Name: <?= $profile->name; ?></h4>
    </div>
</div>

<?php $this->getPartial('footer'); ?>
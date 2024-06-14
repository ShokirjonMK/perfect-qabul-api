<?php
/*
 * Available variables:
 * 
 * Queried object: $obj
 * Segment customer: $customer
 * Segment profile: $profile
 * Old attributes: $oldAttributes
 */

$this->getPartial('header'); ?>

<div class="container">
    <h3>Username: <?= $customer->username; ?></h3>
    <h4>Name: <?= $profile->name; ?></h4>
</div>

<?php $this->getPartial('footer'); ?>
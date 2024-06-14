<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <?= _e('© {year} {brand_name}. All rights reserved.', [
                    'year' => date('Y'),
                    'brand_name' => get_setting_value('brand_name', 'Ecommerce')
                ]); ?>
            </div>
            <div class="col-sm-6">
                <div class="text-sm-right d-none d-sm-block">
                    <?= CMF_VERSION_NAME; ?>. <?= _e('Version: {version_no}', ['version_no' => APP_VERSION]); ?>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Page loader -->
<div id="page-loader">
    <div class="page-loader-in">
        <img src="<?= images_url('preloader/02-black.gif'); ?>" alt="preloader" />
        <span><?= _e('Please wait...'); ?></span>
    </div>
</div><!-- /Page loader -->

<!-- Quick action modal -->
<div class="modal" id="quick-action-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form class="modal-content" id="quick-action-modal-form">
            <input type="hidden" name="ajax" value="quick-action" />
            <input type="hidden" name="type" id="quick-action-type" />
            <input type="hidden" name="id" id="quick-action-id" />

            <div class="modal-header">
                <h5 class="modal-title mt-0"><?= _e('Title'); ?></h5>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light waves-effect" data-dismiss="modal"><?= _e('Close'); ?></button>
                <button type="submit" class="btn btn-success waves-effect waves-light"><?= _e('Save changes'); ?></button>
            </div>
        </form>
    </div>
</div><!-- /Quick action modal -->

<script type="text/javascript">
    var success_alert_message = '';
    var error_alert_message = '';
</script>

<?php if (Yii::$app->session->hasFlash('success-alert')) : ?>
    <script type="text/javascript">
        success_alert_message = '<?= Yii::$app->session->getFlash('success-alert'); ?>';
    </script>
<?php endif; ?>

<?php if (Yii::$app->session->hasFlash('error-alert')) : ?>
    <script type="text/javascript">
        error_alert_message = '<?= Yii::$app->session->getFlash('error-alert'); ?>';
    </script>
<?php endif; ?>

<?php
$this->registerJs(
    <<<JS

$(document).ready(function() {
    if (success_alert_message != undefined && success_alert_message != '') {
        toastr.success(success_alert_message, trs.messages.success, {timeOut: 10000});
    }
    if (error_alert_message != undefined && error_alert_message != '') {
        toastr.error(error_alert_message, trs.messages.error, {timeOut: 10000});
    }
});

JS
); ?>
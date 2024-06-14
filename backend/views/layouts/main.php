<?php

use backend\assets\AppAsset;
use yii\helpers\Html;

AppAsset::register($this);
$this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="<?= theme_favicon_image(); ?>">
    <?php $this->registerCsrfMetaTags(); ?>
    <title><?= Html::encode($this->title); ?> &bull; <?= get_setting_value('brand_name', 'Ecommerce'); ?></title>
    <?php $this->head(); ?>

    <style>
        .custom-file-label::after {
            content: "<?= _e('Select file'); ?>";
        }
    </style>

    <script type="text/javascript">
        var site_url = '<?= admin_url(); ?>';
        var site_lang = '<?= admin_current_lang('lang_code'); ?>';
        var this_url = window.location.href;
        var images_url = '<?= images_url(); ?>';
        var ajax_error_msg = "<?= _e('An error occurred while processing your request. Please try again.'); ?>";
    </script>

    <?= \backend\widgets\ScriptsWidget::widget(); ?>
</head>

<body data-sidebar="dark" id="body-html">
    <?php $this->beginBody(); ?>

    <div id="preloader">
        <div id="preloader-in">
            <span></span>
            <span></span>
        </div>
    </div>

    <!-- Begin page -->
    <div id="layout-wrapper">
        <?= \backend\widgets\HeaderWidget::widget(); ?>

        <!-- Left Sidebar Start -->
        <?= \backend\widgets\SidebarWidget::widget(); ?>
        <!-- Left Sidebar End -->

        <!-- Start right Content here -->
        <div class="main-content">
            <div class="page-content">
                <div class="content-header">
                    <?= $this->render('breadcrumb.php'); ?>
                </div>

                <div class="container-fluid">
                    <?= $content ?>
                </div>
            </div>

            <?= \backend\widgets\FooterWidget::widget(); ?>
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <?php $this->endBody(); ?>

</body>

</html>
<?php $this->endPage(); ?>

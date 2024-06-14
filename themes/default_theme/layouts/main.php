<?php

use themes\default_theme\app\AppAsset;

AppAsset::register($this);

$default_favicon = $this->getAssetsUrl('images/favicon.ico');
$site_favicon = get_setting_value('site_favicon', $default_favicon);

$this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= get_current_lang(); ?>">

<head>
    <?php $this->metaTags(); ?>
    <?php $this->registerCsrfMetaTags(); ?>

    <meta name="author" content="UTAS | www.utas.uz">
    <meta name="version" content="<?= APP_VERSION; ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?= $site_favicon; ?>?version=1.0.0">

    <?php $this->head(); ?>

    <script type="text/javascript">
        var site_url = '<?= site_url(); ?>';
        var assets_url = '<?= assets_url(); ?>';
        var images_url = '<?= images_url(); ?>';
        var redirectTo = '';
        var ajax_error_msg = '<?= _e('An error occurred while processing your request. Please try again.'); ?>';

        var sdwidth, sdevice;
        var ua = window.navigator.userAgent;
        var msie = ua.indexOf("MSIE ");

        if (navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i)) {
            var uagent = 'mobile';
        } else {
            var uagent = 'desktop';
        }
    </script>
</head>

<body <?= $this->getBodyClass('main-layout inner_page'); ?>>
    <!-- loader  -->
    <div class="loader_bg">
        <div class="loader"><img src="<?= $this->getAssetsUrl('theme/images/loading.gif'); ?>" alt="preloader" /></div>
    </div>
    <!-- end loader -->

    <?php $this->beginBody(); ?>
    <?= $content; ?>
    <?php $this->initScripts(); ?>
    <?php $this->endBody(); ?>
</body>

</html>
<?php $this->endPage(); ?>
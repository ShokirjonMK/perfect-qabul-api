<?php

use backend\assets\LoginAsset;
use yii\helpers\Html;

LoginAsset::register($this);

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
</head>

<body class="auth-body-bg">
    <?php $this->beginBody(); ?>

    <?php
    $flash = Yii::$app->session->getAllFlashes();

    if (!empty($flash)) :
        foreach ($flash as $type => $message) :
            $js = <<<JS
        Swal.fire({
          type: "{$type}",
          title: "{$message}",
        })
JS;
            $this->registerJs($js, \yii\web\View::POS_LOAD);
        endforeach;
    endif; ?>

    <?= $content ?>

    <?php $this->endBody(); ?>
</body>

</html>
<?php $this->endPage(); ?>
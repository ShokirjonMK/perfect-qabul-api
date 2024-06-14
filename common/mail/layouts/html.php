<?php

use yii\helpers\Html;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
    <table bgcolor="#f2f2f2" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse;font: 15px/15px 'Proxima Nova','Calibri','Helvetica',Arial,sans-serif;line-height: 1.5;">
        <tbody>
            <tr>
                <td style="border-collapse:collapse;">
                    <table border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse;" align="center">
                        <tbody>
                            <tr>
                                <td height="30"></td>
                            </tr>
                            <tr>
                                <td style="text-align: center;">
                                    <a href="<?= site_url(); ?>">
                                        <img src="<?= images_url('logo/logo.png'); ?>" height="50" alt="logo">
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td height="20"></td>
                            </tr>
                        </tbody>
                    </table>

                    <?php $this->beginBody() ?>
                    <?= $content ?>
                    <?php $this->endBody() ?>

                    <table border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse;text-align:center;" align="center">
                        <tbody>
                            <tr>

                            </tr>
                            <tr>
                                <td height="20"></td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
</body>

</html>
<?php $this->endPage() ?>
<?php

use base\Url;

$name = 'Unknown';
$url = Url::account('activation', ['email' => $user->email, 'token' => $user->verification_token]);

if (isset($profile->name)) {
    $name = $profile->name;
} ?>
<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse;" align="center">
    <tbody>
        <tr>
            <td width="30"></td>
            <td width="640" height="20"></td>
            <td width="30"></td>
        </tr>
        <tr>
            <td width="30"></td>
            <td width="640" style="text-align: center;">
                <h2 style="padding:0;margin: 0 0 15px 0;">Активация аккаунта</h2>

                <p style="padding:0;margin:0 0 15px 0;">
                    Здравствуйте <?= $name; ?>,
                    <br>
                    Регистрация успешно завершена.
                </p>

                <p style="padding:0;margin:0 0 15px 0;">
                    Чтобы активировать свою учетную запись, нажмите следующую кнопку, чтобы подтвердить свой адрес электронной почты:
                </p>

                <p style="padding:0;margin:0 0 15px 0;">
                    <a href="<?= $url; ?>" style="display: inline-block;padding: 9px 20px;background: #007494;border: none;border-radius: 10px;color: #fff;font-size: 14px;line-height: 1;text-decoration: none;" target="_blank">
                        Подтвердить адрес электронной почты
                    </a>
                </p>

                <p style="font-size: 12px;padding:0;margin: 0;">
                    <em>Если вы не регистрировались на сайте www.hopme.shop, не обращайте внимания на это письмо.</em>
                </p>
            </td>
            <td width="30"></td>
        </tr>
        <tr>
            <td width="30"></td>
            <td width="640" height="25"></td>
            <td width="30"></td>
        </tr>
    </tbody>
</table>
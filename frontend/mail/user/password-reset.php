<?php

use base\Url;

$url = Url::account('recovery', ['email' => $user->email, 'token' => $user->password_reset_token]); ?>
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
                <h2 style="padding:0;margin: 0 0 15px 0;">Сброс пароля</h2>

                <p style="padding:0;margin:0 0 15px 0;">
                    Если вы забыли свой пароль и хотите его сбросить,
                    <br>
                    используйте ссылку ниже, чтобы начать.
                </p>

                <p style="padding:0;margin:0 0 15px 0;">
                    <a href="<?= $url; ?>" style="display: inline-block;padding: 9px 20px;background: #007494;border: none;border-radius: 10px;color: #fff;font-size: 14px;line-height: 1;text-decoration: none;" target="_blank">
                        Изменить пароль
                    </a>
                </p>

                <p style="font-size: 12px;padding:0;margin: 0;">
                    <em>Если вы не просили восстановить пароль, не обращайте внимания на это письмо.</em>
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
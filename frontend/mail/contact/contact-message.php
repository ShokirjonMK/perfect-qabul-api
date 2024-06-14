<table bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse:collapse;" align="center">
    <tbody>
        <tr>
            <td width="30"></td>
            <td width="640" height="20"></td>
            <td width="30"></td>
        </tr>
        <tr>
            <td width="30"></td>
            <td width="640" style="text-align: left;">
                <h2 style="padding:0;margin: 0 0 15px 0;">Contact form message</h2>

                <p style="padding:0;margin:0 0 15px 0;">
                    <b>Fullname:</b> <?= $fullname; ?>
                    <br>
                    <b>Email:</b> <?= $email; ?>
                    <br>
                    <b>Phone number:</b> <?= $phone; ?>
                    <br>
                    <b>Subject:</b> <?= $subject; ?>
                </p>

                <p style="padding:0;margin:0;">
                    <b>Message:</b>
                    <br>
                    <?= $message; ?>
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
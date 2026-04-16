<?php
/**
 * Email view: contact form message forwarded to admin / shop owner.
 *
 * Variables (injected via extract()):
 *   string $name     Sender's name
 *   string $email    Sender's email address
 *   string $phone    Sender's phone number
 *   string $message  Message body
 */
?>
<p style="margin:0 0 12px;font-family:Helvetica,Arial,sans-serif;font-size:14px;color:#444444;">
    A visitor has sent a message via the contact form:
</p>

<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="border-collapse:collapse;margin:0 0 20px;">
    <tbody>
        <tr>
            <td width="120"
                style="padding:8px 12px 8px 0;font-family:Helvetica,Arial,sans-serif;font-size:13px;
                       font-weight:bold;color:#555555;vertical-align:top;border-bottom:1px solid #f0f0f0;">
                Name
            </td>
            <td style="padding:8px 0;font-family:Helvetica,Arial,sans-serif;font-size:13px;color:#444444;
                       vertical-align:top;border-bottom:1px solid #f0f0f0;">
                <?= htmlspecialchars($name) ?>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 12px 8px 0;font-family:Helvetica,Arial,sans-serif;font-size:13px;
                       font-weight:bold;color:#555555;vertical-align:top;border-bottom:1px solid #f0f0f0;">
                Email
            </td>
            <td style="padding:8px 0;font-family:Helvetica,Arial,sans-serif;font-size:13px;color:#444444;
                       vertical-align:top;border-bottom:1px solid #f0f0f0;">
                <a href="mailto:<?= htmlspecialchars($email) ?>"
                   style="color:#E87169;text-decoration:none;">
                    <?= htmlspecialchars($email) ?>
                </a>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 12px 8px 0;font-family:Helvetica,Arial,sans-serif;font-size:13px;
                       font-weight:bold;color:#555555;vertical-align:top;border-bottom:1px solid #f0f0f0;">
                Phone
            </td>
            <td style="padding:8px 0;font-family:Helvetica,Arial,sans-serif;font-size:13px;color:#444444;
                       vertical-align:top;border-bottom:1px solid #f0f0f0;">
                <?= htmlspecialchars($phone) ?>
            </td>
        </tr>
        <tr>
            <td style="padding:8px 12px 8px 0;font-family:Helvetica,Arial,sans-serif;font-size:13px;
                       font-weight:bold;color:#555555;vertical-align:top;">
                Message
            </td>
            <td style="padding:8px 0;font-family:Helvetica,Arial,sans-serif;font-size:13px;color:#444444;
                       vertical-align:top;">
                <?= nl2br(htmlspecialchars($message)) ?>
            </td>
        </tr>
    </tbody>
</table>

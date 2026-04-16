<?php
/**
 * Email view: password-reset request.
 *
 * Variables (injected via extract()):
 *   string $name        Recipient's first name
 *   string $resetUrl    Full URL to the password-reset page (with token)
 *   string $appBusinessName  App brand name (auto-injected by renderEmail)
 */
?>
<p style="margin:0 0 16px;font-family:Helvetica,Arial,sans-serif;font-size:16px;color:#444444;">
    Dear <?= htmlspecialchars(ucfirst($name)) ?>,
</p>

<p style="margin:0 0 16px;font-family:Helvetica,Arial,sans-serif;font-size:14px;color:#444444;line-height:1.6;">
    You requested to reset your password for <strong><?= htmlspecialchars($appBusinessName) ?></strong>.
    Click the button below to choose a new password.
</p>

<!-- CTA button -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:24px 0;">
    <tbody>
        <tr>
            <td align="center">
                <a href="<?= htmlspecialchars($resetUrl) ?>"
                   style="display:inline-block;background-color:#E87169;color:#FFFFFF;
                          font-family:Helvetica,Arial,sans-serif;font-size:15px;font-weight:bold;
                          text-decoration:none;padding:12px 30px;border-radius:3px;">
                    Reset My Password
                </a>
            </td>
        </tr>
    </tbody>
</table>

<p style="margin:0 0 8px;font-family:Helvetica,Arial,sans-serif;font-size:13px;color:#888888;line-height:1.6;">
    If the button above does not work, copy and paste the following link into your browser:
</p>
<p style="margin:0 0 20px;font-family:Helvetica,Arial,sans-serif;font-size:12px;word-break:break-all;">
    <a href="<?= htmlspecialchars($resetUrl) ?>"
       style="color:#E87169;text-decoration:none;">
        <?= htmlspecialchars($resetUrl) ?>
    </a>
</p>

<p style="margin:0;font-family:Helvetica,Arial,sans-serif;font-size:13px;color:#888888;line-height:1.6;">
    If you did not request a password reset, you can safely ignore this email.
    Your password will remain unchanged.
</p>

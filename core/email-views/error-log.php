<?php
/**
 * Email view: error-log alert sent to admin.
 *
 * Variables (injected via extract()):
 *   string $message   Error message / log entry
 *   string $logsUrl   Full URL to the admin logs page
 */
?>
<p style="margin:0 0 16px;font-family:Helvetica,Arial,sans-serif;font-size:14px;color:#444444;line-height:1.6;">
    An error has been logged on the live site. Please review the log below and take any
    necessary action.
</p>

<!-- Error message block -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
    <tbody>
        <tr>
            <td style="background-color:#fff3f3;border-left:4px solid #E87169;padding:14px 16px;
                       font-family:Helvetica,Arial,sans-serif;font-size:13px;color:#555555;line-height:1.6;">
                <?= nl2br(htmlspecialchars($message)) ?>
            </td>
        </tr>
    </tbody>
</table>

<!-- CTA button -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;">
    <tbody>
        <tr>
            <td align="center">
                <a href="<?= htmlspecialchars($logsUrl) ?>"
                   style="display:inline-block;background-color:#333333;color:#FFFFFF;
                          font-family:Helvetica,Arial,sans-serif;font-size:14px;font-weight:bold;
                          text-decoration:none;padding:11px 26px;border-radius:3px;">
                    View Full Logs
                </a>
            </td>
        </tr>
    </tbody>
</table>

<p style="margin:0;font-family:Helvetica,Arial,sans-serif;font-size:12px;color:#999999;">
    Or copy this link into your browser:
    <a href="<?= htmlspecialchars($logsUrl) ?>"
       style="color:#E87169;text-decoration:none;word-break:break-all;">
        <?= htmlspecialchars($logsUrl) ?>
    </a>
</p>

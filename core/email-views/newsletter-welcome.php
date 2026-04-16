<?php
/**
 * Email view: newsletter welcome message (first newsletter after subscription).
 *
 * Variables (injected via extract()):
 *   string $subscriber_name   Subscriber's name
 *   string $message           Newsletter body text
 *   string $image             Optional image filename (relative to EMAIL_IMAGE_DIR)
 *   string $imageCaption      Optional caption for the image
 *   string $appURL            Base URL (auto-injected by renderEmail)
 */
$imageDir = env('EMAIL_IMAGE_DIR', 'assets/images/email_images/');
?>
<p style="margin:0 0 16px;font-family:Helvetica,Arial,sans-serif;font-size:16px;color:#444444;">
    Dear <?= htmlspecialchars(ucfirst($subscriber_name)) ?>,
</p>

<p style="margin:0 0 20px;font-family:Helvetica,Arial,sans-serif;font-size:14px;color:#444444;line-height:1.6;">
    <?= nl2br(htmlspecialchars($message)) ?>
</p>

<?php if (!empty($image)): ?>
<!-- Optional image + caption -->
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;">
    <tbody>
        <tr>
            <?php if (!empty($imageCaption)): ?>
            <td width="50%" style="vertical-align:top;padding-right:15px;">
                <img src="<?= htmlspecialchars(rtrim($appURL, '/') . '/' . ltrim($imageDir, '/') . $image) ?>"
                     alt="<?= htmlspecialchars($imageCaption) ?>"
                     width="100%"
                     style="display:block;border:0;max-width:250px;">
            </td>
            <td width="50%" style="vertical-align:top;">
                <p style="margin:0;font-family:Helvetica,Arial,sans-serif;font-size:14px;
                           color:#666666;line-height:1.6;">
                    <?= htmlspecialchars($imageCaption) ?>
                </p>
            </td>
            <?php else: ?>
            <td>
                <img src="<?= htmlspecialchars(rtrim($appURL, '/') . '/' . ltrim($imageDir, '/') . $image) ?>"
                     alt=""
                     width="100%"
                     style="display:block;border:0;">
            </td>
            <?php endif; ?>
        </tr>
    </tbody>
</table>
<?php endif; ?>

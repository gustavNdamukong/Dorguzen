<?php
/**
 * App override for the newsletter welcome email view.
 *
 * Variables injected via renderEmail() extract():
 *   string $subscriber_name   Subscriber's first name
 *   string $message           Welcome body (may contain HTML, includes unsubscribe link)
 *   string $image             Relative path to image, e.g. assets/images/newsletters/x.jpg
 *   string $appURL            Base URL auto-injected by renderEmail()
 */
?>
<p style="margin:0 0 16px;font-family:Helvetica,Arial,sans-serif;font-size:18px;
           font-weight:bold;color:<?= htmlspecialchars($accentColour ?? '#e67e22', ENT_QUOTES) ?>;">
    Welcome message
</p>

<p style="margin:0 0 16px;font-family:Helvetica,Arial,sans-serif;font-size:16px;color:#444444;">
    Hi <strong><?= htmlspecialchars(ucfirst($subscriber_name ?? 'Subscriber')) ?></strong>,
</p>

<p style="margin:0 0 20px;font-family:Helvetica,Arial,sans-serif;font-size:14px;color:#444444;line-height:1.7;">
    Thank you for subscribing to the <strong><?= htmlspecialchars($appBusinessName ?? '') ?></strong> newsletter.
    We're thrilled to have you on board!
</p>

<?php if (!empty($image)): ?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;">
    <tbody><tr><td>
        <img src="<?= htmlspecialchars(rtrim($appURL, '/') . '/' . ltrim($image, '/')) ?>"
             alt="" width="100%"
             style="display:block;border:0;max-width:540px;">
    </td></tr></tbody>
</table>
<?php endif; ?>

<div style="font-family:Helvetica,Arial,sans-serif;font-size:14px;color:#444444;line-height:1.7;">
    <?= $message ?>
</div>

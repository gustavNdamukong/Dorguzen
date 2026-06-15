<?php
/**
 * App override for the regular newsletter email view.
 *
 * Variables injected via renderEmail() extract():
 *   string $subscriber_name   Subscriber's first name
 *   string $message           Newsletter body (may contain HTML)
 *   string $image             Relative path to image, e.g. assets/images/newsletters/x.jpg
 *   string $imageCaption      (unused — unsubscribe URL is appended to $message by the job)
 *   string $appURL            Base URL auto-injected by renderEmail()
 */
?>
<p style="margin:0 0 16px;font-family:Helvetica,Arial,sans-serif;font-size:16px;color:#444444;">
    Dear <?= htmlspecialchars(ucfirst($subscriber_name ?? 'Subscriber')) ?>,
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

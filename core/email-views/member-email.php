<?php
/**
 * Email view: new member email (account activation OR welcome).
 *
 * Used by both sendEmailActivationEmail() and sendWelcomeEmail().
 * The caller sets $heading in $data to distinguish the two:
 *   - Activation: heading = "Activate Your Account"
 *   - Welcome:    heading = "Welcome to {AppName}"
 *
 * Variables (injected via extract()):
 *   string $name     Recipient's first name
 *   string $message  Message body (may contain an HTML activation link)
 */
?>
<p style="margin:0 0 16px;font-family:Helvetica,Arial,sans-serif;font-size:16px;color:#444444;">
    Dear <?= htmlspecialchars(ucfirst($name)) ?>,
</p>

<p style="margin:0 0 20px;font-family:Helvetica,Arial,sans-serif;font-size:14px;color:#444444;line-height:1.6;">
    <?= $message /* may contain an HTML link — not escaped */ ?>
</p>

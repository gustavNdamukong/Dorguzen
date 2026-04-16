<?php
/**
 * Default HTML email layout.
 *
 * This is a standalone PHP template — NOT a DGZ_Layout subclass.
 * It is loaded by DGZ_Messenger::renderEmail() via output buffering.
 *
 * Variables available (injected via extract()):
 *   string $content          Rendered body content from the email-view file
 *   string $appBusinessName  App business/brand name (e.g. "Dorguzen")
 *   string $appSlogan        Short app tagline
 *   string $appURL           Base URL of the application
 *   string $appYear          Current four-digit year
 *   string $heading          Email-type heading shown below the brand name (may be empty)
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($appBusinessName) ?></title>
    <!--[if !mso]><!-->
    <style type="text/css">
        /* Reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; display: block; outline: none; }
        body { margin: 0; padding: 0; background-color: #f4f4f4; }
        /* Link colours for clients that support <style> */
        a { color: #E87169; text-decoration: none; }
        a:hover { text-decoration: underline; }
        /* Responsive */
        @media only screen and (max-width: 620px) {
            .email-wrapper { width: 100% !important; }
            .email-content { padding: 0 15px !important; }
        }
    </style>
    <!--<![endif]-->
</head>
<body bgcolor="#f4f4f4" style="margin:0;padding:0;background-color:#f4f4f4;">

<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#f4f4f4"
       style="background-color:#f4f4f4;margin:0;padding:0;">
    <tbody>
        <tr>
            <td align="center" style="padding:30px 10px;">

                <!-- ═══════════════════ HEADER ═══════════════════ -->
                <table class="email-wrapper" width="600" cellpadding="0" cellspacing="0" border="0"
                       style="border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <td bgcolor="#333333" style="background-color:#333333;">
                                <table width="540" cellpadding="0" cellspacing="0" border="0" align="center">
                                    <tbody>
                                        <tr><td height="20"></td></tr>
                                        <tr>
                                            <td align="center"
                                                style="color:#FFFFFF;font-family:Verdana,Geneva,sans-serif;
                                                       font-size:42px;line-height:1.3;letter-spacing:-1px;">
                                                <?= htmlspecialchars($appBusinessName) ?>
                                            </td>
                                        </tr>
                                        <?php if (!empty($heading)): ?>
                                        <tr>
                                            <td align="center"
                                                style="color:#AAAAAA;font-family:Helvetica,Arial,sans-serif;
                                                       font-size:16px;line-height:1.5;padding-top:4px;">
                                                <?= htmlspecialchars($heading) ?>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                        <tr><td height="20"></td></tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- ══════════════════ /HEADER ══════════════════ -->

                <!-- ═══════════════════ BODY ════════════════════ -->
                <table class="email-wrapper" width="600" cellpadding="0" cellspacing="0" border="0"
                       style="border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <td bgcolor="#FFFFFF" style="background-color:#FFFFFF;">
                                <table class="email-content" width="540" cellpadding="0" cellspacing="0"
                                       border="0" align="center">
                                    <tbody>
                                        <tr><td height="25"></td></tr>
                                        <tr>
                                            <td style="font-family:Helvetica,Arial,sans-serif;font-size:14px;
                                                       line-height:1.6;color:#444444;">
                                                <?= $content ?>
                                            </td>
                                        </tr>
                                        <tr><td height="25"></td></tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>

                        <!-- Accent bar -->
                        <tr>
                            <td bgcolor="#E87169" style="background-color:#E87169;">
                                <table width="540" cellpadding="0" cellspacing="0" border="0" align="center">
                                    <tbody>
                                        <tr><td height="12"></td></tr>
                                        <tr>
                                            <td align="center"
                                                style="color:#FFFFFF;font-family:Helvetica,Arial,sans-serif;
                                                       font-size:13px;line-height:1.5;">
                                                <?= htmlspecialchars($appBusinessName) ?>
                                                <?php if (!empty($appSlogan)): ?>
                                                    &mdash; <?= htmlspecialchars($appSlogan) ?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr><td height="12"></td></tr>
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- ══════════════════ /BODY ════════════════════ -->

                <!-- ══════════════════ FOOTER ═══════════════════ -->
                <table class="email-wrapper" width="600" cellpadding="0" cellspacing="0" border="0"
                       style="border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <td align="center"
                                style="padding:15px 0;color:#999999;font-family:Helvetica,Arial,sans-serif;
                                       font-size:12px;line-height:1.5;">
                                &copy; <?= htmlspecialchars($appYear) ?> <?= htmlspecialchars($appBusinessName) ?>.
                                All rights reserved.
                            </td>
                        </tr>
                    </tbody>
                </table>
                <!-- ═════════════════ /FOOTER ═══════════════════ -->

            </td>
        </tr>
    </tbody>
</table>

</body>
</html>

<?php

namespace Dorguzen\Core;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Dorguzen\Config\Config;
use Dorguzen\Models\Logs;

class DGZ_Messenger
{
    protected $_config;

    protected $_appEmail;

    protected $_appEmailOther;

    protected $_headerFrom;

    protected $_headerReplyTo;

    protected $_appName;

    protected $_appBusinessName;

    protected $_appSlogan;

    protected $_appURL;

    protected $_phpMailer;
    protected $_logger;



    public function __construct()
    {
        $config = container(Config::class);

        $this->_config          = $config;
        $this->_appEmail        = $config->getConfig()['appEmail'];
        $this->_appEmailOther   = $config->getConfig()['appEmailOther'];
        $this->_headerReplyTo   = $config->getConfig()['headerReply-To'];
        $this->_appName         = $config->getConfig()['appName'];
        $this->_appBusinessName = $config->getConfig()['appBusinessName'];
        $this->_appSlogan       = $config->getConfig()['appSlogan'];
        $this->_appURL          = $config->getConfig()['appURL'];

        $this->_headerFrom = ($config->getConfig()['live'] === 'true')
            ? $config->getConfig()['liveHeaderFrom']
            : $config->getConfig()['localHeaderFrom'];

        // ——— PHPMailer / SMTP ————————————————————————————————————————
        // All SMTP credentials come from .env so you can point at MailTrap
        // (or any mail-catcher) during development without touching code.
        //
        //   MAIL_HOST=sandbox.smtp.mailtrap.io
        //   MAIL_PORT=587
        //   MAIL_USERNAME=<mailtrap-user>
        //   MAIL_PASSWORD=<mailtrap-pass>
        //   MAIL_ENCRYPTION=tls
        //   MAIL_FROM_ADDRESS=noreply@yourapp.com
        //   MAIL_FROM_NAME="Your App"
        // ————————————————————————————————————————————————————————————
        $this->_logger    = container(Logs::class);
        $this->_phpMailer = new PHPMailer(true);
        $this->_phpMailer->isSMTP();
        $this->_phpMailer->Host       = env('MAIL_HOST', 'smtp.mailgun.org');
        $this->_phpMailer->SMTPAuth   = true;
        $this->_phpMailer->Username   = env('MAIL_USERNAME', '');
        $this->_phpMailer->Password   = env('MAIL_PASSWORD', '');
        $this->_phpMailer->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
        $this->_phpMailer->Port       = (int) env('MAIL_PORT', 587);
        $this->_phpMailer->Timeout    = (int) env('MAIL_TIMEOUT', 15);
        $this->_phpMailer->setFrom(
            env('MAIL_FROM_ADDRESS', $this->_appEmail),
            env('MAIL_FROM_NAME',    $this->_appBusinessName)
        );
    }


    // ═══════════════════════════════════════════════════════════════════
    // GENERIC SEND
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Send an email — with or without an HTML template.
     *
     * When $template is provided the email is rendered via renderEmail() and
     * sent as HTML. When omitted, $body is sent as plain text. This covers
     * every custom email need without requiring changes to this class.
     *
     * Usage — templated HTML:
     *   $messenger->sendEmail(
     *       toEmail:  'user@example.com',
     *       toName:   'Jane',
     *       subject:  'Your booking is confirmed',
     *       replyTo:  $visitorEmail,
     *       data:     ['heading' => 'Booking Confirmed', 'name' => $name, ...],
     *       template: 'booking-confirmation',
     *   );
     *
     * Usage — plain text:
     *   $messenger->sendEmail('user@example.com', 'Jane', 'Hello', 'Just a quick note.');
     *
     * @param  string $toEmail     Recipient email address.
     * @param  string $toName      Recipient display name.
     * @param  string $subject     Email subject line.
     * @param  string $body        Plain-text body (used when $template is empty).
     * @param  string $replyTo     Reply-To address (optional).
     * @param  string $replyToName Reply-To display name (optional).
     * @param  array  $data        Template variables passed to renderEmail() (optional).
     * @param  string $template    Email template name without .php (optional).
     * @return bool                true on success, false on failure.
     */
    public function sendEmail(
        string $toEmail,
        string $toName      = '',
        string $subject     = '',
        string $body        = '',
        string $replyTo     = '',
        string $replyToName = '',
        array  $data        = [],
        string $template    = '',
    ): bool {
        try {
            $this->_phpMailer->clearAddresses();
            $this->_phpMailer->addAddress($toEmail, $toName);

            if ($replyTo !== '') {
                $this->_phpMailer->addReplyTo($replyTo, $replyToName);
            }

            if ($template !== '') {
                $body   = $this->renderEmail($template, $data);
                $isHtml = true;
            } else {
                $isHtml = false;
            }

            $this->_phpMailer->isHTML($isHtml);
            $this->_phpMailer->Subject = $subject;
            $this->_phpMailer->Body    = $body;
            $this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            $this->_logger->log('Email failed to send from: sendEmail()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }


    // ═══════════════════════════════════════════════════════════════════
    // BUILT-IN FRAMEWORK EMAILS
    // ═══════════════════════════════════════════════════════════════════

    public function sendContactFormMsgToAdmin($name, $visitorEmail, $phone, $message)
    {
        try {
            $msg = $this->renderEmail('contact-form', [
                'heading' => 'Contact Form Message',
                'name'    => $name,
                'email'   => $visitorEmail,
                'phone'   => $phone,
                'message' => $message,
            ]);
            $this->_phpMailer->addAddress($this->_appEmail, 'Admin');
            $this->_phpMailer->addReplyTo($visitorEmail);
            $this->_phpMailer->isHTML(true);
            $this->_phpMailer->Subject = "From website contact form";
            $this->_phpMailer->Body    = $msg;
            $this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            $this->_logger->log('Email failed to send from: sendContactFormMsgToAdmin()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }


    public function sendNewsletterWelcomeMsg(
        $subscriber_name,
        $email,
        $newsletter_heading,
        $newsletter_subject,
        $newsletter_message,
        $newsletter_image,
        $newsletter_image_caption,
        string $template = 'newsletter-welcome')
    {
        try {
            $msg = $this->renderEmail($template, [
                'heading'         => $newsletter_heading,
                'subscriber_name' => $subscriber_name,
                'message'         => $newsletter_message,
                'image'           => $newsletter_image,
                'imageCaption'    => $newsletter_image_caption,
            ]);

            $this->_phpMailer->clearAddresses();
            $this->_phpMailer->addAddress($email);
            $this->_phpMailer->isHTML(true);
            $this->_phpMailer->Subject = $newsletter_subject;
            $this->_phpMailer->Body    = $msg;
            $this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            $this->_logger->log('Email failed to send from: sendNewsletterWelcomeMsg()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }


    public function sendNewsletterMsg(
        $subscriber_name,
        $email,
        $newsletter_heading,
        $newsletter_subject,
        $newsletter_message,
        $newsletter_image,
        $newsletter_image_caption,
        string $template = 'newsletter')
    {
        try {
            $msg = $this->renderEmail($template, [
                'heading'         => $newsletter_heading,
                'subscriber_name' => $subscriber_name,
                'message'         => $newsletter_message,
                'image'           => $newsletter_image,
                'imageCaption'    => $newsletter_image_caption,
            ]);

            $this->_phpMailer->clearAddresses();
            $this->_phpMailer->addAddress($email);
            $this->_phpMailer->isHTML(true);
            $this->_phpMailer->Subject = $newsletter_subject;
            $this->_phpMailer->Body    = $msg;
            $this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            $this->_logger->log('Email failed to send from: sendNewsletterMsg()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }


    public function sendEmailActivationEmail($name, $email, $subject, $message)
    {
        try {
            $msg = $this->renderEmail('member-email', [
                'heading' => 'Activate Your Account',
                'name'    => $name,
                'message' => $message,
            ]);
            $this->_phpMailer->addAddress($email);
            $this->_phpMailer->isHTML(true);
            $this->_phpMailer->Subject = $subject;
            $this->_phpMailer->Body    = $msg;
            $this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            $this->_logger->log('Email failed to send from: sendEmailActivationEmail()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }


    public function sendWelcomeEmail($name, $email, $subject, $message)
    {
        try {
            $msg = $this->renderEmail('member-email', [
                'heading' => 'Welcome to ' . $this->_appBusinessName,
                'name'    => $name,
                'message' => $message,
            ]);
            $this->_phpMailer->addAddress($email);
            $this->_phpMailer->isHTML(true);
            $this->_phpMailer->Subject = $subject;
            $this->_phpMailer->Body    = $msg;
            $this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            $this->_logger->log('Email failed to send from: sendWelcomeEmail()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }


    public function sendPasswordResetEmail($email, $firstname, $resetCode)
    {
        $subject = "Reset your password at " . $this->_appBusinessName;

        try {
            $msg = $this->renderEmail('password-reset', [
                'heading'  => 'Reset Your Password',
                'name'     => $firstname,
                'resetUrl' => $this->_config->getHomePage() . '/auth/reset?em=' . $resetCode,
            ]);
            $this->_phpMailer->addAddress($email);
            $this->_phpMailer->isHTML(true);
            $this->_phpMailer->Subject = $subject;
            $this->_phpMailer->Body    = $msg;
            $this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            $this->_logger->log('Email failed to send from: sendPasswordResetEmail()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }


    public function sendErrorLogMsgToAdmin($message)
    {
        $subject = "An error has occurred on live and has been logged";

        try {
            $msg = $this->renderEmail('error-log', [
                'heading' => 'Error Alert',
                'message' => $message,
                'logsUrl' => $this->_config->getHomePage() . '/admin/log',
            ]);
            $this->_phpMailer->addAddress($this->_appEmail);
            $this->_phpMailer->isHTML(true);
            $this->_phpMailer->Subject = $subject;
            $this->_phpMailer->Body    = $msg;
            $this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            $this->_logger->log('Email failed to send from: sendErrorLogMsgToAdmin()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }


    // ═══════════════════════════════════════════════════════════════════
    // EMAIL RENDERING
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Render a complete HTML email string from a view + layout.
     *
     * View resolution order (first match wins):
     *   1. views/emails/{$view}.php          — developer override
     *   2. core/email-views/{$view}.php      — framework default
     *
     * Layout file: layouts/email/{$layout}EmailLayout.php
     *
     * The method auto-injects app-level context into $data so that view
     * files and the layout always have access to $appName, $appBusinessName,
     * $appSlogan, $appURL, $appYear, and $heading (defaults to '').
     *
     * @param  string $view   View filename without .php (e.g. 'contact-form')
     * @param  array  $data   Template variables (merged with auto-injected context)
     * @param  string $layout Layout name prefix in layouts/email/ (default: 'default')
     * @return string         Complete HTML email ready to pass to PHPMailer->Body
     */
    private function renderEmail(string $view, array $data, string $layout = 'default'): string
    {
        $data = array_merge([
            'appName'         => $this->_appName,
            'appBusinessName' => $this->_appBusinessName,
            'appSlogan'       => $this->_appSlogan,
            'appURL'          => $this->_appURL,
            'appYear'         => date('Y'),
            'heading'         => '',
            'accentColour'    => $this->_config->getAppColorTheme() ?: '#E87169',
        ], $data);

        $appView  = __DIR__ . "/../views/emails/{$view}.php";
        $coreView = __DIR__ . "/email-views/{$view}.php";
        $viewPath = file_exists($appView) ? $appView : $coreView;

        extract($data);
        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        $layoutPath = __DIR__ . "/../layouts/email/{$layout}EmailLayout.php";
        ob_start();
        include $layoutPath;
        return ob_get_clean();
    }


    // ═══════════════════════════════════════════════════════════════════
    // RAW HTML SEND
    // ═══════════════════════════════════════════════════════════════════

    /**
     * Send a pre-rendered HTML email body directly via PHPMailer.
     *
     * Use this when your calling code has already rendered the email template
     * and simply needs it delivered. Unlike sendEmail(), this does NOT call
     * renderEmail() — the HTML you pass is used as-is as the email body.
     *
     * @param  string $toEmail    Recipient email address.
     * @param  string $toName     Recipient display name.
     * @param  string $subject    Email subject line.
     * @param  string $htmlBody   Complete HTML string for the email body.
     * @return bool               true on success, false on failure.
     */
    public function sendHtml(string $toEmail, string $toName, string $subject, string $htmlBody): bool
    {
        try {
            $this->_phpMailer->clearAddresses();
            $this->_phpMailer->addAddress($toEmail, $toName);
            $this->_phpMailer->isHTML(true);
            $this->_phpMailer->Subject = $subject;
            $this->_phpMailer->Body    = $htmlBody;
            $this->_phpMailer->send();
            return true;
        } catch (\Exception $e) {
            $this->_logger->log('Email failed to send from: sendHtml()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }


}

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



    // DONE
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
            $this->_logger->log('The email: sendContactFormMsgToAdmin() failed to send', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }


    // DONE
    public function sendShopContactMsgToShopOwner($shopVisitorName, $shopVisitorEmail, $shopOwnerEmail, $phone, $message)
    {
        /*
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $shopOwnerEmail\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        $to = "$shopOwnerEmail";
        $subject = "Message from your Camerooncom Shop contact form";

        $msg = $this->sendContactFormMsgToAdminTemplate($shopVisitorName, $shopVisitorEmail, $phone, $message);

        // And send the email!
        $send = mail($to, $subject, $msg, $headers);
        if ($send)
        {
            return true;
        }
        else
        {
            return false;
        }*/

        //--------------------------
        try {
            $msg = $this->renderEmail('contact-form', [
                'heading' => 'Shop Contact Form Message',
                'name'    => $shopVisitorName,
                'email'   => $shopVisitorEmail,
                'phone'   => $phone,
                'message' => $message,
            ]);
    		$this->_phpMailer->addAddress($shopOwnerEmail, 'Shop owner');
            $this->_phpMailer->addReplyTo($shopVisitorEmail);
            $this->_phpMailer->isHTML(true);
    		$this->_phpMailer->Subject = "Message from your Camerooncom Shop contact form";
    		$this->_phpMailer->Body    = $msg;
    		$this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            // Log this error
            $this->_logger->log('Email failed to send from: sendShopContactMsgToShopOwner()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
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
        $newsletter_image_caption)
    {

        /*
        //----------------------------------------
        $sent = $messenger->sendNewsletterWelcomeMsg(
                    $emailData['subscriber_name'], 
                    $emailData['subscriber_email'], 
                    $emailData['newsletter_heading'],
                    $emailData['newsletter_subject'],
                    $emailData['newsletter_message'],
                    $emailData['newsletter_image'],
                    $emailData['newsletter_image_caption']
                );
        //----------------------------------------
         */
        //Get the newsletter data from the DB
        //$newsletterWelcome = new Newsletter();
        //$sql = "SELECT * FROM newsletter WHERE newsletter_name = '$letterName'";
        //$welcomeletterData = $newsletterWelcome->query($sql);

        //$subject = $welcomeletterData[0]['newsletter_subject'];

        /*
        // Add your "sending" email below, better to get this fron the config file
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $this->_headerReplyTo\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        // We'll set the email "to" address to the database record
        $to = "$email";

        //TODO: TEST THIS
        $msg = $this->sendNewsletterWelcomeMsgTemplate($welcomeletterData[0]['newsletter_heading'], $name, $welcomeletterData[0]['newsletter_message'], $welcomeletterData[0]['newsletter_image'], $welcomeletterData[0]['newsletter_image_caption']);

        // And send the email!
        $send = mail($to, $subject, $msg, $headers);
        if ($send)
        {
            return true;
        }
        else
        {
            return false;
        }*/
        //--------------------------
        try {
            $msg = $this->renderEmail('newsletter-welcome', [
                'heading'         => $newsletter_heading,
                'subscriber_name' => $subscriber_name,
                'message'         => $newsletter_message,
                'image'           => $newsletter_image,
                'imageCaption'    => $newsletter_image_caption,
            ]);

            // Clear addresses from previous loop iteration
            $this->_phpMailer->clearAddresses();

    		$this->_phpMailer->addAddress($email);
            $this->_phpMailer->isHTML(true);
    		$this->_phpMailer->Subject = $newsletter_subject;
    		$this->_phpMailer->Body    = $msg;
    		$this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            // Log this error
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
        $newsletter_image_caption)
    {
        

        //$newsletter = new Newsletter();
        //$sql = "SELECT * FROM newsletter WHERE newsletter_id = $newsletterId";
        //$newsletterData = $newsletter->query($sql);

        //$subject = $newsletterData[0]['newsletter_subject'];
        /*
        // Add your "sending" email below, better to get this fron the config file
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $this->_headerReplyTo\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        // We'll set the email "to" address to the database record
        $to = "$email";

        //TODO: TEST THIS
        $msg = $this->sendNewsletterMsgTemplate($newsletterData[0]['newsletter_heading'], $name, $newsletterData[0]['newsletter_message'], $newsletterData[0]['newsletter_image'], $newsletterData[0]['newsletter_image_caption']);

        // And send the email!
        $send = mail($to, $subject, $msg, $headers);
        if ($send)
        {
            return true;
        }
        else
        {
            return false;
        }*/
        //--------------------------
        try {
            $msg = $this->renderEmail('newsletter', [
                'heading'         => $newsletter_heading,
                'subscriber_name' => $subscriber_name,
                'message'         => $newsletter_message,
                'image'           => $newsletter_image,
                'imageCaption'    => $newsletter_image_caption,
            ]);

            // Clear addresses from previous loop iteration
            $this->_phpMailer->clearAddresses();

    		$this->_phpMailer->addAddress($email);
            $this->_phpMailer->isHTML(true);
    		$this->_phpMailer->Subject = $newsletter_subject;
    		$this->_phpMailer->Body    = $msg;
    		$this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            // Log this error
            $this->_logger->log('Email failed to send from: sendNewsletterMsg()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }



    // DONE
    public function sendEmailActivationEmail($name, $email, $subject, $message)
    {
        /*
        //prepare to send an email to the new user with a link to activate their account
        //Time to send a welcome email to the new member with an account activation code
        $to = "$email";

        // Add your "sending" email below, notice we are getting this from the config file
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $this->_headerReplyTo\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        $msg = $this->createNewMemberTemplate($name, $message);

        // And send the email!
        $send = mail($to, $subject, $msg, $headers);
        if ($send)
        {
            return true;
        }
        else
        {
            return false;
        }*/
        //--------------------------
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
            // Log this error
            $this->_logger->log('Email failed to send from: sendEmailActivationEmail()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }



    // DONE
    public function sendWelcomeEmail($name, $email, $subject, $message)
    {
        /*
        //prepare to send an email to the new user with a link to activate their account
        //Time to send a welcome email to the new member with an account activation code
        $to = "$email";

        // Add your "sending" email below, notice we are getting this from the config file
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $this->_headerReplyTo\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        $msg = $this->createNewMemberTemplate($name, $message);

        // And send the email!
        $send = mail($to, $subject, $msg, $headers);
        if ($send)
        {
            return true;
        }
        else
        {
            return false;
        }*/
        //--------------------------
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
            // Log this error
            $this->_logger->log('Email failed to send from: sendWelcomeEmail()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }




    // DONE
    public function sendPasswordResetEmail($email, $firstname, $resetCode)
    {
        $subject = "Reset your password at ".$this->_appBusinessName;

        /*
        $to = "$email";

        // Add your "sending" email below, notice we are getting this from the config file
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $this->_headerReplyTo\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        $msg = $this->passwordResetTemplate($firstname, $resetCode);

        // And send the email!
        $send = mail($to, $subject, $msg, $headers);

        if ($send)
        {
            return true;
        }
        else
        {
            return false;
        }*/
        //--------------------------
        try {
            $msg = $this->renderEmail('password-reset', [
                'heading'  => 'Reset Your Password',
                'name'     => $firstname,
                'resetUrl' => $this->_config->getHomePage() . 'auth/reset?em=' . $resetCode,
            ]);
    		$this->_phpMailer->addAddress($email);
            $this->_phpMailer->isHTML(true);
    		$this->_phpMailer->Subject = $subject;
    		$this->_phpMailer->Body    = $msg;
    		$this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            // Log this error
            $this->_logger->log('Email failed to send from: sendPasswordResetEmail()', "Mailer Error: {$this->_phpMailer->ErrorInfo}");
            return false;
        }
    }


    // DONE
    public function sendErrorLogMsgToAdmin($message)
    {
        // Add your "sending" email below, better to get this from the config file
        /*$headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $this->_headerReplyTo\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";*/

        // We'll set the email "to" address to the database record
        //$to = $email;
        $to = $this->_appEmail;
        $additionalReceiverEmail = $this->_appEmailOther;
        $subject = "An error has occurred on live and has been logged";

        /*$msg = $this->sendErrorLogMsgToAdminTemplate($message);

        // And send the email!
        $send = mail($to, $subject, $msg, $headers);
        if ($send)
        {
            return true;
        }
        else
        {
            return false;
        }*/
        //--------------------------
        try {
            $msg = $this->renderEmail('error-log', [
                'heading' => 'Error Alert',
                'message' => $message,
                'logsUrl' => $this->_config->getHomePage() . 'admin/log',
            ]);
    		$this->_phpMailer->addAddress($to);
            $this->_phpMailer->isHTML(true);
    		$this->_phpMailer->Subject = $subject;
    		$this->_phpMailer->Body    = $msg;
    		$this->_phpMailer->send();
            return true;
        } catch (Exception $e) {
            // Log this error
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
        // Merge messenger-level context (caller's $data keys take precedence)
        $data = array_merge([
            'appName'         => $this->_appName,
            'appBusinessName' => $this->_appBusinessName,
            'appSlogan'       => $this->_appSlogan,
            'appURL'          => $this->_appURL,
            'appYear'         => date('Y'),
            'heading'         => '',
        ], $data);

        // Resolve the content-view path
        $appView  = __DIR__ . "/../views/emails/{$view}.php";
        $coreView = __DIR__ . "/email-views/{$view}.php";
        $viewPath = file_exists($appView) ? $appView : $coreView;

        // Render the content view into $content
        extract($data);
        ob_start();
        include $viewPath;
        $content = ob_get_clean();

        // Wrap the content in the layout
        $layoutPath = __DIR__ . "/../layouts/email/{$layout}EmailLayout.php";
        ob_start();
        include $layoutPath;
        return ob_get_clean();
    }


}


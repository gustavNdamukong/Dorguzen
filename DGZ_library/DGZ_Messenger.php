<?php

namespace DGZ_library;

use configs\Config;

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



    public function __construct()
    {
        $config = new Config();

        $this->_config = $config;

        $this->_appEmail = $config->getConfig()['appEmail'];

        $this->_appEmailOther = $config->getConfig()['appEmailOther'];

        if ($config->getConfig()['live'] == false)
        {
            $this->_headerFrom = $config->getConfig()['localHeaderFrom'];
        }
        else
        {
            $this->_headerFrom = $config->getConfig()['liveHeaderFrom'];
        }

        $this->_headerReplyTo = $config->getConfig()['headerReply-To'];

        $this->_appName = $config->getConfig()['appName'];

        $this->_appBusinessName = $config->getConfig()['appBusinessName'];

        $this->_appSlogan = $config->getConfig()['appSlogan'];

        $this->_appURL = $config->getConfig()['appURL'];
    }




    public function sendContactFormMsgToAdmin($name, $visitorEmail, $phone, $message)
    {
        // Add your "sending" email below, better to get this from the config file
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $visitorEmail\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        // We'll set the email "to" address to the database record
        //$to = $email;
        $to = $this->_appEmail.','.$this->_appEmailOther;
        $subject = "Inquiry from your website contact form";

        $msg = $this->sendContactFormMsgToAdminTemplate($name, $visitorEmail, $phone, $message);

        // And send the email!
        $send = mail($to, $subject, $msg, $headers);
        if ($send)
        {
            return true;
        }
        else
        {
            return false;
        }

    }




    public function sendShopContactMsgToShopOwner($name, $shopOwnerEmail, $phone, $message)
    {
        // Add your "sending" email below, better to get this from the config file
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $shopOwnerEmail\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        // We'll set the email "to" address to the database record
        //$to = $email;
        $to = $this->_appEmail.','.$this->_appEmailOther;
        $subject = "Message from your Camerooncom Shop contact form";

        $msg = $this->sendContactFormMsgToAdminTemplate($name, $shopOwnerEmail, $phone, $message);

        // And send the email!
        $send = mail($to, $subject, $msg, $headers);
        if ($send)
        {
            return true;
        }
        else
        {
            return false;
        }

    }


    /**
     * TODO: This is for a newsletter (e-mail marketing) module to come
     */
    /*public function sendNewsletterWelcomeMsg($name, $email, $letterName)
    {
        //Get the newsletter data from the DB
        $newsletterWelcome = new Newsletter();
        $sql = "SELECT * FROM newsletter WHERE newsletter_name = '$letterName'";
        $welcomeletterData = $newsletterWelcome->query($sql);

        $subject = $welcomeletterData[0]['newsletter_subject'];

        // Add your "sending" email below, better to get this fron the config file
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $this->_headerReplyTo\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        // We'll set the email "to" address to the database record
        $to = "$email";

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
        }
    }




    public function sendNewsletterMsg($name, $email, $newsletterId)
    {
        $newsletter = new Newsletter();
        $sql = "SELECT * FROM newsletter WHERE newsletter_id = $newsletterId";
        $newsletterData = $newsletter->query($sql);

        $subject = $newsletterData[0]['newsletter_subject'];

        // Add your "sending" email below, better to get this fron the config file
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $this->_headerReplyTo\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        // We'll set the email "to" address to the database record
        $to = "$email";

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
        }

    }*/




    public function sendEmailActivationEmail($name, $email, $subject, $message)
    {
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
        }
    }




    public function sendWelcomeEmail($name, $email, $subject, $message)
    {
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
        }
    }





    public function sendPasswordResetEmail($email, $firstname, $resetCode)
    {
        $subject = "Reset your password at ".$this->_appBusinessName;

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
        }
    }


    public function sendErrorLogMsgToAdmin($message)
    {
        // Add your "sending" email below, better to get this from the config file
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $this->_headerReplyTo\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        // We'll set the email "to" address to the database record
        //$to = $email;
        $to = $this->_appEmail.','.$this->_appEmailOther;
        $subject = "An error has occurred on live and has been logged";

        $msg = $this->sendErrorLogMsgToAdminTemplate($message);

        // And send the email!
        $send = mail($to, $subject, $msg, $headers);
        if ($send)
        {
            return true;
        }
        else
        {
            return false;
        }
    }




    ############################################ EMAIL TEMPLATES #######################################################


    private function sendContactFormMsgToAdminTemplate($name, $email, $phone, $message)
    {
        //Determine if we are live or not in order to build any links with the right URLs
        if ($this->_config->getConfig()['live'])
        {
            $url = $this->_config->getConfig()['fileRootPathLive'];
        }
        else
        {
            $url = $this->_config->getConfig()['fileRootPathLocal'];
        }


        ######<!-- ==========================
        #####  FIRST SIMPLE EMAIL TEMPLATE WITH ONE IMAGE
        #######=========================== -->

        $msg = "
            <!DOCTYPE HTML>
		    <html class=\"no-js\" lang=\"en-gb\">
		    <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>

                    <style type='text/css'>
                        #heading {
                                 background-color: #b3d4fc; /*This makes that blue bg color*/
               font-family: Verdana, Geneva, sans-serif;
                font-size: 12px;
                text-align: center;
             }

           #imageBox {
                    float: right;
                    }
                    
           </style>
        
        
           <!--[if lt IE 7]>
          <style type='text/css'>
          #wrapper { height:100%; }
          </style>
          <![endif]-->
        
          <!--[if lt IE 8]>
          <link rel='stylesheet' href='css/ie.css'>
          <![endif]-->
        
        </head>
        <body>
             <div id='maincontent' class='column'> 
                  <br />
                  <h1 id='heading'>Message from website <?=$this->_appBusinessName?> form</h1>
                  <h3>From $name,</h3>
                  <h3>Their email address: $email,</h3>
                  <h3>Phone number: $phone,</h3>
                  <h3>Description of the job:</h3>
                  <p>$message</p>
                  <br />";

        $msg .= "              
            </div>
        </body>
        </html>";

        return $msg;

        ######<!-- ==========================
        #####  FIRST SIMPLE EMAIL TEMPLATE WITH ONE IMAGE - END
        #######=========================== -->

    }











    private function sendNewsletterWelcomeMsgTemplate($heading, $name, $message, $image = '', $imageCaption = '')
    {
        //Determine if we are live or not in order to build any links with the right URLs
        $app = new Config();
        if ($app->getConfig()['live'])
        {
            $url = $app->getConfig()['fileRootPathLive'];
        }
        else
        {
            $url = $app->getConfig()['fileRootPathLocal'];
        }


        ######<!-- ==========================
        #####  SECOND RICH EMAIL TEMPLATE WITH ONE IMAGE
        #######=========================== -->
        $msg = '
        <!DOCTYPE html>
<html>
<head>
    <!-- ==========================
    	Meta Tags
    =========================== -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ==========================
    	Title
        =========================== -->
    <title></title>
</head>
<body style="background-image:url('.$url.'/assets/images/pattern.png); margin:0; padding:0;">
	   
    <!-- ==========================
    	EMAIL TEMPLATE - START
    =========================== -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody>
            <tr>
                <td>
                	<!-- EMAIL HEADER - START -->
                    <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">
                        <tbody>
                            <tr>
                                <td bgcolor="#333333">
                                    <table width="570" cellpadding="0" cellspacing="0" border="0" align="center">
                                        <tbody>
                                            <tr>
                                            	<td width="100%" height="5"></td>
                                            </tr>
                                            <tr>
                                            	<td width="100%" align="center" style="color:#FFFFFF;font-family:Verdana,Geneva,sans-serif;font-size:50px;line-height:150%;">Camerooncom</td>
                                            </tr>
                                            <tr>
                                            	<td width="100%" align="center" style="color:#888888;font-family:Helvetica,Arial,sans-serif;font-size:18px;line-height:150%;">'.$heading.'</td>
                                            </tr>
                                            <tr>
                                            	<td width="100%" height="20"></td>
                                            </tr>
                                        </tbody>
                                	</table>
                            	</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- EMAIL HEADER - END -->
                    
                    <!-- EMAIL BODY - START -->
                    <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">
                        <tbody>
                            <tr>
                                <td bgcolor="#FFFFFF">
                                    <table width="570" cellpadding="0" cellspacing="0" border="0" align="center">
                                        <tbody>
                                            <tr>
                                            	<td height="15"></td>
                                            </tr>';
        $msg .= '<tr>
                                                <p></p>
                                                <h3>Dear '.$name.'</h3>
                                            	<td align="center">
                                                	<h3 style="color:#555555;font-family:Helvetica,Arial,sans-serif;font-size:24px;line-height:150%;margin-bottom:0">Welcome to our Newsletter</h3>
                                                    <p style="color:#999999;font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:150%;">'.$message.'</p>
                                                </td>
                                            </tr>';
        $msg .= '<tr>
                                            	<td height="30"></td>
                                            </tr>
                                            <tr>
                                            	<td height="1" bgcolor="#f0f0f0"></td>
                                            </tr>
                                            <tr>
                                            	<td height="30"></td>
                                            </tr>';
        if ($image != '') {
            $msg .= '
                                            
                                            <tr>
                                            	<td>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <table border="0" cellpadding="0" cellspacing="0" width="285">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="left">
                                                                                    <img src="'.$url.'/assets/images/email_images/'.$image.'" alt="" width="270">
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                                <td>
                                                                    <table border="0" cellpadding="0" cellspacing="0" width="285">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <h3 style="color:#666666;font-family:Helvetica,Arial,sans-serif;font-size:18px;line-height:150%;margin-bottom:0;">Image</h3>
                                                                                    <p style="color:#999999;font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:150%;">'.$imageCaption.'</p>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>';
        }

        $msg .=
            '<tr>
                                            	<td height="30"></td>
                                            </tr>
                                            <tr>
                                            	<td height="1" bgcolor="#f0f0f0"></td>
                                            </tr>
                                            <tr>
                                            	<td height="30"></td>
                                            </tr>';
        $msg .= '<tr>
                                            	<td height="15"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                             </tr>
                             <tr>
                                <td bgcolor="#E87169">
                                    <table width="570" cellpadding="0" cellspacing="0" border="0" align="center">
                                        <tbody>
                                            <tr>
                                            	<td height="15"></td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                    <p style="color:#FFFFFF;font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:150%;">CAMEROONCOM.COM Cameroon\'s Online Market Place</p>
                                                </td>
                                            </tr>
                                            <tr>
                                            	<td height="10"></td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                	<a href="#"><img src="'.$url.'/assets/icons/icon_facebook_circle_gray.png" width="48"></a>
                                                    <a href="#"><img src="'.$url.'/assets/icons/icon_twitter_circle_gray.png" width="48"></a>
                                                    <a href="#"><img src="'.$url.'/assets/icons/icon_google_circle_gray.png" width="48"></a>
                                                    <a href="#"><img src="'.$url.'/assets/icons/icon_instagram_circle_gray.png" width="48"></a>
                                                    <a href="#"><img src="'.$url.'/assets/icons/icon_linkedin_circle_gray.png" width="48"></a>
                                                    <a href="#"><img src="'.$url.'/assets/icons/icon_youtube_circle_gray.png" width="48"></a>
                                                </td>
                                            </tr>
                                            <tr>
                                            	<td height="25"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- EMAIL BODY - END -->
                    
                    <!-- EMAIL FOOTER - START -->
                    <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">
                        <tbody>
                            <tr>
                                <td>
                                    <table width="570" cellpadding="0" cellspacing="0" border="0" align="center">
                                        <tbody>
                                            <tr>
                                            	<td height="20"></td>
                                            </tr>
                                            <tr>
                                            	<td align="center">
                                                	<p style="color:#999999;font-family:Helvetica,Arial,sans-serif;font-size:14px;margin-top:0;">© CAMEROONCOM All right reserved. Designed by <a href="https://www.nolimitmedia.co.uk/" target="_blank" style="color:#E87169;text-decoration:none;">NoLimit Media.</a></p>
                                            	</td>
                                            </tr>
                                            <tr>
                                            	<td height="5"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- EMAIL FOOTER - END -->
                </td>
            </tr>
        </tbody>
    </table>   
</body>
</html>';

        return $msg;

        ######<!-- ==========================
        #####  SECOND RICH EMAIL TEMPLATE WITH ONE IMAGE - END
        #######=========================== -->

    }






    private function sendNewsletterMsgTemplate($heading, $name, $message, $image = '', $imageCaption = '')
    {
        //Determine if we are live or not in order to build any links with the right URLs
        $app = new Config();
        if ($app->getConfig()['live'])
        {
            $url = $app->getConfig()['fileRootPathLive'];
        }
        else
        {
            $url = $app->getConfig()['fileRootPathLocal'];
        }

        ######<!-- ==========================
        #####  SECOND RICH EMAIL TEMPLATE WITH ONE IMAGE
        #######=========================== -->
        $msg = '
        <!DOCTYPE html>
<html>
<head>
    <!-- ==========================
    	Meta Tags
    =========================== -->
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- ==========================
    	Title
        =========================== -->
    <title></title>
</head>
<body style="background-image:url('.$url.'/assets/images/pattern.png); margin:0; padding:0;">
	   
    <!-- ==========================
    	EMAIL TEMPLATE - START
    =========================== -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
        <tbody>
            <tr>
                <td>
                	<!-- EMAIL HEADER - START -->
                    <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">
                        <tbody>
                            <tr>
                                <td bgcolor="#333333">
                                    <table width="570" cellpadding="0" cellspacing="0" border="0" align="center">
                                        <tbody>
                                            <tr>
                                            	<td width="100%" height="5"></td>
                                            </tr>
                                            <tr>
                                            	<td width="100%" align="center" style="color:#FFFFFF;font-family:Verdana,Geneva,sans-serif;font-size:50px;line-height:150%;">Camerooncom</td>
                                            </tr>
                                            <tr>
                                            	<td width="100%" align="center" style="color:#888888;font-family:Helvetica,Arial,sans-serif;font-size:18px;line-height:150%;">'.$heading.'</td>
                                            </tr>
                                            <tr>
                                            	<td width="100%" height="20"></td>
                                            </tr>
                                        </tbody>
                                	</table>
                            	</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- EMAIL HEADER - END -->
                    
                    <!-- EMAIL BODY - START -->
                    <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">
                        <tbody>
                            <tr>
                                <td bgcolor="#FFFFFF">
                                    <table width="570" cellpadding="0" cellspacing="0" border="0" align="center">
                                        <tbody>
                                            <tr>
                                            	<td height="15"></td>
                                            </tr>';
        $msg .= '<tr>
                                                <p></p>
                                                <h3>Dear '.$name.'</h3>
                                            	<td align="center">
                                                	<h3 style="color:#555555;font-family:Helvetica,Arial,sans-serif;font-size:24px;line-height:150%;margin-bottom:0">The Camerooncom Newsletter</h3>
                                                    <p style="color:#999999;font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:150%;">'.$message.'</p>
                                                </td>
                                            </tr>';

        $msg .= '<tr>
                                            	<td height="30"></td>
                                            </tr>
                                            <tr>
                                            	<td height="1" bgcolor="#f0f0f0"></td>
                                            </tr>
                                            <tr>
                                            	<td height="30"></td>
                                            </tr>';
        if ($image != '') {
            $msg .= '
                                            
                                            <tr>
                                            	<td>
                                                    <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                                        <tbody>
                                                            <tr>
                                                                <td>
                                                                    <table border="0" cellpadding="0" cellspacing="0" width="285">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td align="left">
                                                                                    <img src="'.$url.'/assets/images/email_images/'.$image.'" alt="" width="270">
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                                <td>
                                                                    <table border="0" cellpadding="0" cellspacing="0" width="285">
                                                                        <tbody>
                                                                            <tr>
                                                                                <td>
                                                                                    <h3 style="color:#666666;font-family:Helvetica,Arial,sans-serif;font-size:18px;line-height:150%;margin-bottom:0;">Image</h3>
                                                                                    <p style="color:#999999;font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:150%;">'.$imageCaption.'</p>
                                                                                </td>
                                                                            </tr>
                                                                        </tbody>
                                                                    </table>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>';
        }

        $msg .=
            '<tr>
                                            	<td height="30"></td>
                                            </tr>
                                            <tr>
                                            	<td height="1" bgcolor="#f0f0f0"></td>
                                            </tr>
                                            <tr>
                                            	<td height="30"></td>
                                            </tr>';

        $msg .= '<tr>
                                            	<td height="15"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                             </tr>
                             <tr>
                                <td bgcolor="#E87169">
                                    <table width="570" cellpadding="0" cellspacing="0" border="0" align="center">
                                        <tbody>
                                            <tr>
                                            	<td height="15"></td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                    <p style="color:#FFFFFF;font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:150%;">CAMEROONCOM.COM Cameroon\'s Online Market Place</p>
                                                </td>
                                            </tr>
                                            <tr>
                                            	<td height="10"></td>
                                            </tr>
                                            <tr>
                                                <td align="center">
                                                	<a href="#"><img src="'.$url.'/assets/icons/icon_facebook_circle_gray.png" width="48"></a>
                                                    <a href="#"><img src="'.$url.'/assets/icons/icon_twitter_circle_gray.png" width="48"></a>
                                                    <a href="#"><img src="'.$url.'/assets/icons/icon_google_circle_gray.png" width="48"></a>
                                                    <a href="#"><img src="'.$url.'/assets/icons/icon_instagram_circle_gray.png" width="48"></a>
                                                    <a href="#"><img src="'.$url.'/assets/icons/icon_linkedin_circle_gray.png" width="48"></a>
                                                    <a href="#"><img src="'.$url.'/assets/icons/icon_youtube_circle_gray.png" width="48"></a>
                                                </td>
                                            </tr>
                                            <tr>
                                            	<td height="25"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- EMAIL BODY - END -->
                    
                    <!-- EMAIL FOOTER - START -->
                    <table width="600" cellpadding="0" cellspacing="0" border="0" align="center">
                        <tbody>
                            <tr>
                                <td>
                                    <table width="570" cellpadding="0" cellspacing="0" border="0" align="center">
                                        <tbody>
                                            <tr>
                                            	<td height="20"></td>
                                            </tr>
                                            <tr>
                                            	<td align="center">
                                                	<p style="color:#999999;font-family:Helvetica,Arial,sans-serif;font-size:14px;margin-top:0;">© CAMEROONCOM 2017 All right reserved. Designed by <a href="http://www.nolimitmedia.com/" target="_blank" style="color:#E87169;text-decoration:none;">NoLimit Media.</a></p>
                                            	</td>
                                            </tr>
                                            <tr>
                                            	<td height="5"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- EMAIL FOOTER - END -->
                </td>
            </tr>
        </tbody>
    </table>   
</body>
</html>';

        return $msg;

        ######<!-- ==========================
        #####  SECOND RICH EMAIL TEMPLATE WITH ONE IMAGE - END
        #######=========================== -->
    }







    private function createNewMemberTemplate($name, $message)
    {
        $name = ucfirst($name);
        $msg = "
            <!DOCTYPE HTML>
		    <html class=\"no - js\" lang=\"en - gb\">
		    <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>

                    <style type='text/css'>
                        #heading {
                                 background-color: #b3d4fc; /*This makes that blue bg color*/
               font-family: Verdana, Geneva, sans-serif;
                font-size: 12px;
                text-align: center;
             }

           #imageBox {
                    float: right;
                    }
                    
           </style>
        
        
           <!--[if lt IE 7]>
          <style type='text/css'>
          #wrapper { height:100%; }
          </style>
          <![endif]-->
        
          <!--[if lt IE 8]>
          <link rel='stylesheet' href='css/ie.css'>
          <![endif]-->
        
        </head>
        <body>
             <div id='maincontent' class='column'> 
                  <br />
                  <h1 id='heading'>Welcome to Camerooncom</h1>
                  <h3>Dear $name,</h3>
                  <p>$message</p>
                  <br />";

        $msg .= "              
            </div>
        </body>
        </html>";

        return $msg;

    }






    /**
     * @param $username
     * @param $password
     * @param $firstname
     * @return string
     */
    private function passwordResetTemplate($firstname, $resetCode)
    {
        $name = ucfirst($firstname);

        $msg = "
            <!DOCTYPE HTML>
		    <html class=\"no - js\" lang=\"en - gb\">
		    <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>

                    <style type='text/css'>
                        #heading {
                                 background-color: #b3d4fc; /*This makes that blue bg color*/
               font-family: Verdana, Geneva, sans-serif;
                font-size: 12px;
                text-align: center;
             }

           #imageBox {
                    float: right;
                    }
                    
           </style>
        
        
           <!--[if lt IE 7]>
          <style type='text/css'>
          #wrapper { height:100%; }
          </style>
          <![endif]-->
        
          <!--[if lt IE 8]>
          <link rel='stylesheet' href='css/ie.css'>
          <![endif]-->
        
        </head>
        <body>
             <div id='maincontent' class='column'> 
                  <br />
                  <h1 id='heading'>Message from $this->_appBusinessName</h1>
                    <h1>Dear $name<br /></h1> 
                    <h2>You requested to reset your log in details for $this->_appName</h2>
                    <p>Please click on the following link to reset your password.</p>
                    <br />
                    
                    <p><a href='".$this->_config->getHomePage()."auth/reset?em=$resetCode'>Click here to reset your password</a> or copy and paste the 
                    following link in your browser:</p>
                    <p>".$this->_config->getHomePage()."auth/reset?em=$resetCode</p>

                    <br />
                    <h3>$this->_appBusinessName</h3>
                    <p>$this->_appSlogan</p>
                    <p><img src='".$this->_config->getFileRootPath()."assets/images/logos/logo.svg' /></p>
                    <br />
                  <br />";

        $msg .= "              
            </div>
        </body>
        </html>";

        return $msg;
    }


    


    private function sendErrorLogMsgToAdminTemplate($message)
    {
        $msg = "
            <!DOCTYPE HTML>
		    <html class=\"no-js\" lang=\"en-gb\">
		    <head>
                    <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>

                    <style type='text/css'>
                        #heading {
                                 background-color: #b3d4fc; /*This makes that blue bg color*/
               font-family: Verdana, Geneva, sans-serif;
                font-size: 12px;
                text-align: center;
             }

           #imageBox {
                    float: right;
                    }
                    
           </style>
        
           <!--[if lt IE 7]>
          <style type='text/css'>
          #wrapper { height:100%; }
          </style>
          <![endif]-->
        
          <!--[if lt IE 8]>
          <link rel='stylesheet' href='css/ie.css'>
          <![endif]-->
        
        </head>
        <body>
             <div id='maincontent' class='column'> 
                  <br />
                  <h1 id='heading'>!Error Alert <?=$this->_appBusinessName?></h1>
                  <p>$message</p>
                  
                  <br />
                  <p><a href='".$this->_config->getHomePage()."admin/log'>Click here to visit the logs</a> or copy and paste the 
                    following link in your browser:</p>
                    <p>".$this->_config->getHomePage()."admin/log</p>
                  <br />";

        $msg .= "              
            </div>
        </body>
        </html>";

        return $msg;
    }
}


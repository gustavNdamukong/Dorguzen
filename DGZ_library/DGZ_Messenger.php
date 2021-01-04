<?php

namespace DGZ_library;

use settings\Settings;

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
        $settings = new Settings();

        $this->_config = new Settings();

        $this->_appEmail = $settings->getSettings()['appEmail'];

        $this->_appEmailOther = $settings->getSettings()['appEmailOther'];

        if ($settings->getSettings()['live'] == false)
        {
            $this->_headerFrom = $settings->getSettings()['localHeaderFrom'];
        }
        else
        {
            $this->_headerFrom = $settings->getSettings()['liveHeaderFrom'];
        }

        $this->_headerReplyTo = $settings->getSettings()['headerReply-To'];

        $this->_appName = $settings->getSettings()['appName'];

        $this->_appBusinessName = $settings->getSettings()['appBusinessName'];

        $this->_appSlogan = $settings->getSettings()['appSlogan'];

        $this->_appURL = $settings->getSettings()['appURL'];
    }










    public function sendContactFormMsgToAdmin($name, $visitorEmail, $phone, $message)
    {
        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $visitorEmail\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        $to = $this->_appEmail.','.$this->_appEmailOther;
        $subject = "Inquiry from your website contact form";

        $msg = $this->sendContactFormMsgToAdminTemplate($name, $visitorEmail, $phone, $message);

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

        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $this->_headerReplyTo\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        $msg = $this->passwordResetTemplate($firstname, $resetCode);

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
        if ($this->_config->getSettings()['live'])
        {
            $url = $this->_config->getSettings()['liveUrl'];
        }
        else
        {
            $url = $this->_config->getSettings()['localUrl'];
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









    /**
     * @param $firstname
     * @param $resetCode
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
                    
                    <p><a href='".$this->_config->getHomePage()."admin/verifyEmail?em=$resetCode'>Click here to reset your password</a> or copy and paste the 
                    following link in your browser:</p>
                    <p>".$this->_config->getHomePage()."/admin/verifyEmail?em=$resetCode</p>

                    <br />
                    <h3>$this->_appBusinessName</h3>
                    <p>$this->_appSlogan</p>
                    <p><img src='".$this->_config->getHomePage()."assets/images/logos/final_p3.svg' /></p>
                    <br />
                  <br />";

           $msg .= "              
            </div>
        </body>
        </html>";

           return $msg;
       }

    

    
}


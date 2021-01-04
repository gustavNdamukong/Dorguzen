<?php

namespace DGZ_library;

use settings\Settings;
class DGZ_Notifier
{

    protected $_applicationEmail;
    
    protected $_headerReplyTo;
    
    protected $_headerFrom;

    protected $_appName;

    protected $_appBusinessName;



    public function __construct()
    {
        $config = new Settings();

        $this->_applicationEmail = $config->getSettings()['appEmail'];
        $this->_appName = $config->getSettings()['appName'];
        $this->_appBusinessName = $config->getSettings()['appBusinessName'];

        if ($config->getSettings()['live'] == false)
        {
           $this->_headerFrom = $config->getSettings()['localHeaderFrom'];
        }
        else
        {
            $this->_headerFrom = $config->getSettings()['liveHeaderFrom'];
        }

        $this->_headerReplyTo = $this->_headerFrom = $config->getSettings()['headerReply-To'];
    }









    public function notify($receiverName, $email, $subject, $message)
    {
        $to = "$email";

        $headers  = "From: $this->_headerFrom\r\n";
        $headers .= "Reply-To: $this->_headerReplyTo\r\n";
        $headers .= "Content-type: text/html; charset=utf-8\r\n";

        $msg = $this->notifierTemplate($receiverName, $message);

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



    ######<!-- ==========================
    #####  FIRST SIMPLE EMAIL TEMPLATE WITH ONE IMAGE
    #######=========================== -->
    private function sendContactFormMsgToAdminTemplate($name, $email, $message)
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
                  <h1 id='heading'>Message from the <?=$this->_appBusinessName?> Contact form</h1>
                  <h3>From $name,</h3>
                  <h3>Their email address: $email,</h3>
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
































    ######<!-- ==========================
    #####  FIRST SIMPLE EMAIL TEMPLATE WITH ONE IMAGE
    #######=========================== -->
    private function notifierTemplate($name, $message)
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
                  <h1 id='heading'>Welcome to <?=$this->_appBusinessName?></h1>
                  <h3>Dear $name,</h3>
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
       


    

    
}


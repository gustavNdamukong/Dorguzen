<?php
namespace controllers;


use DGZ_library\DGZ_Validate;
use DGZ_library\DGZ_Messenger;
use ContactFormMessage;
use DGZ_library\DGZ_View;


class FeedbackController extends \DGZ_library\DGZ_Controller
{

    public function __construct()
    {
        parent::__construct();
    }






    public function getDefaultAction()
    {
        return 'defaultAction';
    }





    public function defaultAction()
    {
        $view = DGZ_View::getView('contact', $this, 'html');

        $this->setLayoutDirectory($this->settings->getSettings()['layoutDirectory']);
        $this->setLayoutView($this->settings->getSettings()['defaultLayout']);
        $view->show();
    }





    public function contact()
    {
        $view = DGZ_View::getView('contact', $this, 'html');

        $this->setLayoutDirectory($this->settings->getSettings()['layoutDirectory']);
        $this->setLayoutView($this->settings->getSettings()['defaultLayout']);
        $view->show();
    }





    public function processContact()
    {

        if ((isset($_POST['name'])) && ($_POST['name'] != ''))
        {
            $val = new DGZ_Validate();
            $contact_error = $name = $email = $phone = $message = '';

            $name = $val->fix_string($_POST['name']);
            if ($name == "")
            {
                $contact_error .= "<p>Please enter your name</p>";
            }

            //validate the email
            if (isset($_POST['email'])) {
                $email = $_POST['email'];
                //Bear in mind that validate_email() returns "" if the email is valid
                $emailError = $val->validate_email($email);
                if ($emailError == "")
                {
                    //the email is valid
                    $email = $email;
                }
                else
                {
                    $contact_error .= $emailError;
                }
            }

            //validate the phone number if supplied
            if (isset($_POST['phone'])) {
                $phone= $val->fix_string($_POST['phone']);
            }

            //Validate the message
            if (isset($_POST['message'])) {
                $message = $val->fix_string($_POST['message']);
            }
            if ($message == '')
            {
                // No message provided
                $contact_error .= '<p>Please enter a message</p>';
            }


            if ($contact_error == '') {
                //First, save the message to DB
                $contactFormModel = new ContactFormMessage();
                $contactFormModel->contactformmessage_name = $name;
                $contactFormModel->contactformmessage_email = $email;
                if ($phone != '') {
                    $contactFormModel->contactformmessage_phone = $phone;
                }

                $contactFormModel->contactformmessage_message = $message;
                $contactFormModel->contactformessage_date = date("Y-m-d H:i:s");
                $saved = $contactFormModel->save();

                //Also send an email to the site admin
                $messenger = new DGZ_Messenger();
                $send = $messenger->sendContactFormMsgToAdmin($name, $email, $phone, $message);

                if ($send) {
                    $this->addSuccess('Your message has been received and we will contact you ASAP', 'Thank you!');
                    $this->redirect('feedback', 'contact');
                }
            }
            else
            {
                $this->addErrors($contact_error, 'Sorry!');
                $this->redirect('feedback', 'contact');
            }
        }

    }


}
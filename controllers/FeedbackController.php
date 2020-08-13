<?php
namespace controllers;


use DGZ_library\DGZ_Validate;
use DGZ_library\DGZ_Messenger;
use DGZ_library\DGZ_Uploader\DGZ_Uploader;
use Testimonials;
use ContactFormMessage;

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
        $view = \DGZ_library\DGZ_View::getView('contact', $this, 'html');

        $this->setLayoutDirectory('dorguzApp');
        $this->setLayoutView('dorguzAppLayout');
        $view->show();
    }








    public function giveTestimonial()
    {
        if ((isset($_POST['tm_client_name'])) && ($_POST['tm_client_name'] != ''))
        {
            //Clean the input to prevent SQL Injection attacks
            $tm_error = $tm_jobType = $tm_company = $tm_job_date = $tm_job_completion_date = $tm_email = $tm_client_tel = $tm_rating = $tm_comment = '';
            $val = new DGZ_Validate();

            //get client name
            $tm_name = $_POST['tm_client_name'];
            $tm_name = $val->fix_string($tm_name);
            if ($tm_name == "")
            {
                // No name provided
                $tm_error .= '<p>Please enter your name</p>';
            }

            //get the tm_job type
            if (isset($_POST['tm_jobType'])) {
                $tm_jobType = $_POST['tm_jobType'];
                $tm_jobType = $val->fix_string($tm_jobType);
            }

            //get company name
            if (isset($_POST['tm_company'])) {
                $tm_company = $_POST['tm_company'];
                $tm_company = $val->fix_string($tm_company);
            }

            //get job date
            if (isset($_POST['tm_job_date'])) {
                $tm_job_date = $_POST['tm_job_date'];
                $tm_job_date = $val->fix_string($tm_job_date);
            }

            //get job completion date
            if (isset($_POST['tm_job_completion_date'])) {
                $tm_job_completion_date = $_POST['tm_job_completion_date'];
                $tm_job_completion_date = $val->fix_string($tm_job_completion_date);
            }

            //get client email
            if (isset($_POST['tm_client_email'])) {
                $tm_email = $_POST['tm_client_email'];

                //Bear in mind that validate_email() returns "" if the email is valid
                $emailError = $val->validate_email($tm_email);
                if ($emailError == "")
                {
                    //the email is valid
                    $tm_email = $tm_email;
                }
                else
                {
                    $tm_error .= $emailError;
                }
            }


            //get client telephone
            if (isset($_POST['tm_client_tel'])) {
                $tm_client_tel = $_POST['tm_client_tel'];
                $tm_client_tel = $val->fix_string($tm_client_tel);
            }

            //get client rating
            $tm_rating = $_POST['tm_rating'];
            if ($tm_rating == "")
            {
                // No rating provided
                $tm_error .= '<p>Please give a rating</p>';
            }

            //get client rating comment
            $tm_comment = $_POST['tm_comment'];
            $tm_comment = $val->fix_string($tm_comment);
            if ($tm_comment == "")
            {
                // No comment provided
                $tm_error .= '<p>Please enter the reason for your rating</p>';
            }


            // We'll only run the following code if no errors were encountered
            if ($tm_error == "")
            {
                $tms = new \Testimonials();

                $tms->testimonials_client_name = $tm_name;
                $tms->testimonials_job_type = $tm_jobType;
                $tms->testimonials_company = $tm_company;
                $tms->testimonials_job_date = $tm_job_date;
                $tms->testimonials_job_completion_date = $tm_job_completion_date;
                $tms->testimonials_client_email = $tm_email;
                $tms->testimonials_client_tel = $tm_client_tel;
                $tms->testimonials_rating = $tm_rating;
                $tms->testimonials_comment = $tm_comment;
                $tms->testimonials_date_ = date("Y-m-d H:i:s");

                $testified = $tms->save();

                if ($testified) {
                    $this->addSuccess('You have successfully provided feedback', 'Thank you!');
                    $this->redirect('home', 'home');
                }
                else {
                    $this->addErrors($testified, 'Sorry!');
                    $this->redirect('home', 'home');
                }
            }
            else
            {
                // There were errors, so send the visitor back with them
                $this->addErrors($tm_error,'Sorry!');
                $this->redirect('home', 'home');
            }
        }
        else
        {
            // None of the form fields were entered
            $this->addErrors('Please fill in the form','Error');
            $this->redirect('home','home');
        }



    }








    /**
     * Get all testimonials to display some on site
     */
    public function getTestimonials()
    {
        $tm_model = new \Testimonials();

        $query = "SELECT * FROM testimonials WHERE testimonials_approved = 'yes'";
        $testimonials = $tm_model->query($query);
        return $testimonials;
    }








    /**
     * Grab and display a given testimonial
     *
     * @param $testimonials_id
     * @throws \DGZ_library\DGZ_Exception
     */
    public function testimonial($testimonials_id)
    {
        $tm_model = new \Testimonials();

        $query = "SELECT * FROM testimonials WHERE testimonials_id = $testimonials_id";
        $testimonial = $tm_model->query($query);

        $view = \DGZ_library\DGZ_View::getView('testimonial', $this, 'html');
        $this->setPageTitle('Testimonial');
        $view->show($testimonial[0]);
    }







    public function testimonials()
    {
        $tm_model = new \Testimonials();

        $query = "SELECT * FROM testimonials WHERE testimonials_approved = 'yes'";
        $testimonials = $tm_model->query($query);

        $view = \DGZ_library\DGZ_View::getView('allTestimonials', $this, 'html');
        $this->setPageTitle('Testimonials');
        $view->show($testimonials);
    }






    public function manageTestimonials($edit = '')
    {
        if ($edit == '') {
            $view = \DGZ_library\DGZ_View::getView('manageTestimonials', $this, 'html');
            $this->setLayoutDirectory('admin');
            $this->setLayoutView('adminLayout');
            $view->show();
        }
        else
        {
            //modify the testimonial status by the given record ID
            if ((isset($_POST['tes_approve'])) && ($_POST['tes_approve'] != "")) {
                $approvedStatus = $_POST['tes_approve'];
                $recId = $_POST['recId'];

                if ($recId == "") {
                    $this->addErrors('We could not detect the ID of the record you are trying to update', 'Sorry!');
                    $this->redirect('feedback', 'manageTestimonials');
                }

                if ($approvedStatus == "") {
                    $this->addErrors('Please pick an option', 'Sorry!');
                    $this->redirect('feedback', 'manageTestimonials');
                }

                //build the object as you would like to update it
                //We only set values for the fields we need to update
                $testimonialClass = new Testimonials();
                $testimonialClass->testimonials_approved = "$approvedStatus";

                //We now build the where clause as that is a MUST when doing an update.
                $where = ['testimonials_id' => $recId];
                $updated = $testimonialClass->updateObject($where);
                if ($updated) {
                    $this->addSuccess('The testimonial was successfully updated', 'Great!');
                    $this->redirect('feedback', 'manageTestimonials');
                    exit();
                }
                else
                {
                    $this->addErrors('Something went wrong');
                    $this->redirect('feedback', 'manageTestimonials');
                }
            }

            $this->addErrors('Please pick an option', 'Sorry!');
            $this->redirect('feedback', 'manageTestimonials');
        }
    }








    public function deleteTestimonial($testimonials_id)
    {
            if ((isset($_GET['testimonials_id'])) && ($_GET['testimonials_id'] != "")) {
                $recId = $_GET['testimonials_id'];
                $testimonialClass = new Testimonials();

                $query = "DELETE FROM testimonials WHERE testimonials_id = $recId";

                $deleted = $testimonialClass->query($query);

                if ($deleted) {
                    $this->addSuccess('The testimonial was successfully deleted');
                    $this->redirect('feedback', 'manageTestimonials');
                    exit();
                }
            }

            $this->addErrors('Something went wrong', 'Error');
            $this->redirect('feedback', 'manageTestimonials');
    }








    /**
     * shows the details of a single news item
     *
     *
     */
    public function subscribe()
    {
        if ((isset($_POST['nl_name'])) && ($_POST['nl_name'] != '')) {
            // Clean the input to prevent SQL Injection attacks
            $nl_error = $nl_email = '';
            $val = new DGZ_Validate();

            $nl_name = $_POST['nl_name'];
            $nl_name = $val->fix_string($nl_name);
            if ($nl_name == '') {
                // No name provided
                $nl_error .= '<p>Please enter your name</p>';
            }


            if (isset($_POST['home2'])) {
                $home2 = $_POST['home2'];
            }


            //validate the email
            if (isset($_POST['nl_email'])) {
                $nl_email = $_POST['nl_email'];
                //Bear in mind that validate_email() returns "" if the email is valid
                $emailError = $val->validate_email($nl_email);
                if ($emailError == "")
                {
                    //the email is valid
                    $nl_email = $nl_email;
                }
                else
                {
                    $nl_error .= $emailError;
                }
            }

            // We'll only run the following code if no errors were encountered
            if ($nl_error == "") {
                //First of all check if this visitor has already subscribed to our newsletter
                $sub_model = new \Subscribers();

                if (!$sub_model->checkIfAlreadySubcribed($nl_email)) {


                    $subs = new \Subscribers();
                    $subs->subscribers_name = $nl_name;
                    $subs->subscribers_email = $nl_email;
                    $subs->subscribers_just_subscribed = 1;
                    $subs->subscribers_date_created = date("Y-m-d H:i:s");

                    $subscribed = $subs->save();

                    if ($subscribed) {
                        $this->addSuccess('You have been successfully subscribed', 'Thank you!');
                        if (isset($home2)) {
                            $this->redirect('home', 'home');
                        }
                        else {
                            $this->redirect('home');
                        }
                    }
                    else {
                        $this->addErrors($subscribed, 'Sorry!');

                        if (isset($home2)) {
                            $this->redirect('home', 'home');
                        }
                        else {
                            $this->redirect('home', 'home');
                        }
                    }
                }
                else {
                    // The email address provided is already in the database
                    $this->addWarning('That email address is already subscribed.', 'Thank you');

                    if (isset($home2)) {
                        $this->redirect('home', 'home');
                    }
                    else {
                        $this->redirect('home', 'home');
                    }
                }

            }
            else {
                // There were errors, so send the visitor back with them
                $this->addErrors($nl_error, 'Sorry!');

                if (isset($home2)) {
                    $this->redirect('home', 'home');
                }
                else {
                    $this->redirect('home', 'home');
                }
            }
        }
        else
        {
            //nothing was entered in the form fields
            $this->addErrors('Please fill in the form', 'Error!');
            $this->redirect('home', 'home');
        }

    }








    public function manageSubscribers()
    {
        $view = \DGZ_library\DGZ_View::getView('manageSubscribers', $this, 'html');

        $this->setLayoutDirectory('admin');
        $this->setLayoutView('adminLayout');

        $this->setPageTitle('Subscribers');
        $view->show();
    }








    public function contact()
    {
        $view = \DGZ_library\DGZ_View::getView('contact', $this, 'html');

        $this->setLayoutDirectory('dorguzApp');
        $this->setLayoutView('dorguzAppLayout');
        $view->show();
    }









    public function processContact()
    {
        //echo '<pre>'; var_dump($_POST); die();

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

                //Save to DB
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










    public function getAllSubscibers()
    {
        $subs = new \Subscribers();
        return $subs->getAllSubscibers();
    }








    public function getAllNewsletters()
    {
        $subs = new \Newsletter();
        return $subs->getAllNewsletters();
    }





    public function getAllNewSubscribers()
    {
        $subs = new \Subscribers();
        return $subs->getAllNewSubscribers();
    }






    public function sendNewsletterWelcomeEmail()
    {
        $subscribers = unserialize(base64_decode($_POST['allSubscribers']));

        //echo '<pre>'; var_dump($subscribers); die();
        $subs = new \Subscribers();

        $messenger = new DGZ_Messenger();

        foreach ($subscribers as $subscriber)
        {
            $name = $subscriber['subscribers_name'];
            $email = $subscriber['subscribers_email'];
            $messenger->sendNewsletterWelcomeMsg($name, $email, 'welcome letter');

            $sql = "UPDATE subscribers SET subscribers_just_subscribed = '0' WHERE subscribers_email = '$email'";
            $updated = $subs->query($sql);
        }

        $this->addSuccess('Welcome message sent to all new subscribers');
        $this->redirect('feedback', 'manageSubscribers');
    }







    public function sendNewsletterToAll()
    {
        //Grab the submitted values
        $subscribers = unserialize(base64_decode($_POST['allSubscribers']));
        $newsletterId = $_POST['newsletter_id'];

        //echo '<pre>'; var_dump($_POST); die();
        $subs = new \Subscribers();

        $messenger = new DGZ_Messenger();

        foreach ($subscribers as $subscriber)
        {
            $name = $subscriber['subscribers_name'];
            $email = $subscriber['subscribers_email'];
            $messenger->sendNewsletterMsg($name, $email, $newsletterId);

            $sql = "UPDATE subscribers SET subscribers_just_subscribed = '0' WHERE subscribers_email = '$email'";
            $updated = $subs->query($sql);
        }

        $this->addSuccess('Newsletter successfully sent to all subscribers');
        $this->redirect('feedback', 'manageSubscribers');
    }










    /**
     * Create a newsletter
     *
     *
     */
    public function createNewsletter()
    {
        // Clean the input to prevent SQL Injection attacks
        $nl_error = '';
        $filename = false;

        if ((isset($_POST['nl_name'])) && ($_POST['nl_name'] != ''))
        {
            $nl_name = $_POST['nl_name'];
        }
        else
        {
            // No name provided
            $nl_error .= '<p>Please enter a name for the Newsletter</p>';
        }


        if ((isset($_POST['nl_subject'])) && ($_POST['nl_subject'] != ''))
        {
            $nl_subject = $_POST['nl_subject'];
        }
        else
        {
            // No subject provided
            $nl_error .= '<p>Please enter a subject for the Newsletter</p>';
        }




        if ((isset($_POST['nl_heading'])) && ($_POST['nl_heading'] != ''))
        {
            $nl_heading = $_POST['nl_heading'];
        }
        else
        {
            // No heading provided
            $nl_error .= '<p>Please enter heading text for the Newsletter</p>';
        }





        if ((isset($_POST['nl_message'])) && ($_POST['nl_message'] != ''))
        {
            $nl_message = $_POST['nl_message'];
        }
        else
        {
            // No message provided
            $nl_error .= '<p>Please enter the message for the Newsletter</p>';
        }

        
        
        
        //Check the uploaded file - btw, it's optional which is why we check for it separately after checking for errors ($nl_error)
        if (isset($_FILES['nl_image']['name'])) {
            $filename = $_FILES['nl_image']['name'];
        }

        // $filename will be an empty array if the upload failed
        //we also make sure that we only check n validate the image caption field if a file was actually uploaded
        if ($filename) {
            //If a file was uploaded, then they MUST enter a caption for the image, as the caption text is meant to appear beside the image in the newsletter email
            if ((isset($_POST['nl_image_caption'])) && ($_POST['nl_image_caption'] != ''))
            {
                $nl_image_caption = $_POST['nl_image_caption'];
            }
            else
            {
                // No caption provided
                $nl_error .= '<p>Please enter a caption for the uploaded image</p>';
            }
        }
        
        
        if ($nl_error == '') {
            if ($filename)
            {
                /////$destination = 'assets/images/email_images/';

                $settings = new \settings\Settings();
                /////$maxEmailImageCropSize = $settings->getSettings()['maxFileUploadSize'];
                $upload = new DGZ_Uploader('emailImageDir');
                /////////////$upload = new DGZ_Uploader\DGZ_Uploader('emailImagesDir');
                /////$upload->setMaxSize($maxEmailImageCropSize);
                $upload->move('resize');
                $newFilename = $upload->getFilenames()[0];
            }

            //Whether an image was uploaded or not proceed with the DB insertion
            $newsletterClass = new \Newsletter();
            $newsletterClass->newsletter_name = $nl_name;
            $newsletterClass->newsletter_subject = $nl_subject;
            $newsletterClass->newsletter_heading = $nl_heading;
            $newsletterClass->newsletter_message = $nl_message;

            //We only save the filename if one was uploaded as it was optional
            if (isset($newFilename)) {
                $newsletterClass->newsletter_image = $newFilename;
                //equally, there'd only be a caption if a file was involved
                $newsletterClass->newsletter_image_caption = $nl_image_caption;
            }
            
            $saved = $newsletterClass->save();
            if ($saved) {
                $this->addSuccess('The newsletter was successfully created', 'Great!');
                $this->redirect('feedback', 'manageSubscribers');
            }
        }
        else
        {
            $this->addErrors($nl_error, 'Sorry!');
            $this->redirect('feedback', 'manageSubscribers');
        }



    }












    /**
     * Edit a newsletter
     *
     *
     */
    public function editNewsletter()
    {
        // Clean the input to prevent SQL Injection attacks
        $nl_error = '';

        if ((isset($_POST['recId'])) && ($_POST['recId'] != ''))
        {
            $nl_id = $_POST['recId'];
        }
        else
        {
            // No ID for the record came through
            $nl_error .= '<p>Something went wrong, please try again or contact your software engineer!</p>';
        }


        if ((isset($_POST['nl_name'])) && ($_POST['nl_name'] != ''))
        {
            $nl_name = $_POST['nl_name'];
        }
        else
        {
            // No name provided
            $nl_error .= '<p>Please enter a name for the Newsletter</p>';
        }


        if ((isset($_POST['nl_subject'])) && ($_POST['nl_subject'] != ''))
        {
            $nl_subject = $_POST['nl_subject'];
        }
        else
        {
            // No subject provided
            $nl_error .= '<p>Please enter a subject for the Newsletter</p>';
        }




        if ((isset($_POST['nl_heading'])) && ($_POST['nl_heading'] != ''))
        {
            $nl_heading = $_POST['nl_heading'];
        }
        else
        {
            // No heading provided
            $nl_error .= '<p>Please enter heading text for the Newsletter</p>';
        }





        if ((isset($_POST['nl_message'])) && ($_POST['nl_message'] != ''))
        {
            $nl_message = $_POST['nl_message'];
        }
        else
        {
            // No message provided
            $nl_error .= '<p>Please enter the message for the Newsletter</p>';
        }





        //There could previously have been no image at all
        if ((isset($_POST['showCurrentImage'])) && ($_POST['showCurrentImage'] != ''))
        {
            $showCurrentImage = $_POST['showCurrentImage'];
        }


        //Check the uploaded file - btw, it's optional which is why we check for it separately after checking for errors ($nl_error)
        //Plus, if they uploaded a new img, then we have to delete the image file (if there was one) from the folder system
        $filename = (isset($_FILES['nl_image']['name']) && ($_FILES['nl_image']['name'] != '')?$_FILES['nl_image']['name']:'');

        // $filename will be an empty array if the upload failed
        //we also make sure that we only check n validate the image caption field if a file was actually uploaded
        if ($filename != '') {
            //If a file was uploaded, then they MUST enter a caption for the image, as the caption text is meant to appear beside the image in the newsletter email
            if ((isset($_POST['nl_image_caption'])) && ($_POST['nl_image_caption'] != ''))
            {
                $nl_image_caption = $_POST['nl_image_caption'];
            }
            else
            {
                // No caption provided
                $nl_error .= '<p>Please enter a caption for the uploaded image</p>';
            }
        }


        if ($nl_error == '') {
            if ($filename != '')
            {
                $destination = 'assets/images/email_images/';

                //Delete the previous image if there was one before uploading the new one
                if (isset($showCurrentImage))
                {
                    unlink("$destination/$showCurrentImage");
                }

                $settings = new \settings\Settings();
                /////$maxEmailImageCropSize = $settings->getSettings()['maxFileUploadSize'];
                $upload = new DGZ_Uploader('emailImageDir');
                /////$upload->setMaxSize($maxEmailImageCropSize);
                $upload->move('resize');
                $newFilename = $upload->getFilenames()[0];
            }

            //Whether an image was uploaded or not proceed with the DB insertion
            $newsletterClass = new \Newsletter();
            $newsletterClass->newsletter_name = $nl_name;
            $newsletterClass->newsletter_subject = $nl_subject;
            $newsletterClass->newsletter_heading = $nl_heading;
            $newsletterClass->newsletter_message = $nl_message;

            //We only save the filename if one was uploaded as it was optional
            //Note that, otherwise if no image was uploaded but there was one existing previously, we do nothing about it and do not update that field
            if (isset($newFilename)) {
                $newsletterClass->newsletter_image = $newFilename;
                //equally, there'd only be a caption if a file was involved
                $newsletterClass->newsletter_image_caption = $nl_image_caption;
            }

            //We now build the where clause as that is a MUST when doing an update. In this case we simply use only the record ID
            $where = ['newsletter_id' => $nl_id];
            $updated = $newsletterClass->updateObject($where);
            if ($updated) {
                $this->addSuccess('The newsletter was successfully updated', 'Great!');
                $this->redirect('feedback', 'manageSubscribers');
            }
        }
        else
        {
            $this->addErrors($nl_error, 'Sorry!');
            $this->redirect('feedback', 'manageSubscribers');
        }
    }















    public function deleteNewsletter($newsletter_id, $confirmDel = 'false')
    {
        //echo '<pre>'; var_dump($_REQUEST); die();
        if ($confirmDel == 'false') {
            $view = \DGZ_library\DGZ_View::getView('confirmDeleteNewsletter', $this, 'html');
            $view->show($newsletter_id);
        }
        elseif ($confirmDel == 'true')
        {
            $newsletter = new \Newsletter();
            $newsletterData = $newsletter->getById($newsletter_id);

            //get the image of the newsletter if there is one, as it has to be removed from the folder system
            $newsletter_image = $newsletterData[0]['newsletter_image'];

            if ($newsletter_image != '') {
                //delete it
                $imageLocation = 'assets/images/email_images/';
                unlink("$imageLocation/$newsletter_image");
            }

            //now delete the newsletter from DB
            $crit = ['newsletter_id' => $newsletter_id];

            $del = $newsletter->deleteWhere($crit);

            if ($del) {
                $this->addSuccess('The newsletter was successfully deleted');
                $this->redirect('feedback', 'manageSubscribers');
            }
            else {
                $this->addErrors('The newsletter could not be deleted');
                $this->redirect('feedback', 'manageSubscribers');
            }
        }


    }













}
<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_Validate;
use Dorguzen\Core\DGZ_Messenger;
use Dorguzen\Services\FeedbackService;

class FeedbackController extends DGZ_Controller
{
    public function __construct(private FeedbackService $feedbackService)
    {
        parent::__construct();
    }

    public function getDefaultAction()
    {
        return 'defaultAction';
    }

    public function defaultAction()
    {
        $this->contact();
    }

    public function contact()
    {
        $view = DGZ_View::getView('contact', $this, 'html');
        $this->setLayoutDirectory($this->config->getConfig()['layoutDirectory']);
        $this->setLayoutView($this->config->getConfig()['defaultLayout']);
        $view->show();
    }

    public function processContact()
    {
        if (empty($_POST['name'])) {
            $this->redirect('feedback', 'contact');
            return;
        }

        $val = new DGZ_Validate();

        $name    = $val->fix_string($_POST['name']    ?? '');
        $email   =                  $_POST['email']   ?? '';
        $phone   = $val->fix_string($_POST['phone']   ?? '');
        $message = $val->fix_string($_POST['message'] ?? '');

        $contact_error = $this->feedbackService->validateContactInput($name, $email, $message);

        if ($contact_error !== '') {
            $this->addErrors($contact_error, 'Sorry!');
            $this->redirect('feedback', 'contact');
            return;
        }

        $this->feedbackService->saveContactMessage([
            'name'    => $name,
            'email'   => $email,
            'phone'   => $phone,
            'message' => $message,
        ]);

        $messenger = new DGZ_Messenger();
        $messenger->sendContactFormMsgToAdmin($name, $email, $phone, $message);

        $this->addSuccess('Your message has been received. We will contact you soon!', 'Thank you!');
        $this->redirect('feedback', 'contact');
    }
}

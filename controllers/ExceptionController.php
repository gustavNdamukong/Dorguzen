<?php

namespace controllers;


class ExceptionController extends \DGZ_library\DGZ_Controller
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

    }

    public function error()
    { 
        $view = \DGZ_library\DGZ_View::getView('DGZExceptionView', $this, 'html');
        http_response_code(404);
        $this->setLayoutDirectory($this->getDefaultLayoutDirectory());
        $this->setLayoutView($this->getDefaultLayout());
        $this->setPageTitle('Error - 404');
        $exceptions = $this->getException();
        $view->show($exceptions);
    }

}
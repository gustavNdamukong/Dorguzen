<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_View;
use Dorguzen\Core\DGZ_Controller;

class HomeController extends DGZ_Controller
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
        $view = DGZ_View::getView('home', $this, 'html');
        $this->setPageTitle('Home');
        $this->setImageSlider(true);
        $view->show();
    }
}

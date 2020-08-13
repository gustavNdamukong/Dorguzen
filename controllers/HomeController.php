<?php

namespace controllers;



class HomeController extends \DGZ_library\DGZ_Controller  {



    public function __construct()
    {
        //die('we in aboutUsController constructor dawg!!! haha');
        parent::__construct();
    }


    public function getDefaultAction()
    {
        return 'defaultAction';
    }








    public function defaultAction()
    {
        //If you set no layout view, the default layout will be used
        //if you set a layout (like so: setLayoutView('BlankLayout');) make sure that layout file (class) is in the default layout directory
        //if you set a default layout folder (like so: $this->setDefaultLayoutDirectory('CoolPersonalWebsite');), make sure you also set the
        // layout file to be used in that dir
        $view = \DGZ_library\DGZ_View::getView('home', $this, 'html');
        $this->setPageTitle('The Dorguzen framework');
        $this->setImageSlider(true);
        $view->show();
    }







    public function home()
    {
        $view = \DGZ_library\DGZ_View::getView('home', $this, 'html');
        $this->setPageTitle('Home');
        $this->setImageSlider(true);
        $view->show();
    }


}


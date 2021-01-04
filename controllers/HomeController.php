<?php

namespace controllers;

use DGZ_library\DGZ_View;

class HomeController extends \DGZ_library\DGZ_Controller  {



    public function __construct()
    {
        parent::__construct();
    }


    public function getDefaultAction()
    {
        return 'defaultAction';
    }


    /**
     * If you set no layout view, the default layout will be used
     * if you set a layout (like so: setLayoutView('BlankLayout');) make sure that 'BlankLayout.php' file (class) is in the default layout directory
     * if you set a default layout folder (like so: $this->setDefaultLayoutDirectory('CoolPersonalWebsite');), make sure you also set the
     * layout file to be used in that 'CoolPersonalWebsite' dir
     *
     * @throws \DGZ_library\DGZ_Exception
     */
    public function defaultAction()
    {
        $view = DGZ_View::getView('home', $this, 'html');
        $this->setPageTitle('Home');
        $this->setImageSlider(true);
        $view->show();
    }




    public function home()
    {
        $view = DGZ_View::getView('home', $this, 'html');
        $this->setPageTitle('Home');
        $this->setImageSlider(true);
        $view->show();
    }


}


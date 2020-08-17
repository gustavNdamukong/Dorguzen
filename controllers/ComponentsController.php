<?php

namespace controllers;



class ComponentsController extends \DGZ_library\DGZ_Controller  {





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
        //If you set no layout view, the default layout will be used
        //if you set   a layout (like so: setLayoutView('EmailLayout');) make sure that layout file (class) is in the default layout directory
        //if you set a default layout folder (like so: $this->setDefaultLayoutDirectory('CoolPersonalWebsite');), make sure you also set the layout file to be used in that dir
        $view = \DGZ_library\DGZ_View::getAdminView('components', $this, 'html');
        $this->setPageTitle('components');
        $view->show();
    }




    /**
     * View extra layout tools and components that you can use to build your app with
     *
     * @throws \DGZ_library\DGZ_Exception
     */
    public function components()
    {
        $view = \DGZ_library\DGZ_View::getAdminView('components', $this, 'html');
        $view->show();
    }





    /**
     * View extra layout tools and components that you can use to build your app with
     *
     * @throws \DGZ_library\DGZ_Exception
     */
    public function index()
    {
        $view = \DGZ_library\DGZ_View::getView('index', $this, 'html', true);
        $view->show();
    }




    /**
     * View extra layout tools and components that you can use to build your app with
     * remember to pass a 4th parameter true to getView()
     *
     * @throws \DGZ_library\DGZ_Exception
     */
    public function products()
    {
        $view = \DGZ_library\DGZ_View::getView('products', $this, 'html', true);
        $view->show();
    }





    /**
     * View extra layout tools and components that you can use to build your app with
     * remember to pass a 4th parameter true to getView()
     *
     * @throws \DGZ_library\DGZ_Exception
     */
    public function singleProduct()
    {
        $view = \DGZ_library\DGZ_View::getView('singleProduct', $this, 'html', true);
        $view->show();
    }




    public function myAccount()
    {
        $view = \DGZ_library\DGZ_View::getView('myAccount', $this, 'html', true);
        $view->show();
    }





    public function checkout()
    {
        $view = \DGZ_library\DGZ_View::getView('checkout', $this, 'html', true);
        $view->show();
    }





    public function wishlist()
    {
        $view = \DGZ_library\DGZ_View::getView('wishlist', $this, 'html', true);
        $view->show();
    }




    public function compare()
    {
        $view = \DGZ_library\DGZ_View::getView('compare', $this, 'html', true);
        $view->show();
    }




    public function signin()
    {
        $view = \DGZ_library\DGZ_View::getView('signin', $this, 'html', true);
        $view->show();
    }



    public function lostPassword()
    {
        $view = \DGZ_library\DGZ_View::getView('lostPassword', $this, 'html', true);
        $view->show();
    }



    public function profile()
    {
        $view = \DGZ_library\DGZ_View::getView('profile', $this, 'html', true);
        $view->show();
    }



    public function cart()
    {
        $view = \DGZ_library\DGZ_View::getView('cart', $this, 'html', true);
        $view->show();
    }



    public function address()
    {
        $view = \DGZ_library\DGZ_View::getView('address', $this, 'html', true);
        $view->show();
    }



    public function orders()
    {
        $view = \DGZ_library\DGZ_View::getView('orders', $this, 'html', true);
        $view->show();
    }



    public function signup()
    {
        $view = \DGZ_library\DGZ_View::getView('signup', $this, 'html', true);
        $view->show();
    }




    public function downloads()
    {
        $view = \DGZ_library\DGZ_View::getView('downloads', $this, 'html', true);
        $view->show();
    }




    public function stores()
    {
        $view = \DGZ_library\DGZ_View::getView('stores', $this, 'html', true);
        $view->show();
    }




    public function lookbook()
    {
        $view = \DGZ_library\DGZ_View::getView('lookbook', $this, 'html', true);
        $view->show();
    }




    public function emailTemplate()
    {
        $view = \DGZ_library\DGZ_View::getView('emailTemplate', $this, 'html', true);
        $view->show();
    }
}


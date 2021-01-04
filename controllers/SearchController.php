<?php

namespace controllers;


use DGZ_library\DGZ_Validate;
use DGZ_library\DGZ_View;


class SearchController extends \DGZ_library\DGZ_Controller
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




    


    /**
     * Modify this search form as you please. We have created it to get you started, it searches for view files existing in this app,
     * but feel free to make it do a more complex search, of something more specific.
     *
     */
    public function doSearch()
    {
        $val = new DGZ_Validate();
        $word = "";
        
        if(isset($_GET['search_keyword']))
        {
            $word = $val->fix_string($_GET['search_keyword']);
        }

        if ($word != "") {

            //We are only using keywords of view files that exist in the application
            $viewKeywords =
                [
                    'home' => 'home',
                    'contact' => 'contact',
                ];


            if (key_exists($word, $viewKeywords)) {
                $view = DGZ_View::getView($viewKeywords[$word], $this, 'html');
                $this->setPageTitle($word);
                $view->show();
            }
            elseif (isset($_GET['searchOrigin']))
            {
                if ($_GET['searchOrigin'] != '') {
                    $this->addWarning('No such page found, please visit the site Menu');
                    if ($_GET['searchOrigin'] == 'home' || $_GET['searchOrigin'] == 'Home')
                    {
                        $this->redirect('home', 'home');
                    }
                    else {
                        $this->redirect($_GET['searchOrigin']);
                    }
                }
                else
                {
                    $this->addWarning('No such page found, please visit the site Menu');
                    $this->redirect('home', 'home');
                    exit();
                }
            }
            else
            {
                $this->addWarning('No such page found, please visit the site Menu');
                $this->redirect('home', 'home');
                exit();
            }
            
        }

    }






}
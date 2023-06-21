<?php

namespace modules\seo\controllers;

use DGZ_library\DGZ_Validate;
use DGZ_library\DGZ_Translator;
use ReflectionClass;
use ReflectionException;
use controllers\AuthController;
use DGZ_library\DGZ_View;
use Seo;
use Seo_global;

class SeoController extends \DGZ_library\DGZ_Controller
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
        $seo = new Seo();
        $globalSeo = new Seo_global();
        
        //echo '<pre>';
        //die(print_r($seo->getData()));
        $seoData = $seo->getData();
        $globalSeoData = $globalSeo->getData();
        $globalData = ($globalSeoData ? $globalSeoData[0] : []);
        $view = DGZ_View::getModuleView('seo', 'index', $this, 'html');
        $this->setPageTitle('Seo');
        $view->show($globalData, $seoData);
    }


    public function editPageSeo($pageId)
    {
        $seo = new Seo();
        $data = $seo->getPageSeo($pageId);
        $view = DGZ_View::getModuleView('seo', 'editPage', $this, 'html');
        $this->setPageTitle('Edit Page Seo');
        $view->show($data);
    }


    public function editGlobalSeo()
    {
        $globalSeo = new Seo_global();
        $data = $globalSeo->getAll();
    }

	
		
}


  
	
	
	
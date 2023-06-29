<?php

namespace modules\seo\controllers;

use DGZ_library\DGZ_View;
use DGZ_library\DGZ_Translator;
use Seo;
use Seo_global;

class SeoController extends \DGZ_library\DGZ_Controller
{

    private $seo;


    private $seo_global;



    public function __construct()
    {
        parent::__construct();

        $this->seo = new Seo();
        $this->seo_global = new Seo_global();
    }


    public function getDefaultAction()
    {
        return 'defaultAction';
    }


    public function defaultAction()
    { 
        $seo = new Seo();
        $globalSeo = new Seo_global();
        
        $seoData = $seo->getData();
        $globalSeoData = $globalSeo->getData();
        $globalDataSet = ($globalSeoData ? $globalSeoData : []);
        $view = DGZ_View::getModuleView('seo', 'index', $this, 'html');
        $this->setPageTitle('Seo');
        $view->show($globalDataSet, $seoData);
    }


    public function getSeoByName($pageName)
    {
        $seo = new Seo();
        $whereClause = [
            'seo_page_name' => "$pageName"
        ];

        return $seo->selectWhere([],$whereClause);
    }


    public function pageDetail($pageId)
    {
        $seo = new Seo();
        $globalSeo = new Seo_global();
        
        $seoData = $seo->getData();
        $view = DGZ_View::getModuleView('seo', 'detail', $this, 'html');
        $this->setPageTitle('Seo Page Detailed View');
        $view->show($seoData);
    }


    public function editPageSeo($pageId)
    {
        $seo = new Seo();
        $data = $seo->getPageSeo($pageId);
        $view = DGZ_View::getModuleView('seo', 'editPage', $this, 'html');
        $this->setPageTitle('Edit Page Seo');
        $view->show($data);
    }

    public function savePageEdit()
    {
        //$seo = new Seo();
        //$data = $seo->getPageSeo($pageId);
        $view = DGZ_View::getModuleView('seo', 'editPage', $this, 'html');
        $this->setPageTitle('Edit Page Seo');
        //$view->show($data);
    }

    public function addPage()
    {
        $view = DGZ_View::getModuleView('seo', 'addPage', $this, 'html');
        $this->setPageTitle('Create SEO page');
        $view->show(); 
    }

    public function saveNewPage()//////////////////////////////////////////////////////////////////
    { 
        $error = '';

        $seo_page_name = '';
        $seo_meta_title_en = '';
        $seo_meta_title_fre = '';
        $seo_meta_title_es = '';
        $seo_meta_desc_en = '';
        $seo_meta_desc_fre = '';
        $seo_meta_desc_es = '';
        $seo_dynamic = '';
        $seo_keywords_en = '';
        $seo_keywords_fre = '';
        $seo_keywords_es = '';
        $seo_canonical_href = '';
        $seo_no_index = '';
        $seo_h1_text_en = '';
        $seo_h1_text_fre = '';
        $seo_h1_text_es = '';
        $seo_h2_text_en = '';
        $seo_h2_text_fre = '';
        $seo_h2_text_es = '';
        $seo_page_content_en = '';
        $seo_page_content_fre = '';
        $seo_page_content_es = '';
        $seo_og_title_en = '';
        $seo_og_title_fre = '';
        $seo_og_title_es = '';
        $seo_og_desc_en = '';
        $seo_og_desc_fre = '';
        $seo_og_desc_es = '';
        $seo_og_image = '';
        $seo_og_image_width = '';
        $seo_og_image_height = '';
        $seo_og_image_secure_url = '';
        $seo_og_type_en = '';
        $seo_og_type_fre = '';
        $seo_og_type_es = '';
        $seo_og_url = '';
        $seo_og_video = '';
        $seo_twitter_title_en = '';
        $seo_twitter_title_fre = '';
        $seo_twitter_title_es = '';
        $seo_twitter_desc_en = '';
        $seo_twitter_desc_fre = '';
        $seo_twitter_desc_es = '';
        $seo_twitter_image = '';
        

        if (
            (isset($_POST['seo_page_name'])) &&
            ($_POST['seo_page_name'] != '')
        )
        {
            $seo_page_name = $_POST['seo_page_name'];
        }
        else
        {
            $error .= "Please you have to provide the name of the page";
        }

        if (
            (isset($_POST['seo_meta_title_en'])) &&
            ($_POST['seo_meta_title_en'] != '')
        )
        {
            $seo_meta_title_en = $_POST['seo_meta_title_en'];
        }

        if (
            (isset($_POST['seo_meta_title_fre'])) &&
            ($_POST['seo_meta_title_fre'] != '')
        )
        {
            $seo_meta_title_fre = $_POST['seo_meta_title_fre'];
        }

        if (
            (isset($_POST['seo_meta_title_es'])) &&
            ($_POST['seo_meta_title_es'] != '')
        )
        {
            $seo_meta_title_es = $_POST['seo_meta_title_es']; 
        }

        if (
            (isset($_POST['seo_meta_desc_en'])) &&
            ($_POST['seo_meta_desc_en'] != '')
        )
        {
            $seo_meta_desc_en = $_POST['seo_meta_desc_en'];
        } ////////////////

        if (
            (isset($_POST['seo_global_og_locale'])) &&
            ($_POST['seo_global_og_locale'] != '')
        )
        {
            $seo_global_og_locale = $_POST['seo_global_og_locale'];
        }

        if (
            (isset($_POST['seo_global_og_site'])) &&
            ($_POST['seo_global_og_site'] != '')
        )
        {
            $seo_global_og_site = $_POST['seo_global_og_site']; 
        }

        if (
            (isset($_POST['seo_global_og_article_publisher'])) &&
            ($_POST['seo_global_og_article_publisher'] != '')
        )
        {
            $seo_global_og_article_publisher = $_POST['seo_global_og_article_publisher'];
        } 

        if (
            (isset($_POST['seo_global_og_author'])) &&
            ($_POST['seo_global_og_author'] != '')
        )
        {
            $seo_global_og_author = $_POST['seo_global_og_author'];
        } 

        if (
            (isset($_POST['seo_global_fb_id'])) &&
            ($_POST['seo_global_fb_id'] != '')
        )
        {
            $seo_global_fb_id = $_POST['seo_global_fb_id'];
        } 

        if (
            (isset($_POST['seo_global_twitter_card'])) &&
            ($_POST['seo_global_twitter_card'] != '')
        )
        {
            $seo_global_twitter_card = $_POST['seo_global_twitter_card'];
        } 

        if (
            (isset($_POST['seo_global_twitter_site'])) &&
            ($_POST['seo_global_twitter_site'] != '')
        )
        {
            $seo_global_twitter_site = $_POST['seo_global_twitter_site'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }
        
        $this->seo_global->seo_global_geo_placename = $seo_global_geo_placename;
        $this->seo_global->seo_global_geo_region = $seo_global_geo_region;
        $this->seo_global->seo_global_geo_position = $seo_global_geo_position;
        $this->seo_global->seo_global_reflang_alternate1 = $seo_global_reflang_alternate1;
        $this->seo_global->seo_global_reflang_alternate2 = $seo_global_reflang_alternate2;
        $this->seo_global->seo_global_og_locale = $seo_global_og_locale;
        $this->seo_global->seo_global_og_site = $seo_global_og_site;
        $this->seo_global->seo_global_og_article_publisher = $seo_global_og_article_publisher;
        $this->seo_global->seo_global_og_author = $seo_global_og_author;
        $this->seo_global->seo_global_fb_id = $seo_global_fb_id;
        $this->seo_global->seo_global_twitter_card = $seo_global_twitter_card;
        $this->seo_global->seo_global_twitter_site = $seo_global_twitter_site;

        $saved = $this->seo_global->save();

        if ($saved)
        {
            $this->addSuccess('The new global SEO data was created', 'Success!');
            $this->redirect('seo');
        }
        else
        {
            $this->addErrors('Model new global SEO data could not be created', 'Error!');
            $this->redirect('seo');
        }
    }


    /**
     * This method receives an AJAX call to verify & relay back to the calling
     * view code if a given page name (which should be unique) is already
     * in use or not
     */
    public function checkPageName()
    {
        $langClass = new DGZ_Translator();
        $lang = $this->getLang();

        if (isset($_POST['pageName']))
        {
            $pageName = $_POST['pageName'];
        }

        $query = "SELECT * FROM seo WHERE seo_page_name = '$pageName'";

        $seo = $this->seo->query($query);

        if ($seo)
        {
            die("<b style='color:red'>&nbsp;&larr;
            ".$langClass->translate($lang, 'seo.php', 'page-name-exists')."</b>");
        }
        else
        {
            //We have to return something, but because in this case we want to take no action 
            //on the form if the pageName is unique, we return a null.
            die(null);
        }

    }


    //-------------------HANDLE GLOBAL DATA----------------

    public function editGlobal($globalId)
    {
        $seo = new Seo_global();
        $data = $seo->getGlobalSeo($globalId);
        $view = DGZ_View::getModuleView('seo', 'editGlobal', $this, 'html');
        $this->setPageTitle('Edit Global Seo');
        $view->show($data);
    }


    //Save global edit here
    public function saveGlobalEdit()
    {
        $seo_global_id = '';
        $seo_global_geo_placename = '';
        $seo_global_geo_region = '';
        $seo_global_geo_position = '';
        $seo_global_reflang_alternate1 = '';
        $seo_global_reflang_alternate2 = '';
        $seo_global_og_locale = '';
        $seo_global_og_site = '';
        $seo_global_og_article_publisher = '';
        $seo_global_og_author = '';
        $seo_global_fb_id = '';
        $seo_global_twitter_card = '';
        $seo_global_twitter_site = '';

        if (
            (isset($_POST['seo_global_id'])) &&
            ($_POST['seo_global_id'] != '')
        )
        {
            $seo_global_id = $_POST['seo_global_id'];
        }

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_region'])) &&
            ($_POST['seo_global_geo_region'] != '')
        )
        {
            $seo_global_geo_region = $_POST['seo_global_geo_region'];
        }

        if (
            (isset($_POST['seo_global_geo_position'])) &&
            ($_POST['seo_global_geo_position'] != '')
        )
        {
            $seo_global_geo_position = $_POST['seo_global_geo_position']; 
        }

        if (
            (isset($_POST['seo_global_reflang_alternate1'])) &&
            ($_POST['seo_global_reflang_alternate1'] != '')
        )
        {
            $seo_global_reflang_alternate1 = $_POST['seo_global_reflang_alternate1'];
        }

        if (
            (isset($_POST['seo_global_reflang_alternate2'])) &&
            ($_POST['seo_global_reflang_alternate2'] != '')
        )
        {
            $seo_global_reflang_alternate2 = $_POST['seo_global_reflang_alternate2'];
        }

        if (
            (isset($_POST['seo_global_og_locale'])) &&
            ($_POST['seo_global_og_locale'] != '')
        )
        {
            $seo_global_og_locale = $_POST['seo_global_og_locale'];
        }

        if (
            (isset($_POST['seo_global_og_site'])) &&
            ($_POST['seo_global_og_site'] != '')
        )
        {
            $seo_global_og_site = $_POST['seo_global_og_site']; 
        }

        if (
            (isset($_POST['seo_global_og_article_publisher'])) &&
            ($_POST['seo_global_og_article_publisher'] != '')
        )
        {
            $seo_global_og_article_publisher = $_POST['seo_global_og_article_publisher'];
        } 

        if (
            (isset($_POST['seo_global_og_author'])) &&
            ($_POST['seo_global_og_author'] != '')
        )
        {
            $seo_global_og_author = $_POST['seo_global_og_author'];
        } 

        if (
            (isset($_POST['seo_global_fb_id'])) &&
            ($_POST['seo_global_fb_id'] != '')
        )
        {
            $seo_global_fb_id = $_POST['seo_global_fb_id'];
        } 

        if (
            (isset($_POST['seo_global_twitter_card'])) &&
            ($_POST['seo_global_twitter_card'] != '')
        )
        {
            $seo_global_twitter_card = $_POST['seo_global_twitter_card'];
        } 

        if (
            (isset($_POST['seo_global_twitter_site'])) &&
            ($_POST['seo_global_twitter_site'] != '')
        )
        {
            $seo_global_twitter_site = $_POST['seo_global_twitter_site'];
        }
        
        if ($seo_global_id != '')
        {
            $this->seo_global->seo_global_id = $seo_global_id;
            $this->seo_global->seo_global_geo_placename = $seo_global_geo_placename;
            $this->seo_global->seo_global_geo_region = $seo_global_geo_region;
            $this->seo_global->seo_global_geo_position = $seo_global_geo_position;
            $this->seo_global->seo_global_reflang_alternate1 = $seo_global_reflang_alternate1;
            $this->seo_global->seo_global_reflang_alternate2 = $seo_global_reflang_alternate2;
            $this->seo_global->seo_global_og_locale = $seo_global_og_locale;
            $this->seo_global->seo_global_og_site = $seo_global_og_site;
            $this->seo_global->seo_global_og_article_publisher = $seo_global_og_article_publisher;
            $this->seo_global->seo_global_og_author = $seo_global_og_author;
            $this->seo_global->seo_global_fb_id = $seo_global_fb_id;
            $this->seo_global->seo_global_twitter_card = $seo_global_twitter_card;
            $this->seo_global->seo_global_twitter_site = $seo_global_twitter_site;

            $where = ['seo_global_id' => $seo_global_id];

            $updated = $this->seo_global->update($where);

            if ($updated)
            {
                $this->addSuccess('The global SEO data was updated', 'Success!');
                $this->redirect('seo');
            }
            else
            {
                $this->addErrors('Model seo_global could not be updated', 'Error!');
                $this->redirect('seo');
            }
        }
        else
        {
            $this->addErrors('The ID of that global record could not be verified', 'Error!');
            $this->redirect('seo');
        }                                  
    }


    public function addGlobal()
    {
        $view = DGZ_View::getModuleView('seo', 'addGlobal', $this, 'html');
        $this->setPageTitle('Create Gloal SEO Data');
        $view->show(); 
    }



    public function saveNewGlobal() 
    {
        $seo_global_geo_placename = '';
        $seo_global_geo_region = '';
        $seo_global_geo_position = '';
        $seo_global_reflang_alternate1 = '';
        $seo_global_reflang_alternate2 = '';
        $seo_global_og_locale = '';
        $seo_global_og_site = '';
        $seo_global_og_article_publisher = '';
        $seo_global_og_author = '';
        $seo_global_fb_id = '';
        $seo_global_twitter_card = '';
        $seo_global_twitter_site = '';

        if (
            (isset($_POST['seo_global_geo_placename'])) &&
            ($_POST['seo_global_geo_placename'] != '')
        )
        {
            $seo_global_geo_placename = $_POST['seo_global_geo_placename'];
        }

        if (
            (isset($_POST['seo_global_geo_region'])) &&
            ($_POST['seo_global_geo_region'] != '')
        )
        {
            $seo_global_geo_region = $_POST['seo_global_geo_region'];
        }

        if (
            (isset($_POST['seo_global_geo_position'])) &&
            ($_POST['seo_global_geo_position'] != '')
        )
        {
            $seo_global_geo_position = $_POST['seo_global_geo_position']; 
        }

        if (
            (isset($_POST['seo_global_reflang_alternate1'])) &&
            ($_POST['seo_global_reflang_alternate1'] != '')
        )
        {
            $seo_global_reflang_alternate1 = $_POST['seo_global_reflang_alternate1'];
        }

        if (
            (isset($_POST['seo_global_reflang_alternate2'])) &&
            ($_POST['seo_global_reflang_alternate2'] != '')
        )
        {
            $seo_global_reflang_alternate2 = $_POST['seo_global_reflang_alternate2'];
        }

        if (
            (isset($_POST['seo_global_og_locale'])) &&
            ($_POST['seo_global_og_locale'] != '')
        )
        {
            $seo_global_og_locale = $_POST['seo_global_og_locale'];
        }

        if (
            (isset($_POST['seo_global_og_site'])) &&
            ($_POST['seo_global_og_site'] != '')
        )
        {
            $seo_global_og_site = $_POST['seo_global_og_site']; 
        }

        if (
            (isset($_POST['seo_global_og_article_publisher'])) &&
            ($_POST['seo_global_og_article_publisher'] != '')
        )
        {
            $seo_global_og_article_publisher = $_POST['seo_global_og_article_publisher'];
        } 

        if (
            (isset($_POST['seo_global_og_author'])) &&
            ($_POST['seo_global_og_author'] != '')
        )
        {
            $seo_global_og_author = $_POST['seo_global_og_author'];
        } 

        if (
            (isset($_POST['seo_global_fb_id'])) &&
            ($_POST['seo_global_fb_id'] != '')
        )
        {
            $seo_global_fb_id = $_POST['seo_global_fb_id'];
        } 

        if (
            (isset($_POST['seo_global_twitter_card'])) &&
            ($_POST['seo_global_twitter_card'] != '')
        )
        {
            $seo_global_twitter_card = $_POST['seo_global_twitter_card'];
        } 

        if (
            (isset($_POST['seo_global_twitter_site'])) &&
            ($_POST['seo_global_twitter_site'] != '')
        )
        {
            $seo_global_twitter_site = $_POST['seo_global_twitter_site'];
        }
        
        $this->seo_global->seo_global_geo_placename = $seo_global_geo_placename;
        $this->seo_global->seo_global_geo_region = $seo_global_geo_region;
        $this->seo_global->seo_global_geo_position = $seo_global_geo_position;
        $this->seo_global->seo_global_reflang_alternate1 = $seo_global_reflang_alternate1;
        $this->seo_global->seo_global_reflang_alternate2 = $seo_global_reflang_alternate2;
        $this->seo_global->seo_global_og_locale = $seo_global_og_locale;
        $this->seo_global->seo_global_og_site = $seo_global_og_site;
        $this->seo_global->seo_global_og_article_publisher = $seo_global_og_article_publisher;
        $this->seo_global->seo_global_og_author = $seo_global_og_author;
        $this->seo_global->seo_global_fb_id = $seo_global_fb_id;
        $this->seo_global->seo_global_twitter_card = $seo_global_twitter_card;
        $this->seo_global->seo_global_twitter_site = $seo_global_twitter_site;

        $saved = $this->seo_global->save();

        if ($saved)
        {
            $this->addSuccess('The new global SEO data was created', 'Success!');
            $this->redirect('seo');
        }
        else
        {
            $this->addErrors('Model new global SEO data could not be created', 'Error!');
            $this->redirect('seo');
        }
    }



    //Add func to save new Global data


    public function getGlobalSeoData()
    {
        $globalSeo = new Seo_global();
        return $globalSeo->getAll();
    }

		
}


  
	
	
	
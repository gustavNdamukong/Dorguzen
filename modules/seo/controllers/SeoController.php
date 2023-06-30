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
        $seoData = $seo->getById($pageId);
        $view = DGZ_View::getModuleView('seo', 'pageDetail', $this, 'html');
        $this->setPageTitle('Seo Page Detailed View');
        $view->show($seoData);
    }



    public function globalDetail($recordId)
    {
        $globalSeo = new Seo_global();
        
        $seoData = $globalSeo->getById($recordId);
        $view = DGZ_View::getModuleView('seo', 'globalDetail', $this, 'html');
        $this->setPageTitle('Seo Global Detailed View');
        $view->show($seoData);
    }



    public function addPage()
    {
        $view = DGZ_View::getModuleView('seo', 'addPage', $this, 'html');
        $this->setPageTitle('Create SEO page');
        $view->show(); 
    }



    public function saveNewPage()
    { 
        //The page name is the only field we make mandatory.
        //Every other field is optional
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
        } 

        if (
            (isset($_POST['seo_meta_desc_fre'])) &&
            ($_POST['seo_meta_desc_fre'] != '')
        )
        {
            $seo_meta_desc_fre = $_POST['seo_meta_desc_fre'];
        }

        if (
            (isset($_POST['seo_meta_desc_es'])) &&
            ($_POST['seo_meta_desc_es'] != '')
        )
        {
            $seo_meta_desc_es = $_POST['seo_meta_desc_es']; 
        }  

        if (
            (isset($_POST['seo_dynamic'])) &&
            ($_POST['seo_dynamic'] != '')
        )
        {
            $seo_dynamic = $_POST['seo_dynamic'];
        } 

        if (
            (isset($_POST['seo_keywords_en'])) &&
            ($_POST['seo_keywords_en'] != '')
        )
        {
            $seo_keywords_en = $_POST['seo_keywords_en'];
        } 

        if (
            (isset($_POST['seo_keywords_fre'])) &&
            ($_POST['seo_keywords_fre'] != '')
        )
        {
            $seo_keywords_fre = $_POST['seo_keywords_fre'];
        } 

        if (
            (isset($_POST['seo_keywords_es'])) &&
            ($_POST['seo_keywords_es'] != '')
        )
        {
            $seo_keywords_es = $_POST['seo_keywords_es'];
        } 

        if (
            (isset($_POST['seo_canonical_href'])) &&
            ($_POST['seo_canonical_href'] != '')
        )
        {
            $seo_canonical_href = $_POST['seo_canonical_href'];
        } 

        if (
            (isset($_POST['seo_no_index'])) &&
            ($_POST['seo_no_index'] != '')
        )
        {
            $seo_no_index = $_POST['seo_no_index'];
        } 

        if (
            (isset($_POST['seo_h1_text_en'])) &&
            ($_POST['seo_h1_text_en'] != '')
        )
        {
            $seo_h1_text_en = $_POST['seo_h1_text_en']; 
        }

        if (
            (isset($_POST['seo_h1_text_fre'])) &&
            ($_POST['seo_h1_text_fre'] != '')
        )
        {
            $seo_h1_text_fre = $_POST['seo_h1_text_fre'];  
        }

        if (
            (isset($_POST['seo_h1_text_es'])) &&
            ($_POST['seo_h1_text_es'] != '')
        )
        {
            $seo_h1_text_es = $_POST['seo_h1_text_es'];
        }  

        if (
            (isset($_POST['seo_h2_text_en'])) &&
            ($_POST['seo_h2_text_en'] != '')
        )
        {
            $seo_h2_text_en = $_POST['seo_h2_text_en'];  
        }

        if (
            (isset($_POST['seo_h2_text_fre'])) &&
            ($_POST['seo_h2_text_fre'] != '')
        )
        {
            $seo_h2_text_fre = $_POST['seo_h2_text_fre'];  
        }

        if (
            (isset($_POST['seo_h2_text_es'])) &&
            ($_POST['seo_h2_text_es'] != '')
        )
        {
            $seo_h2_text_es = $_POST['seo_h2_text_es'];
        } 

        if (
            (isset($_POST['seo_page_content_en'])) &&
            ($_POST['seo_page_content_en'] != '')
        )
        {
            $seo_page_content_en = $_POST['seo_page_content_en'];  
        }

        if (
            (isset($_POST['seo_page_content_fre'])) &&
            ($_POST['seo_page_content_fre'] != '')
        )
        {
            $seo_page_content_fre = $_POST['seo_page_content_fre']; 
        }

        if (
            (isset($_POST['seo_page_content_es'])) &&
            ($_POST['seo_page_content_es'] != '')
        )
        {
            $seo_page_content_es = $_POST['seo_page_content_es'];
        } 

        if (
            (isset($_POST['seo_og_title_en'])) &&
            ($_POST['seo_og_title_en'] != '')
        )
        {
            $seo_og_title_en = $_POST['seo_og_title_en']; 
        }

        if (
            (isset($_POST['seo_og_title_fre'])) &&
            ($_POST['seo_og_title_fre'] != '')
        )
        {
            $seo_og_title_fre = $_POST['seo_og_title_fre'];  
        }

        if (
            (isset($_POST['seo_og_title_es'])) &&
            ($_POST['seo_og_title_es'] != '')
        )
        {
            $seo_og_title_es = $_POST['seo_og_title_es'];  
        } 

        if (
            (isset($_POST['seo_og_desc_en'])) &&
            ($_POST['seo_og_desc_en'] != '')
        )
        {
            $seo_og_desc_en = $_POST['seo_og_desc_en']; 
        }

        if (
            (isset($_POST['seo_og_desc_fre'])) &&
            ($_POST['seo_og_desc_fre'] != '')
        )
        {
            $seo_og_desc_fre = $_POST['seo_og_desc_fre']; 
        }

        if (
            (isset($_POST['seo_og_desc_es'])) &&
            ($_POST['seo_og_desc_es'] != '')
        )
        {
            $seo_og_desc_es = $_POST['seo_og_desc_es'];  
        } 

        if (
            (isset($_POST['seo_og_image'])) &&
            ($_POST['seo_og_image'] != '')
        )
        {
            $seo_og_image = $_POST['seo_og_image'];
        }

        if (
            (isset($_POST['seo_og_image_width'])) &&
            ($_POST['seo_og_image_width'] != '')
        )
        {
            $seo_og_image_width = $_POST['seo_og_image_width'];
        }

        if (
            (isset($_POST['seo_og_image_height'])) &&
            ($_POST['seo_og_image_height'] != '')
        )
        {
            $seo_og_image_height = $_POST['seo_og_image_height'];
        } 

        if (
            (isset($_POST['seo_og_image_secure_url'])) &&
            ($_POST['seo_og_image_secure_url'] != '')
        )
        {
            $seo_og_image_secure_url = $_POST['seo_og_image_secure_url'];
        }  

        if (
            (isset($_POST['seo_og_type_en'])) &&
            ($_POST['seo_og_type_en'] != '')
        )
        {
            $seo_og_type_en = $_POST['seo_og_type_en']; 
        }

        if (
            (isset($_POST['seo_og_type_fre'])) &&
            ($_POST['seo_og_type_fre'] != '')
        )
        {
            $seo_og_type_fre = $_POST['seo_og_type_fre']; 
        }

        if (
            (isset($_POST['seo_og_type_es'])) &&
            ($_POST['seo_og_type_es'] != '')
        )
        {
            $seo_og_type_es = $_POST['seo_og_type_es'];
        } 

        if (
            (isset($_POST['seo_og_url'])) &&
            ($_POST['seo_og_url'] != '')
        )
        {
            $seo_og_url = $_POST['seo_og_url'];
        }

        if (
            (isset($_POST['seo_og_video'])) &&
            ($_POST['seo_og_video'] != '')
        )
        {
            $seo_og_video = $_POST['seo_og_video'];
        }

        if (
            (isset($_POST['seo_twitter_title_en'])) &&
            ($_POST['seo_twitter_title_en'] != '')
        )
        {
            $seo_twitter_title_en = $_POST['seo_twitter_title_en'];  
        }

        if (
            (isset($_POST['seo_twitter_title_fre'])) &&
            ($_POST['seo_twitter_title_fre'] != '')
        )
        {
            $seo_twitter_title_fre = $_POST['seo_twitter_title_fre'];  
        }

        if (
            (isset($_POST['seo_twitter_title_es'])) &&
            ($_POST['seo_twitter_title_es'] != '')
        )
        {
            $seo_twitter_title_es = $_POST['seo_twitter_title_es'];
        }  

        if (
            (isset($_POST['seo_twitter_desc_en'])) &&
            ($_POST['seo_twitter_desc_en'] != '')
        )
        {
            $seo_twitter_desc_en = $_POST['seo_twitter_desc_en'];
        }

        if (
            (isset($_POST['seo_twitter_desc_fre'])) &&
            ($_POST['seo_twitter_desc_fre'] != '')
        )
        {
            $seo_twitter_desc_fre = $_POST['seo_twitter_desc_fre'];
        }

        if (
            (isset($_POST['seo_twitter_desc_es'])) &&
            ($_POST['seo_twitter_desc_es'] != '')
        )
        {
            $seo_twitter_desc_es = $_POST['seo_twitter_desc_es'];
        }

        if (
            (isset($_POST['seo_twitter_image'])) &&
            ($_POST['seo_twitter_image'] != '')
        )
        {
            $seo_twitter_image = $_POST['seo_twitter_image'];
        }


        if ($error == '')
        {
            $this->seo->seo_page_name               = $seo_page_name;
            $this->seo->seo_meta_title_en           = $seo_meta_title_en;
            $this->seo->seo_meta_title_fre          = $seo_meta_title_fre;
            $this->seo->seo_meta_title_es           = $seo_meta_title_es;
            $this->seo->seo_meta_desc_en            = $seo_meta_desc_en;
            $this->seo->seo_meta_desc_fre           = $seo_meta_desc_fre;
            $this->seo->seo_meta_desc_es            = $seo_meta_desc_es;
            $this->seo->seo_dynamic                 = $seo_dynamic;
            $this->seo->seo_keywords_en             = $seo_keywords_en;
            $this->seo->seo_keywords_fre            = $seo_keywords_fre;
            $this->seo->seo_keywords_es             = $seo_keywords_es;
            $this->seo->seo_canonical_href          = $seo_canonical_href;
            $this->seo->seo_no_index                = $seo_no_index;
            $this->seo->seo_h1_text_en              = $seo_h1_text_en;
            $this->seo->seo_h1_text_fre             = $seo_h1_text_fre;
            $this->seo->seo_h1_text_es              = $seo_h1_text_es;
            $this->seo->seo_h2_text_en              = $seo_h2_text_en;
            $this->seo->seo_h2_text_fre             = $seo_h2_text_fre;
            $this->seo->seo_h2_text_es              = $seo_h2_text_es;
            $this->seo->seo_page_content_en         = $seo_page_content_en;
            $this->seo->seo_page_content_fre        = $seo_page_content_fre;
            $this->seo->seo_page_content_es         = $seo_page_content_es;
            $this->seo->seo_og_title_en             = $seo_og_title_en;
            $this->seo->seo_og_title_fre            = $seo_og_title_fre;
            $this->seo->seo_og_title_es             = $seo_og_title_es;
            $this->seo->seo_og_desc_en              = $seo_og_desc_en;
            $this->seo->seo_og_desc_fre             = $seo_og_desc_fre;
            $this->seo->seo_og_desc_es              = $seo_og_desc_es;
            $this->seo->seo_og_image                = $seo_og_image;
            $this->seo->seo_og_image_width          = $seo_og_image_width;
            $this->seo->seo_og_image_height         = $seo_og_image_height;
            $this->seo->seo_og_image_secure_url     = $seo_og_image_secure_url;
            $this->seo->seo_og_type_en              = $seo_og_type_en;
            $this->seo->seo_og_type_fre             = $seo_og_type_fre;
            $this->seo->seo_og_type_es              = $seo_og_type_es;
            $this->seo->seo_og_url                  = $seo_og_url;
            $this->seo->seo_og_video                = $seo_og_video;
            $this->seo->seo_twitter_title_en        = $seo_twitter_title_en;
            $this->seo->seo_twitter_title_fre       = $seo_twitter_title_fre;
            $this->seo->seo_twitter_title_es        = $seo_twitter_title_es;
            $this->seo->seo_twitter_desc_en         = $seo_twitter_desc_en;
            $this->seo->seo_twitter_desc_fre        = $seo_twitter_desc_fre;
            $this->seo->seo_twitter_desc_es         = $seo_twitter_desc_es;
            $this->seo->seo_twitter_image           = $seo_twitter_image;

            $saved = $this->seo->save();

            if ($saved)
            {
                $this->addSuccess('The new SEO data was created', 'Success!');
                $this->redirect('seo');
            }
            else
            {
                $this->addErrors('The new SEO data could not be created', 'Error!');
                $this->redirect('seo');
            }
        }
        else
        {
            $this->addErrors($error, 'Error!');
            $this->redirect('seo');
        } 
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
    {//$this->showArray($_POST);
        //We make two fields here mandatory; the ID field, because it's an update. 
        //We also make the name field mandatory. Every other field is optional
        $error = '';

        $seo_id = '';
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
            (isset($_POST['seo_id'])) &&
            ($_POST['seo_id'] != '')
        )
        {
            $seo_id = $_POST['seo_id'];
        }
        else
        {
            $error .= "Record ID not found <br />";
        }

        if (
            (isset($_POST['seo_page_name'])) &&
            ($_POST['seo_page_name'] != '')
        )
        {
            $seo_page_name = $_POST['seo_page_name'];
        }
        else
        {
            $error .= "Please you have to provide the name of the page <br />";
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
        } 

        if (
            (isset($_POST['seo_meta_desc_fre'])) &&
            ($_POST['seo_meta_desc_fre'] != '')
        )
        {
            $seo_meta_desc_fre = $_POST['seo_meta_desc_fre'];
        }

        if (
            (isset($_POST['seo_meta_desc_es'])) &&
            ($_POST['seo_meta_desc_es'] != '')
        )
        {
            $seo_meta_desc_es = $_POST['seo_meta_desc_es']; 
        }  

        if (
            (isset($_POST['seo_dynamic'])) &&
            ($_POST['seo_dynamic'] != '')
        )
        {
            $seo_dynamic = $_POST['seo_dynamic'];
        } 

        if (
            (isset($_POST['seo_keywords_en'])) &&
            ($_POST['seo_keywords_en'] != '')
        )
        {
            $seo_keywords_en = $_POST['seo_keywords_en'];
        } 

        if (
            (isset($_POST['seo_keywords_fre'])) &&
            ($_POST['seo_keywords_fre'] != '')
        )
        {
            $seo_keywords_fre = $_POST['seo_keywords_fre'];
        } 

        if (
            (isset($_POST['seo_keywords_es'])) &&
            ($_POST['seo_keywords_es'] != '')
        )
        {
            $seo_keywords_es = $_POST['seo_keywords_es'];
        } 

        if (
            (isset($_POST['seo_canonical_href'])) &&
            ($_POST['seo_canonical_href'] != '')
        )
        {
            $seo_canonical_href = $_POST['seo_canonical_href'];
        } 

        if (
            (isset($_POST['seo_no_index'])) &&
            ($_POST['seo_no_index'] != '')
        )
        {
            $seo_no_index = $_POST['seo_no_index'];
        } 

        if (
            (isset($_POST['seo_h1_text_en'])) &&
            ($_POST['seo_h1_text_en'] != '')
        )
        {
            $seo_h1_text_en = $_POST['seo_h1_text_en']; 
        }

        if (
            (isset($_POST['seo_h1_text_fre'])) &&
            ($_POST['seo_h1_text_fre'] != '')
        )
        {
            $seo_h1_text_fre = $_POST['seo_h1_text_fre'];  
        }

        if (
            (isset($_POST['seo_h1_text_es'])) &&
            ($_POST['seo_h1_text_es'] != '')
        )
        {
            $seo_h1_text_es = $_POST['seo_h1_text_es'];
        }  

        if (
            (isset($_POST['seo_h2_text_en'])) &&
            ($_POST['seo_h2_text_en'] != '')
        )
        {
            $seo_h2_text_en = $_POST['seo_h2_text_en'];  
        }

        if (
            (isset($_POST['seo_h2_text_fre'])) &&
            ($_POST['seo_h2_text_fre'] != '')
        )
        {
            $seo_h2_text_fre = $_POST['seo_h2_text_fre'];  
        }

        if (
            (isset($_POST['seo_h2_text_es'])) &&
            ($_POST['seo_h2_text_es'] != '')
        )
        {
            $seo_h2_text_es = $_POST['seo_h2_text_es'];
        } 

        if (
            (isset($_POST['seo_page_content_en'])) &&
            ($_POST['seo_page_content_en'] != '')
        )
        {
            $seo_page_content_en = $_POST['seo_page_content_en'];  
        }

        if (
            (isset($_POST['seo_page_content_fre'])) &&
            ($_POST['seo_page_content_fre'] != '')
        )
        {
            $seo_page_content_fre = $_POST['seo_page_content_fre']; 
        }

        if (
            (isset($_POST['seo_page_content_es'])) &&
            ($_POST['seo_page_content_es'] != '')
        )
        {
            $seo_page_content_es = $_POST['seo_page_content_es'];
        } 

        if (
            (isset($_POST['seo_og_title_en'])) &&
            ($_POST['seo_og_title_en'] != '')
        )
        {
            $seo_og_title_en = $_POST['seo_og_title_en']; 
        }

        if (
            (isset($_POST['seo_og_title_fre'])) &&
            ($_POST['seo_og_title_fre'] != '')
        )
        {
            $seo_og_title_fre = $_POST['seo_og_title_fre'];  
        }

        if (
            (isset($_POST['seo_og_title_es'])) &&
            ($_POST['seo_og_title_es'] != '')
        )
        {
            $seo_og_title_es = $_POST['seo_og_title_es'];  
        } 

        if (
            (isset($_POST['seo_og_desc_en'])) &&
            ($_POST['seo_og_desc_en'] != '')
        )
        {
            $seo_og_desc_en = $_POST['seo_og_desc_en']; 
        }

        if (
            (isset($_POST['seo_og_desc_fre'])) &&
            ($_POST['seo_og_desc_fre'] != '')
        )
        {
            $seo_og_desc_fre = $_POST['seo_og_desc_fre']; 
        }

        if (
            (isset($_POST['seo_og_desc_es'])) &&
            ($_POST['seo_og_desc_es'] != '')
        )
        {
            $seo_og_desc_es = $_POST['seo_og_desc_es'];  
        } 

        if (
            (isset($_POST['seo_og_image'])) &&
            ($_POST['seo_og_image'] != '')
        )
        {
            $seo_og_image = $_POST['seo_og_image'];
        }

        if (
            (isset($_POST['seo_og_image_width'])) &&
            ($_POST['seo_og_image_width'] != '')
        )
        {
            $seo_og_image_width = $_POST['seo_og_image_width'];
        }

        if (
            (isset($_POST['seo_og_image_height'])) &&
            ($_POST['seo_og_image_height'] != '')
        )
        {
            $seo_og_image_height = $_POST['seo_og_image_height'];
        } 

        if (
            (isset($_POST['seo_og_image_secure_url'])) &&
            ($_POST['seo_og_image_secure_url'] != '')
        )
        {
            $seo_og_image_secure_url = $_POST['seo_og_image_secure_url'];
        }  

        if (
            (isset($_POST['seo_og_type_en'])) &&
            ($_POST['seo_og_type_en'] != '')
        )
        {
            $seo_og_type_en = $_POST['seo_og_type_en']; 
        }

        if (
            (isset($_POST['seo_og_type_fre'])) &&
            ($_POST['seo_og_type_fre'] != '')
        )
        {
            $seo_og_type_fre = $_POST['seo_og_type_fre']; 
        }

        if (
            (isset($_POST['seo_og_type_es'])) &&
            ($_POST['seo_og_type_es'] != '')
        )
        {
            $seo_og_type_es = $_POST['seo_og_type_es'];
        } 

        if (
            (isset($_POST['seo_og_url'])) &&
            ($_POST['seo_og_url'] != '')
        )
        {
            $seo_og_url = $_POST['seo_og_url'];
        }

        if (
            (isset($_POST['seo_og_video'])) &&
            ($_POST['seo_og_video'] != '')
        )
        {
            $seo_og_video = $_POST['seo_og_video'];
        }

        if (
            (isset($_POST['seo_twitter_title_en'])) &&
            ($_POST['seo_twitter_title_en'] != '')
        )
        {
            $seo_twitter_title_en = $_POST['seo_twitter_title_en'];  
        }

        if (
            (isset($_POST['seo_twitter_title_fre'])) &&
            ($_POST['seo_twitter_title_fre'] != '')
        )
        {
            $seo_twitter_title_fre = $_POST['seo_twitter_title_fre'];  
        }

        if (
            (isset($_POST['seo_twitter_title_es'])) &&
            ($_POST['seo_twitter_title_es'] != '')
        )
        {
            $seo_twitter_title_es = $_POST['seo_twitter_title_es'];
        }  

        if (
            (isset($_POST['seo_twitter_desc_en'])) &&
            ($_POST['seo_twitter_desc_en'] != '')
        )
        {
            $seo_twitter_desc_en = $_POST['seo_twitter_desc_en'];
        }

        if (
            (isset($_POST['seo_twitter_desc_fre'])) &&
            ($_POST['seo_twitter_desc_fre'] != '')
        )
        {
            $seo_twitter_desc_fre = $_POST['seo_twitter_desc_fre'];
        }

        if (
            (isset($_POST['seo_twitter_desc_es'])) &&
            ($_POST['seo_twitter_desc_es'] != '')
        )
        {
            $seo_twitter_desc_es = $_POST['seo_twitter_desc_es'];
        }

        if (
            (isset($_POST['seo_twitter_image'])) &&
            ($_POST['seo_twitter_image'] != '')
        )
        {
            $seo_twitter_image = $_POST['seo_twitter_image'];
        }


        if ($error == '')
        {
            $this->seo->seo_id                      = $seo_id;
            $this->seo->seo_page_name               = $seo_page_name;
            $this->seo->seo_meta_title_en           = $seo_meta_title_en;
            $this->seo->seo_meta_title_fre          = $seo_meta_title_fre;
            $this->seo->seo_meta_title_es           = $seo_meta_title_es;
            $this->seo->seo_meta_desc_en            = $seo_meta_desc_en;
            $this->seo->seo_meta_desc_fre           = $seo_meta_desc_fre;
            $this->seo->seo_meta_desc_es            = $seo_meta_desc_es;
            $this->seo->seo_dynamic                 = $seo_dynamic;
            $this->seo->seo_keywords_en             = $seo_keywords_en;
            $this->seo->seo_keywords_fre            = $seo_keywords_fre;
            $this->seo->seo_keywords_es             = $seo_keywords_es;
            $this->seo->seo_canonical_href          = $seo_canonical_href;
            $this->seo->seo_no_index                = $seo_no_index;
            $this->seo->seo_h1_text_en              = $seo_h1_text_en;
            $this->seo->seo_h1_text_fre             = $seo_h1_text_fre;
            $this->seo->seo_h1_text_es              = $seo_h1_text_es;
            $this->seo->seo_h2_text_en              = $seo_h2_text_en;
            $this->seo->seo_h2_text_fre             = $seo_h2_text_fre;
            $this->seo->seo_h2_text_es              = $seo_h2_text_es;
            $this->seo->seo_page_content_en         = $seo_page_content_en;
            $this->seo->seo_page_content_fre        = $seo_page_content_fre;
            $this->seo->seo_page_content_es         = $seo_page_content_es;
            $this->seo->seo_og_title_en             = $seo_og_title_en;
            $this->seo->seo_og_title_fre            = $seo_og_title_fre;
            $this->seo->seo_og_title_es             = $seo_og_title_es;
            $this->seo->seo_og_desc_en              = $seo_og_desc_en;
            $this->seo->seo_og_desc_fre             = $seo_og_desc_fre;
            $this->seo->seo_og_desc_es              = $seo_og_desc_es;
            $this->seo->seo_og_image                = $seo_og_image;
            $this->seo->seo_og_image_width          = $seo_og_image_width;
            $this->seo->seo_og_image_height         = $seo_og_image_height;
            $this->seo->seo_og_image_secure_url     = $seo_og_image_secure_url;
            $this->seo->seo_og_type_en              = $seo_og_type_en;
            $this->seo->seo_og_type_fre             = $seo_og_type_fre;
            $this->seo->seo_og_type_es              = $seo_og_type_es;
            $this->seo->seo_og_url                  = $seo_og_url;
            $this->seo->seo_og_video                = $seo_og_video;
            $this->seo->seo_twitter_title_en        = $seo_twitter_title_en;
            $this->seo->seo_twitter_title_fre       = $seo_twitter_title_fre;
            $this->seo->seo_twitter_title_es        = $seo_twitter_title_es;
            $this->seo->seo_twitter_desc_en         = $seo_twitter_desc_en;
            $this->seo->seo_twitter_desc_fre        = $seo_twitter_desc_fre;
            $this->seo->seo_twitter_desc_es         = $seo_twitter_desc_es;
            $this->seo->seo_twitter_image           = $seo_twitter_image;

            $updated = $this->seo->update();

            if ($updated)
            {
                $this->addSuccess('The page SEO data was updated', 'Success!');
                $this->redirect('seo');
            }
            else
            {
                $this->addErrors('Something went wrong', 'Error!');
                $this->redirect('seo');
            }
        }
        else
        {
            $this->addErrors($error, 'Error!');

            if ((isset($seo_id)) && ($seo_id != ''))
            {
                $this->redirect('seo', 'editPageSeo', ['pageId' => $seo_id]);
            }
            else
            {
                $this->redirect('seo');
            }
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


    public function deletePage() 
    {
        if (
            (isset($_POST['pageId'])) &&
            ($_POST['pageId'] != '')
        )
        {
            $pageId = $_POST['pageId'];
            $where = ['seo_id' => $pageId];
            $deleted = $this->seo->deleteWhere($where);

            if ($deleted)
            {
                $this->addSuccess('The page SEO data was deleted', 'Success!');
                $this->redirect('seo');
            }
            else
            {
                $this->addErrors('Could not delete page SEO data', 'Error!');
                $this->redirect('seo');
            }
        }
        else
        {
            $this->addErrors('Record ID not found', 'Error!');
            $this->redirect('seo');
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


    public function getGlobalSeoData()
    {
        $globalSeo = new Seo_global();
        return $globalSeo->getAll();
    }


    public function deleteGlobal() 
    {
        if (
            (isset($_POST['recordId'])) &&
            ($_POST['recordId'] != '')
        )
        {
            $recordId = $_POST['recordId'];
            $where = ['seo_global_id' => $recordId];
            $deleted = $this->seo_global->deleteWhere($where);

            if ($deleted)
            {
                $this->addSuccess('The global SEO data was deleted', 'Success!');
                $this->redirect('seo');
            }
            else
            {
                $this->addErrors('Could not delete global SEO data', 'Error!');
                $this->redirect('seo');
            }
        }
        else
        {
            $this->addErrors('Record ID not found', 'Error!');
            $this->redirect('seo');
        }
    }	
}


  
	
	
	
<?php
namespace DGZ_library;


use Exception;
use ReflectionClass;
use middleware\Middleware;
use Locations;
use Products;
//use Favs;
//use Product_categories;
use configs\Config;
use modules\seo\controllers\SeoController;

/**
 * Description of HtmlPage
 *
 * @author Gustav
 */
abstract class DGZ_Controller implements DGZ_Displayable {

    /**
     * @var string The default action of this page if one isn't specified in the URL.
     */
    protected $defaultAction;

    /**
     * @var string The page format, e.g. 'html', 'json', 'xml'
     */
    protected $format;



    /**
     * @var string The page title for every view file.
     */
    protected $pageTitle = '';




    /**
     * @var string The name for the specific view file.
     */
    protected $viewName = '';


    /**
     * @var bool whether or not to show an image slider for every view file.
     */
    protected $showImageSlider = false;



    /**
     * @var string The name of the application
     */
    protected $appName;


    /**
     * @var string The layout for every view file.
     * @var string The name of the menu to be used on this page. Defaults to 'main'
     */
    protected $defaultLayout;


    /**
     * @var string The name of the menu to be used on this page. Defaults to 'main'
     */
    protected $defaultLayoutDirectory;



    protected $useFullLayout = true;


    /**
     * @var array An array of meta tags containing meta data shared by all views, to be injected via the layout template.
     */
    protected $globalSeoData;

    /**
     * @var array An array of meta tags containing meta data to be injected into the specific view about to be displayed.
     */
    protected $metadata;

    /**
     * @var array containing data for the body of the current view file eg h1 text, h2 text, body (text) content.
     */
    protected $bodySeoData;


    /**
     * @var array An array of custom CSS stylesheets which need to be included on this page.
     */
    protected $styles;

    /**
     * @var array An array of custom Javascript files which need to be included on this page.
     */
    protected $scripts;

    /**
     * @var array An array of information messages to be shown to the user
     */
    protected $notices;

    /**
     * @var array An array of success messages to be shown to the user
     */
    protected $success;

    /**
     * @var array An array of warning messages to be shown to the user
     */
    protected $warnings;


    /**
     * @var array An array of error messages to be shown to the user
     */
    protected $errors;



    /**
     * @var array An array of exception messages to be shown to the user
     */
    protected $exceptions;

    /**
     * @var object \DGZ_library\DGZ_Application An object which holds the detail of this application's configuration
     */
    protected $application;


    /**
     * Contains the time when the page was loaded.
     */
    protected $startTime;


    /**
     * @var string Contains the current redirection URL to be set when the action completes.
     */
    protected $redirectToUrl;


    /**
     * @var object \configs\Config Contains the configs of the app as views associated with controllers will need access to it
     */
    public $config;

    /**
     * @var object contains the DGZ_Translator object
     */
    public $translator;




    /**
     * Constructor. Initialise the various elements of a page controller.
     */
    public function __construct()
    {
        $this->startTime = microtime(true); //can be useful for debugging
        $this->application = new DGZ_Application();
        $this->config = $this->application->getAppConfig();

        $this->translator = new DGZ_Translator();
        $this->defaultAction = $this->getDefaultAction();

        $this->appName = $this->getAppName();

        $this->defaultLayoutDirectory = $this->getDefaultLayoutDirectory();
        $this->defaultLayout = $this->getDefaultLayout();

        $this->format = isset($_REQUEST['format']) ? $_REQUEST['format'] : 'html';

        $this->globalSeoData = [];
        $this->metadata = [];
        $this->bodySeoData = [];
        $this->styles = [];
        $this->scripts = [];
        $this->exceptions = [];
        $this->warnings = [];
        $this->notices = [];
        $this->success = [];
        $this->errors = [];
    }




    /**
     * Gets the name of the application. It gets this from the application (Config) class
     * If you want your page to use a different one, override this function to return a different name.
     * @return string The name of the layout to be used, e.g. 'DefaultLayout'
     */
    public function getAppName() {
        //We should get this layout setting from one central location; the Application class or a config file.
        $app = new \DGZ_library\DGZ_Application();
        return $app->getAppName();
    }



    /**
     * Sets the default layout directory for all pages. It gets this from the application (Config) class
     * If you want your page to use a different one, override this function to return a different name.
     * @return string The name of the layout to be used, e.g. 'DefaultLayout'
     */
    public function getDefaultLayoutDirectory() {
        //We should get this layout setting from one central location; the Application class or a config file.
        $app = new \DGZ_library\DGZ_Application();
        return $app->getDefaultLayoutDirectory();
    }



	/**
	 * Gets the default layout for all pages. It gets this from the application (Settings) class
	 * If you want your page to use a different one, override this function to return a different name.
	 * @return string The name of the layout to be used, e.g. 'DefaultLayout'. We just instantiate the application class n return it Factory style
	 */
	public function getDefaultLayout() {
		//We should get this layout setting from one central location; the Application class or a config file.
		$app = new \DGZ_library\DGZ_Application();
		return $app->getDefaultLayout();
	}





	/**
	 * Changes the default layout dir for your page.
	 * If you want your page to use a specific layout dir from the default one, override the default layout dir here.
	 * @return string The name of the layout dir to be used.
	 */
	public function setLayoutDirectory($newLayourDir) {
		$this->defaultLayoutDirectory = $newLayourDir;
	}






	/**
	 * Changes the default layout for any specific page.
	 * If you want your page to use a specific layout from the default one, override the default layout here.
	 * @return string The name of the layout to be used, e.g. 'emailLayout'.
	 */
	public function setLayoutView($newLayout) {
		$this->defaultLayout = $newLayout;
	}





	/**
	 * This method tells the controller that for the view file about to be invoked, we do not need a full layout file as all
	 * the site's other views do. THis will therefore set the $useFullLayout field to false. When the display() method is then called
	 * to render the view file; it passes this $useFullLayout field's value as one of its parameters when it calls
	 * DGZ_library\DGZ_Layout's getLayout() which will in turn check this value when deciding the type of layout to use for the view.
	 * If it is false, it then uses the BlankLayout file.
	 */
	public function setNoLayout() {
		$this->useFullLayout = false;
	}



	/**
	 *Every page will have a different name & title so we need to empower the programmer to set the HTML title tag value of
	 * every view file they display if they wish.
	 * They will do so by calling the setPageTitle() method of this class from the page controller when generating the view.
	 * They need to pass as a string argument for the view title.
	 *
	 * This title will then be passed over to the layout class so that it knows to set the title for every view file.
	 * This transfer to the layout class happens below in the display() method of this controller class after we grab the
	 * applicable layout file to be used with the view and call that layout class's own setPageTitle() method passing
	 * it the value of the pageTitle property of this class.
	 *
	 * Therefore note that the DGZ_Controller and the DGZ_Layout classes both have a pageTitle property, and a setPageTitle()
	 * method alike. Do not confuse the two.
	 * One (DGZ_Controller's setPageTitle()) is called by the programmer optionally, and the other DGZ_Layout's setPageTitle()
	 * is auto called by the system to relay the page title change through to whatever view file is about to be shown.
	 *
	 * If you fail to set a page title explicitly, the nme of the file will be used by default. It is recommended to
	 * set your a view titles explicitly so that you can make them SEO friendly.
	 */
	public function setPageTitle($title)
	{
		$title = str_replace('_', ' ', $title);
		if ($title == 'index.phtml') {
			$title = 'home';
		}

		$this->pageTitle = ucwords($title);
	}


    /**
    * Create a title within a custom controller just before it reders a view file 
    *   $view = DGZ_View::getView('myViewName', $this, 'html');
    *   $this->setPageTitle('My view page custom title');
    *   $view->show();
    *
    * Then grab this title and use within the show() method of the view file, ideally at the top, 
    *    like so:  
    * 
    *   $this->addMetadata(
    *    [
    *         ...
    *         '<link rel="canonical" href="'.$this->controller->config->getHomePage().'myViewName" />',
    *         '<title>'.$this->controller->getPageTitle().'</title>'
    *    ]);
    */         
    public function getPageTitle()
	{
		return $this->pageTitle;
	}


	/**
	 * Every time we display a view through a controller, we set the name of the view file here automatically.
	 * This name will be passed over from this controller to DGZ_Layout so that the layouts also know the name of the view
	 * they are working with. This relay happens below in this controller class's display() method when we grab the
	 * applicable layout file to be used with the view and call that layout class's own setViewName() method and passing
	 * it the value of the viewName property of this class.
	 *
	 * Therefore note that the DGZ_Controller and the DGZ_Layout classes both have a viewName property and a setViewName()
	 * method alike. Do not confuse the two. You do not have to set these. They are set for you automatically.
	 *
	 * @param $fileName
	 */
	public function setViewName($fileName)
	{
		$this->viewName = $fileName;
	}

	public function rootPath()
	{
		return $this->config->getFileRootPath();
	}



	/**
	 *Every page will have a different outlook, so we need to empower the programmer to decide whether to show an image slider for every view file they display
	 * They will do so by calling this setImageSlider() method. It is optionally called from the controller when generating any view file that it wants an image
	 * slider to be displayed in. It calls setImageSlider() passing a boolean, if 'true' the view will have an image slider as the code in the layout file will
	 * check for this boolean and act accordingly.
	 *
	 * It is how this setting is applied in the layout. From the DGZ_Router class's Route() method (called in index.phtml) we get the controller meant to handle the
	 * URL request. A couple of other things are then set subsequently; like the method of the controller to call, what layout to use to render the view file to be
	 * shown to the user (getLayout() method of this class) etc. Once the controller to handle the request is gotten, the Dorguzen framework then uses the
	 * DGZ_Controller class's display() method is used to set various things on the view file to be rendered, like, the page title value for the specific page,
	 * and whether to display an image slider on the view page etc. To determine whether to display an image slider on a view page, in the DGZ_Controller->display()
	 * method, DGZ_Layout's (which represents the layout instance to be used by the view file being generated) setImageSlider() is called and the value of the
	 * controller's own $showImageSlider field is passed to it (which is why $showImageSlider is false by default on both DGZ_Controller and DGZ_Layout classes).
	 *
	 * Determine whether or not to show an image slider in the specific view file about to be displayed
	 * layout->setImageSlider($this->showImageSlider);
	 *
	 * This is so because, in case the programmer had not explicitly specified that an image slider is shown in the view they're about to show,
	 *
	 * 		$this->setImageSlider(true);
	 *
	 * there will be no image slider for the page in question.
	 *
	 * This makes sense because not many view files will need a slider. The view that typically needs it is the home page
	 *
	 * Therefore note that the DGZ_Layout and the DGZ_Controller both have showSlider properties, as well as setImageSlider() methods alike-do not confuse them.
	 * One (DGZ_Controller's setImageSlider()) is called by the programmer optionally in the user controller just before generating (redirecting to a) view file,
	 * and the other DGZ_Layout's setImageSlider() is auto called by the system behind the scenes when getting that view to display and the value of that
	 * controller's $showImageSlider field which the programmer has just set is relayed (transferred) to the layout's own $showImageSlider field which will be checked
	 * for by the layout file responsible for that view file to determine whether to show the slider or not.
	 *
	 *					  //Determine whether or not to show an image slider in the specific view file about to be displayed
	 *					$layout->setImageSlider($this->showImageSlider);
	 */
	public function setImageSlider($trueOrFalse)
	{
		$this->showImageSlider = $trueOrFalse;
	}




    public function setGlobalSeoData($appGlobalSeoData)
    {
        $this->globalSeoData = $appGlobalSeoData;
    }


    public function getGlobalSeoData()
    {
        return $this->globalSeoData;
    }



	/**
	 * Adds meta tags for a specific view to be injected directly into the head tag of the layout page.
	 * Other generic meta data have been preset in the layout file and are applied to all pages with the exception of the following:
	 *		-description
	 *		-keywords
	 * You can add as many more as need. This is very handy for the SEO of specific views
	 *
	 * @param array $metadataTagsArray. An array containing strings of fully formed meta tags
	 * @example At the top of the show() method of your view file, do this:
	 * 				$this->addMetadata(
	 *					[
	 * 						'<meta name="description" content="Free Web tutorials">',
	 *						'<meta name="keywords" content="HTML, CSS, JavaScript">',
	 *						'<meta name="author" content="John Doe">'
	 *
	 * 					]);
	 *
	 */
	public function addMetadata($metadataTagsArray) {
		$this->metadata = $metadataTagsArray;
	}

    public function getMetadata() {
        return $this->metadata;
    }




    /**
     * @$seoContent array of data to replace $this->bodySeoData array with
     */
    public function setBodySeoData($seoContent) {
		$this->bodySeoData = $seoContent;  
	}




    public function getBodySeoData() {
		return $this->bodySeoData;
	}


    /**
     * Call this method before calling getGlobalSeoData(), getMetadata(), & getBodySeoData()
     * because it fetches & makes all that data available to these methods.
     * Here is what it does:
     *  -Checks if $this->metadata is NOT null
     *      -if $this->metadata is NOT null, 
     *          -returns $this->metadata     
     *  -else checks if $this->metadata is null, 
     *      -checks if an SEO module exists
     *      -if SEO module exists-at the meoment, that is if its turned on in the config, 
     *          -fetches the global SEO data for your whole application
     *          -fetches SEO data of the target view class name in (lowercase)
     *              -if SEO data for target view class is found, 
     *                  fetches this data, builds the page SEO string taking into account the currently active locale & returns it
     *          -else if no SEO data is found in SEO module, returns nothing
     *      else if no SEO module exists, returns nothing 
     *      -When all else fails; because $this->metadata is null, it returns nothing
     */
    public function loadSeoData($viewName) { 
        if ($this->metadata == null)
        {
            //check if SEO module exists & is active
            if (
                (array_key_exists('seo', $this->config->getConfig()['modules'])) &&
                ($this->config->getConfig()['modules']['seo'] == 'on')
            )
            {
                //The module exists, so get its controller
                $seoController = new \modules\seo\controllers\SeoController();
                $targetViewClass = strtolower($viewName);
                $lang = $this->getLang();

                //get view SEO data
                $seoData = $seoController->getSeoByName($targetViewClass);
                $globalSeoData = $seoController->getGlobalSeoData();

                if ($globalSeoData)
                { 
                    //build your app's global SEO HTML elements
                    $appGlobalSeoData = [];

                    if (isset($globalSeoData[0]['seo_global_og_locale']))
                    { 
                        $appGlobalSeoData[] = '<meta property="og:locale:alternate" content="'.$globalSeoData[0]['seo_global_og_locale'].'" />';
                    }
                    if (isset($globalSeoData[0]['seo_global_og_site']))
                    {
                        $appGlobalSeoData[] = '<meta property="og:site_name" content="'.$globalSeoData[0]['seo_global_og_site'].'" />';
                    }
                    if (isset($globalSeoData[0]['seo_global_og_article_publisher']))
                    {
                        //This is the https fully qualified path to the personal/business facebook page of this site owner
                        $appGlobalSeoData[] = '<meta property="article:publisher" content="'.$globalSeoData[0]['seo_global_og_article_publisher'].'" />';
                    }
                    if (isset($globalSeoData[0]['seo_global_og_author']))
                    {
                        //This is the https fully qualified path to the personal facebook page of this site owner 
                        $appGlobalSeoData[] = '<meta property="article:author" content="'.$globalSeoData[0]['seo_global_og_author'].'" />';
                    }
                    if (isset($globalSeoData[0]['seo_global_geo_placename'])) 
                    {
                        //The example values here can be 'England', or 'London'
                        $appGlobalSeoData[] = '<meta name="geo.placename" content="'.$globalSeoData[0]['seo_global_geo_placename'].'" />';
                    }
                    if (isset($globalSeoData[0]['seo_global_geo_region'])) 
                    {
                        //The international abbreviation for the location country eg 'UK'
                        $appGlobalSeoData[] = '<meta name="geo.region" content="'.$globalSeoData[0]['seo_global_geo_region'].'" />';
                    }
                    if (isset($globalSeoData[0]['seo_global_geo_position']))   
                    {
                        //This will be the geo coordinates of the site location eg '7.369722;12.354722'
                        $appGlobalSeoData[] = '<meta name="geo.position" content="'.$globalSeoData[0]['seo_global_geo_position'].'" />';
                    }
                    if (isset($globalSeoData[0]['seo_global_fb_id']))    
                    {
                        $appGlobalSeoData[] = '<meta property="fb:app_id" content="'.$globalSeoData[0]['seo_global_fb_id'].'" />';
                    }
                    if (isset($globalSeoData[0]['seo_global_twitter_card']))    
                    {
                        //An example value could be 'summary', or 'article' etc
                        $appGlobalSeoData[] = '<meta name="twitter:card" content="'.$globalSeoData[0]['seo_global_twitter_card'].'" />';
                    }
                    if (isset($globalSeoData[0]['seo_global_twitter_site']))    
                    {
                        //This is the ID of the Twitter account of this website
                        $appGlobalSeoData[] = '<meta name="twitter:site" content="'.$globalSeoData[0]['seo_global_twitter_site'].'" />';  
                    }
                    if (isset($globalSeoData[0]['seo_global_reflang_alternate1']))    
                    {
                        //If your site has alternative versions in different languages. The values can be 'en-ca', or 'fr-ca' 
                        //for a Canadian site in French and English etc
                        $appGlobalSeoData[] = '<link rel="alternate" href="$this->config()->getHomePage()" hreflang="'.$globalSeoData[0]['seo_global_reflang_alternate1'].'" />';
                    }
                    if (isset($globalSeoData[0]['seo_global_reflang_alternate2']))    
                    {
                        //If your site has alternative versions in different languages. The values can be 'en-ca', or 'fr-ca' 
                        //for a Canadian site in French and English etc
                        $appGlobalSeoData[] = '<link rel="alternate" href="$this->config()->getHomePage()" hreflang="'.$globalSeoData[0]['seo_global_reflang_alternate2'].'" />';
                    }

                    //save it to this class to be forwarded to the target view via the layout template
                    $this->setGlobalSeoData($appGlobalSeoData);
                    //--------------------------------------
                } 

                if ($seoData)
                {
                    //build page SEO data & pass it in
                    $pageHeaderSeoData = [];
                    $pageBodySeoData = [];

                    /*
                    There are some bits which we cannot inject into the header of the target web page, like h1 text, h2 text, page_content etc.
                    We will therefore create a new pair of getter/setter properties on this controller for these. We could call then eg bodySeoData. 
                    We will also create the equivalent of these members on the layout object too. Down in the display() method of this controller where 
                    the content of this getMetatag() method is being retrieved to be passed to the setMetadata() method of the layout class, we will also 
                    call these getBodySeoData() method to pass through the SEO data for the web page body. These bodySeoData getter & setter properties 
                    will be passed a simple associative array holding these values. Once in the view layout, they will then be available on the layout class
                    to be called so their data is output to the relevant spots on the page body. 
                    */

                    //We only need 3 pieces of SEO data for the body section
                    $pageBodySeoData['seo_h1_text'] = isset($seoData[0]['seo_h1_text_'.$lang]) ? $seoData[0]['seo_h1_text_'.$lang] : '';
                    $pageBodySeoData['seo_h2_text'] = isset($seoData[0]['seo_h2_text_'.$lang]) ? $seoData[0]['seo_h2_text_'.$lang] : '';
                    $pageBodySeoData['seo_page_content'] = isset($seoData[0]['seo_page_content_'.$lang]) ? $seoData[0]['seo_page_content_'.$lang] : '';
                    $this->setBodySeoData($pageBodySeoData);  
                    //--------------------------------------
                    
                    //build the head tag SEO data
                    if (isset($seoData[0]['seo_meta_desc_'.$lang]))
                    {
                        $pageHeaderSeoData[] = '<meta name="description" content="'.$seoData[0]['seo_meta_desc_'.$lang].'">';
                        
                    }
                    if (isset($seoData[0]['seo_keywords_'.$lang]))
                    {
                        $pageHeaderSeoData[] = '<meta name="keywords" content="'.$seoData[0]['seo_keywords_'.$lang].'">';
                    }

                    //OG stuff
                    if (isset($seoData[0]['seo_og_title_'.$lang]))
                    {
                        $pageHeaderSeoData[] = '<meta property="og:title" content="'.$seoData[0]['seo_og_title_'.$lang].'" />';
                    }
                    if (isset($seoData[0]['seo_og_desc_'.$lang]))
                    {
                        $pageHeaderSeoData[] = '<meta property="og:description" content="'.$seoData[0]['seo_og_desc_'.$lang].'" />';
                    }
                    if (isset($seoData[0]['seo_og_image']))
                    {
                        //TODO: It depends on how the path is saved in this DB field, we may have to append that to the root path string here
                        //instruct the user to put the fully qualified image URL in the form field. They should test in browser first to confirm
                        //sp that it would just work here eg 'http://dorguzen/assets/social/site.png'
                        $pageHeaderSeoData[] = '<meta property="og:image" content="'.$seoData[0]['seo_og_image'].'" />';
                    }
                    if (isset($seoData[0]['seo_og_image_secure_url']))
                    {
                        //TODO: It depends on how the path is saved in this DB field, we may have to append that to the root path string here
                        //instruct the user to put the fully qualified image URL in the form field. They should test in browser first to confirm
                        //sp that it would just work here eg: 'https://dorguzen/assets/social/site.png'
                        $pageHeaderSeoData[] = '<meta property="og:image:secure_url" content="'.$seoData[0]['seo_og_image_secure_url'].'" />';
                    }
                    if (isset($seoData[0]['seo_og_image_width']))
                    {
                        $pageHeaderSeoData[] = '<meta property="og:image:width" content="'.$seoData[0]['seo_og_image_width'].'" />';
                    }
                    if (isset($seoData[0]['seo_og_image_height']))
                    {
                        $pageHeaderSeoData[] = '<meta property="og:image:height" content="'.$seoData[0]['seo_og_image_height'].'" />';
                    }
                    if (isset($seoData[0]['seo_og_video']))
                    {
                        //Advice the user when entering this data in trhe form to provide the 'https' version of the video URL, else FB will reject it
                        $pageHeaderSeoData[] = '<meta property="og:video" content="'.$seoData[0]['seo_og_video'].'" />';
                    }
                    if (isset($seoData[0]['seo_og_type_'.$lang]))
                    {
                        $pageHeaderSeoData[] = '<meta property="og:type" content="'.$seoData[0]['seo_og_type_'.$lang].'" />';
                    }
                    if (isset($seoData[0]['seo_og_url']))
                    {
                        $pageHeaderSeoData[] = '<meta property="og:url" content="'.$seoData[0]['seo_og_url'].'" />';
                    }


                    //Twitter Card stuff
                    if (isset($seoData[0]['seo_twitter_title_'.$lang]))
                    {
                        $pageHeaderSeoData[] = '<meta name="twitter:title" content="'.$seoData[0]['seo_twitter_title_'.$lang].'" />';
                    }    
                    if (isset($seoData[0]['seo_twitter_desc_'.$lang]))
                    {
                        $pageHeaderSeoData[] = '<meta name="twitter:description" content="'.$seoData[0]['seo_twitter_desc_'.$lang].'" />';
                    } 
                    if (isset($seoData[0]['seo_twitter_image']))
                    {
                        //TODO: It depends on how the path is saved in this DB field, we may have to append that to the root path string here
                        $pageHeaderSeoData[] = '<meta name="twitter:image" content="'.$seoData[0]['seo_twitter_image'].'" />';
                    } 
                    if (
                        (isset($seoData[0]['seo_canonical_href'])) &&
                        ($seoData[0]['seo_canonical_href'] == 1)
                    )    
                    {
                        //the full qualified URL path comes from the DB, so we just insert it into the href attribute
                        $pageHeaderSeoData[] = '<link rel="canonical" href="'.$seoData[0]['seo_canonical_href'].'" />';
                    }
                    if (
                        (isset($seoData[0]['seo_no_index'])) &&
                        ($seoData[0]['seo_no_index'] == 1)
                    )    
                    {
                        /////$pageHeaderSeoData[0]['seo_canonical_href'] = '<meta name="robots" content="noindex">';
                        $pageHeaderSeoData[] = '<meta name="robots" content="noindex">';
                    }
                    if (isset($seoData[0]['seo_meta_title_'.$lang]))
                    {
                        $pageHeaderSeoData[] = "<title>".$seoData[0]['seo_meta_title_'.$lang]."</title>";
                    } 

                    //save it to this class to be forwarded to the target view
                    //Note: calling addMetadata() is the same method you would have used if you were manually adding SEO data straight from 
                    //within view files without using the SEO module.
                    $this->addMetadata($pageHeaderSeoData);
                    //--------------------------------------
                }
                else
                {
                    return;
                }
            }
            else
            {
                return;
            }
        }
        else
        {
            return;
        }
	}




	/**
	 * Adds a custom stylesheet to be included on this page.
	 *
	 * Note 1: This file must be located in the "htdocs/css" folder of your project. Subdirectories within htdocs/css are not supported.
	 * Note 2: There is no need to manually include styles needed by any framework, these are defined explicitly in the relevant layout.
	 *
	 * @param string $cssFileName The filename of the CSS file to load.
	 * @example $page->addStyle('myApplicationStyles.css');
	 *
	 */
	public function addStyle($cssFileName) {
		if(!in_array($cssFileName, $this->styles)) {
			$this->styles[] = $cssFileName;
		}
	}



	/**
	 * Adds a custom javascript file to be included on this page.
	 *
	 * Note 1: This file must be located in the "htdocs/js" folder of your project. Subdirectories within htdocs/js are not supported.
	 * Note 2: There is no need to manually include scripts needed by any framework (such as jQuery or Bootstrap), these are defined explicitly in the relevant layout.
	 *
	 * @param string $jsFileName The filename of the Javascript file to load.
	 * @example $page->addScript('myPageEventHandlers.js');
	 *
	 */
	public function addScript($jsFileName) {
		if(!in_array($jsFileName, $this->scripts)) {
			$this->scripts[] = $jsFileName;
		}
	}



	/**
	 * Add a warning message with an optional title to the warnings list.
	 * These are displayed between the menu and the content (at least in the default layouts anyway)
	 *
	 * @param string $message The message to display to the user
	 * @param string $title An optional title. This is displayed in bold at the beginning of the message
	 */
	public function addWarning($message, $title = null) {
		if($title) {
			$this->warnings[] = ['title' => $title, 'description' => $message];
		} else {
			$this->warnings[] = $message;
		}

		$_SESSION['_warnings'] = $this->warnings;
	}



	/**
	 * Add an error message with an optional title to the error list.
	 * These are displayed between the menu and the content (at least in the default layouts anyway)
	 *
	 * @param string $message The message to display to the user
	 * @param string $title An optional title. This is displayed in bold at the beginning of the message
	 */
	public function addErrors($message, $title = null) {
		if($title) {
			$this->errors[] = ['title' => $title, 'description' => $message];
		} else {
			$this->errors[] = $message;
		}

		$_SESSION['_errors'] = $this->errors;
	}



	/**
	 * Add a information message with an optional title to the information message list.
	 * These are displayed between the menu and the content (at least in the default layouts anyway)
	 *
	 * @param string $message The message to display to the user
	 * @param string $title An optional title. This is displayed in bold at the beginning of the message
	 */
	public function addNotice($message, $title = null) {
		if($title) {
			$this->notices[] = ['title' => $title, 'description' => $message];
		} else {
			$this->notices[] = $message;
		}

		$_SESSION['_notices'] = $this->notices;

	}


	/**
	 * Add a success message with an optional title to the success message list.
	 * These are displayed between the menu and the content (at least in the default layouts anyway)
	 *
	 * @param string $message The message to display to the user
	 * @param string $title An optional title. This is displayed in bold at the beginning of the message
	 */
	public function addSuccess($message, $title = null) {
		if($title) {
			$this->success[] = ['title' => $title, 'description' => $message];
		} else {
			$this->success[] = $message;
		}

		$_SESSION['_success'] = $this->success;

	}


	/**
	 * Add a thrown exception to an internal array so that it can be displayed nicely in the layout.
	 */
	public function addException(\Exception $e) {
		$this->exceptions[] = $e;
		$_SESSION['_exceptions'] = $this->exceptions;
	}

    /**
	 * Grab all internally stored exceptions.
	 */
	public function getException() {
		return $this->exceptions;
	}



	/**
	 * Calls the function as requested by $method with the given parameters
	 * @param string $method. The name of the function to call
	 * @param array $inputParameters. A numerically-indexed array to be passed in as arguments to the method.
	 * @throws Exception
	 */
	public function display($method, array $inputParameters) {

		try {

			if(empty($method)) {
				$method = $this->defaultAction;
			}

			try {
				// Run the controller method. That method can override most of the attributes of this DGZ_Controller class.
				ob_start();

				list($controller, $method, $controllerInput) = DGZ_Router::getControllerAndMethod();

				//The main thing here is we just need to update the method since this is what this display() is all about
				//Point to note is that this DGZ_Controller now is acting as the instantiated controller that was called from DGZ_Router()
				//It just wants to update its methods and method arguments
				//--------------------------- MIDDLEWARE ---------------------------------//
				$middleware = new Middleware($controller, $method);

				$boot = $middleware->boot();
				if (array_key_exists($controllerInput, $boot)) {
					$middleWareIntent = $middleware->boot()[$controllerInput];

                    if ($middleWareIntent === 'isActiveModule') { 
                        //Check if a module is active & deny user access if not
                        if (call_user_func([$middleware, $middleWareIntent], $controllerInput)) {  
                            //The given module is active, so allow to proceed
                        }
                        else { 
                            throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You are trying access a non-existent module.');
                        }
                    }
					if ($middleWareIntent === true) {
						//call the middleware method and proceed if it returns true
						if (call_user_func([$middleware, $controllerInput], $method)) {
						}
						else {
							throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You are trying to visit a restricted area of this application.');
						}
					}
					if ($middleWareIntent === false) {
						//call the middleware method and proceed if it returns false
						if (call_user_func([$middleware, $controllerInput], $method) != false) {
							throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You are trying to visit a restricted area of this application.');
						}
						else {
						}
					}
					if ($middleWareIntent === 'divert') {
						//call the middleware method and proceed with a new controller & or, method
						list($controller, $method, $inputParameters) = call_user_func([$middleware, $middleWareIntent], $method);
					}
                    if ($middleWareIntent === 'authorised') { 
                        //Check if middleware authorised method returns true & proceed
                        if (call_user_func([$middleware, $middleWareIntent], $method)) { 
                            //The routing will proceed as normal with the requested controller & method
                        }
                        else { 
                            throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You are trying to visit a restricted area of this application.');
                        }
                    }
                    if ($middleWareIntent === 'authenticated') { 
                        //Check if middleware authorised method returns true & proceed
                        if (call_user_func([$middleware, $middleWareIntent], $method)) { 
                            //The routing will proceed as normal with the requested controller & method
                        }
                        else { 
                            throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You must be logged in to access this section.');
                        }
                    }
				}
				//--------------------------- END MIDDLEWARE -----------------------------//

				if ($method == 'defaultAction')
				{
					$method = $this->defaultAction();
				}
				else {
					call_user_func_array(array($this, $method), $inputParameters);
				}
			} catch (DGZ_Exception $e) {
				$this->addException($e);

            } catch (Exception $e) {
				$this->addException($e);
			}

			// For HTML formatted output, we use a Layout and a Menu to complete the response.
			// For NON-HTML formatted output, we just allow the output buffer to be sent as it is (PHP automatically outputs any open buffers when the script finishes)

			$contentHtml = trim(ob_get_clean());

			if(
				empty($contentHtml)
				&& !$this->redirectPending()
				&& count($this->exceptions) == 0
				&& count($this->warnings) == 0
				&& count($this->errors) == 0
				&& count($this->notices) == 0
				&& count($this->success) == 0
			) {
				$this->addNotice(
					'This page has not sent anything to display, and is not trying to redirect you to another page.' . PHP_EOL
					. 'If you were not expecting this then it might indicate a hidden error somewhere.',
					'For Your Information'
				);
			}


			//Now load the page layout based on value in $this->layout -- which can be altered by the controller above.
			$layout = DGZ_Layout::getLayout($this->useFullLayout, $this->appName, $this->defaultLayoutDirectory, $this->defaultLayout);


			// Have there been any messages stored in the session?
			// This can happen when a page has done some work in the database and has been redirected.
			if(isset($_SESSION['_warnings']) && is_array($_SESSION['_warnings']) && count($_SESSION['_warnings']) > 0) {
				$this->warnings += $_SESSION['_warnings'];
			}

			if(isset($_SESSION['_errors']) && is_array($_SESSION['_errors']) && count($_SESSION['_errors']) > 0) {
				$this->errors += $_SESSION['_errors'];
			}


			if(isset($_SESSION['_success']) && is_array($_SESSION['_success']) && count($_SESSION['_success']) > 0) {
				$this->success += $_SESSION['_success'];
			}
			if(isset($_SESSION['_notices']) && is_array($_SESSION['_notices']) && count($_SESSION['_notices']) > 0) {
				$this->notices += $_SESSION['_notices'];
			}
			if(isset($_SESSION['_exceptions']) && is_array($_SESSION['_exceptions']) && count($_SESSION['_exceptions']) > 0) {
				$this->exceptions += $_SESSION['_exceptions'];
			}

			// Are we about to redirect? If not (i.e. this page and therefore these messages are going to be shown)
			// then we can clear out the stored messages in the session sent from the last page
			//This is the bit that ensures that we do not show session msgs of a previous view when we go to a diff view
			if(!$this->redirectPending()) {
				if(isset($_SESSION['_warnings'])) {
					unset($_SESSION['_warnings']);
				}

				if(isset($_SESSION['_errors'])) {
					unset($_SESSION['_errors']);
				}

				if(isset($_SESSION['_success'])) {
					unset($_SESSION['_success']);
				}

				if(isset($_SESSION['_notices'])) {
					unset($_SESSION['_notices']);
				}

				if(isset($_SESSION['_exceptions'])) {
					unset($_SESSION['_exceptions']);
				}
			} else {
				// If we are about to do a header redirect then save the session so that it is not lost.
				// This can happen on redirected form submissions.
				session_write_close();
			}

            // If there have been any warnings generated, get a view to render them
            if(count($this->warnings) > 0) {
                $warningView = DGZ_View::getView('WarningListView');
                ob_start();
                $warningView->show($this->warnings);
                $warningHtml = ob_get_clean();
                $layout->setWarningHtml($warningHtml);
            }

            // If there have been any errors generated, get a view to render them
            if(count($this->errors) > 0) {
                $errorsView = DGZ_View::getView('ErrorsListView');
                ob_start();
                $errorsView->show($this->errors);
                $errorsHtml = ob_get_clean();
                $layout->setErrorsHtml($errorsHtml);
            }

            // If there have been any exceptions generated, get a view to render them
            if(count($this->exceptions) > 0) {
                $exceptionView = DGZ_View::getView('ExceptionView', $this);
                ob_start(); 
                $exceptionView->show($this->exceptions);
                $exceptionHtml = ob_get_clean();
                $layout->setExceptionHtml($exceptionHtml);
            }

            // If there have been any notice messages generated, get a view to render them
            if(count($this->notices) > 0) {
                $noticeView = DGZ_View::getView('NoticeListView', $this);
                ob_start();
                $noticeView->show($this->notices);
                $noticeHtml = ob_get_clean();
                $layout->setNoticeHtml($noticeHtml);
            }

            // If there have been any success messages generated, get a view to render them
            if(count($this->success) > 0) {
                $successView = DGZ_View::getView('SuccessListView', $this);
                ob_start();
                $successView->show($this->success);
                $successHtml = ob_get_clean();
                $layout->setSuccessHtml($successHtml);
            }

            $layout->setContentHtml($contentHtml);

            //set any META TAGS, CSS or JS files that the programmer has used on the specific view file
            //load SEO data
            $this->loadSeodata($this->viewName);
            $layout->setGlobalSeoData($this->globalSeoData);
            $layout->setMetadata($this->getMetadata());
            /////////////////////$layout->setBodySeoData($this->getBodySeoData()); /////THIS IS NO LONGER NEEDED HERE RIGHT??? NOW DONE JUST BETWEEN CONTROLLER & DGZ_View()
            $layout->setCssFiles($this->styles);
            $layout->setJavascriptFiles($this->scripts);

            //Set the HTML title tag value for the specific view file about to be displayed
            $layout->setPageTitle($this->pageTitle);

            //Set the view file name of the specific view file about to be displayed
            $layout->setViewName($this->viewName);

            //Determine whether or not to show an image slider in the specific view file about to be displayed
            $layout->setImageSlider($this->showImageSlider);
            $layout->display();

        } catch (DGZ_Exception $e) { 
            if($this->format == 'html') {
                $e->display();
            } else {
                throw $e;
            }
        } catch (Exception $e) { 
            if($this->format == 'html') {
                $layout = \DGZ_library\DGZ_Layout::getLayout(true, $this->appName, $this->defaultLayoutDirectory, $this->defaultLayout);
                $view = DGZ_View::getView('ExceptionView', $this);
                ob_start();
                $view->show($e);
                $contentHtml = ob_get_clean();
                $layout->setContentHtml($contentHtml);
                $layout->display();
            } else {
                throw $e;
            }
        }
    }




    /**
     * Silently redirects the user to another page (within the same application).
     *
     * Ideal when calling an action which has done some work on the database and you don't want to risk
     * the user refreshing/resubmitting the data.
     *
     * You can call this function more than once, the last given URL is the one that is sent to the user.
     *
     * @param string $controller The name of the controller to target
     * @param string $action The action (method) of the controller to run. Leave blank to run the default action of the controller
     * @param array $arguments [Optional] Any extra URL parameters. Omit to pass none.
     */
    public function redirect($controller, $method = '', array $arguments = []) {
        $url = $controller;
        if(!empty($method)) {
            $url = $url.'/'.$method;
        }

        //Check if redirect has already been called before
        $redirecting = $this->redirectPending();
        if ($redirecting)
        {
            //unset any previous header locations
            header_remove('location');
        }

        // Set the new header location (i.e. last-one-wins strategy)
        header('Location: '.$this->config->getFileRootPath() .$url. ((count($arguments) > 0) ? '?' . http_build_query($arguments) : ''));
        exit();

    }





    /**
     * Returns whether a redirect header (i.e. header('Location: [new-url]') has been sent or not.
     *
     * @return boolean TRUE if the page is about to redirect or FALSE if not.
     */
    public function redirectPending() {

        $headers = headers_list();
        foreach($headers as $header) {
            if(stripos($header, 'location:') !== false) {
                return true;
            }
        }
        return false;

    }


    /**
     * Returns whether the given controller and method actually exists inside the project
     *
     * @param string $controller The name of the page/controller which would be loaded
     * @param string $method The name of the method in the controller which would be ran
     * @return bool Whether the controller/method exists or not.
     */
    public static function controllerMethodExists($controller, $method = null) {

        if(class_exists($controller, true)) {

            $reflection = new ReflectionClass($controller);

            // If no method has been provided then work out what the default action is.
            // To do this, we need to instantiate the class and call it's getDefaultAction method.
            if(empty($method)) {
                $controllerInstance = new $controller();
                $method = $controllerInstance->getDefaultAction();
            }

            // Now we should have a method, whether one was passed in or not.
            // We can now see if that method exists within the class.
            try {
                $reflection->getMethod($method);
                return true;
            } catch (Exception $e) {
                // If the method does not exist then ReflectionClass throws an exception, just return false.
                return false;
            }

        } else {
            // The class cannot be found, so return false.
            return false;
        }

    }






    /**
     * Controllers are responsible for processing form submissions and re-directions thereafter.
     * During redirects submitted form values are lost. It makes sense therefore to have a method for controllers
     * to send back submitted post values in cases of invalid submissions so that these values can be re-displayed
     * to users and save them retyping them in. The result is thus awesome sticky forms-way to go.
     *
     * @param $post
     */
    public function postBack($post)
    {
        $_SESSION['postBack'] = $post;
    }




    /**
     * Generate a random code to e used for anything from email verifications to password resets etc
     *
     * @return mixed
     */
    public function generateCode()
    {
        $words = [
            'chokochohilarious',
            'jammijamjim',
            'tolambomanulo',
            'kilabakula',
            'jamborayla',
            'kingkong',
            'camerooncom',
            'camerooncomcm',
            'bayofbiscay',
            'camprocol',
            'tuxedo',
            'camgas',
            'manyolo',
            'geomasso',
            'ndipakem',
            'jamesbond',
            'camerooncomcom',
            'cmerooncomnet',
            'camerooncominfo',
            'nolimit',
            'chopman',
            'builders',
            'jackstrow',
            'colgate',
            'jimreeves',
            'popol',
            'bamenda',
            'buea',
            'bafoussam',
            'nkongsamba',
            'ahidjo',
            'douala',
            'yaounde',
            'bertoua',
            'ebolowa',
            'ngaoundere',
            'maroua',
            'foumban',
            'bafang', '
			lavoir',
            'brancher',
            'sicia',
            'achana',
            'francais',
            'anglais',
            'french',
            'english',
            'business',
            'bosco',
            'shokoloko',
            'bangoshay',
            'papou',
            'wembley',
            'hausa',
            'bororo',
        ];

        $randomnumber = rand(0, 53);
        $randword = rand() . rand(0, 32000);
        $activationCode = "$words[$randomnumber]" . "$randword";

        $trimmed = trim($activationCode);
        return md5($trimmed);
    }


    /**
     * Gets the name of a controller class.
     * A Controller name looks like: 'controllers\BamendaController', 
     *  so, first it strips of the 'Controller' at the end of the name,
     *  then it strips off the 'controllers\' prefix, & returns the string 'Bamenda'
     *  from the name
     * @return mixed
     */
    public function getNameFromController($controller)
	{
		$first = substr($controller, 0, strpos($controller, 'Controller'));
		return substr($first, 12);
	}



    /**
     * @param $data
     * @param int $max_no_perpage the max no of records you want to display per page
     * @return array
     */
    public function getPaginationMarkers($data, $max_no_perpage = 10)
    {
        $result = [];
        if (is_array($data)) {
            $totalRecs = count($data);
        } else {
            $totalRecs = 0;
        }

        $no_pages = ceil($totalRecs / $max_no_perpage);

        $pageNum = isset($_GET['pageno']) ? $_GET['pageno'] : 1;

        //get the position number of the last item on every given page
        if (($pageNum * $max_no_perpage) <= $totalRecs) {
            $last_item_onpage = $pageNum * $max_no_perpage;
        }
        else {
            $last_item_onpage = $totalRecs;
        }

        // calculate the start row of the recordset
        if ($pageNum == 1) {
            $first_item_onpage = 1;
        }
        else {
            $first_item_onpage = $max_no_perpage * ($pageNum - 1) + 1;
        }

        $result['totalRecs'] = $totalRecs;
        $result['max_no_perpage'] = $max_no_perpage;
        $result['no_pages'] = $no_pages;
        $result['pageNum'] = $pageNum;
        $result['first_item_onpage'] = $first_item_onpage;
        $result['last_item_onpage'] = $last_item_onpage;
        return $result;
    }


    public function getLang()
    {
        return $this->translator->getCurrentLang();
    }


    /**
     * Used to see the contents of an array for debugging purposes
     * @param $array
     * @return void
     */
    public function showArray($array)
    {
        echo '<pre>';
        die(print_r($array));
    }

    /**
     * Aka dump and die. Used to dump to screen the contents of a data construct & exit execution-for debugging purposes
     * @param $data
     * @return void
     */
    public function dd($data)
    {
        echo '<pre>';
        die(var_dump($data));
    }

    public function isApiRequest()
    {
        return (isset($_GET['caller-origin'])) && ($_GET['caller-origin'] == 'api') ? true : false;
    }

    /**
     * @status mixed (boolean or int) HTTP response code
     * @message string custom response message
     */
    public function apiResponse($status = 200, $message = '')
    {
        $returnMessage = [];
        $returnMessage['status'] = $status;
        $returnMessage['message'] = $message;
        return $returnMessage;


    }

} ?>
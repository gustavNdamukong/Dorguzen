<?php

namespace DGZ_library;



/**
 * Description of Layout
 *
 * @author Gustav
 */


use settings\Settings;

abstract class DGZ_Layout
{


	/**
	 * @var string The page title for every view file.
	 */
	protected $pageTitle = '';



	/**
	 * @var string The name for the specific view file.
	 */
	protected $viewName = '';



	/**
	 * @var string The name of your application which MUST have been set in settings/config.inc.php.
	 */
	protected static $appName;


	/**
	 * @var string The name of the folder where the layout file for your application is found, e.g. 'DefaultLayout', 'BootstrapLayout'
	 */
	protected static $defaultLayoutDirectory;


	/**
	 * @var string The name of your application's defaultLayout which MUST have been set in settings/config.inc.php.
	 */
	protected static $defaultLayout;


	/**
	 * @var whether or not to show an image slider for every view file.
	 */
	protected $showImageSlider = false;



	/**
	 * @var string Contains the HTML fragment which makes up the main content area
	 */
	protected $content;

	/**
	 * @var string The content-type to send to the client. NOTE: For non HTML layouts only - not all layouts will send this value.
	 */
	protected $contentType;

	/**
	 * @var string Contains an HTML fragment of internal errors thrown while the page/action was running.
	 */
	protected $exceptions;

	/**
	 * @var string Contains the HTML fragement of internal warnings from the framework to the developer (e.g. if app is not properly configured)
	 */
	protected $warnings;

	/**
	 * @var string Contains the HTML fragement of internal errors from the framework to the developer (e.g. if app is not properly configured)
	 */
	protected $errors;

	/**
	 * @var string Contains an HTML fragment of internal messages (not errors) sent while the page/action was running.
	 */
	protected $notices;

	/**
	 * @var string Contains the HTML fragement of success messages from the framework to the user (e.g. if logged in OK)
	 */
	protected $successes;


	/**
	 * @var array An array of meta tags containing meta data of a specific view that will be included on this page.
	 */
	protected $metadata;



	/**
	 * @var array An array of extra javascript files (within the application's htdocs/js directory) to be loaded.
	 */
	protected $jsFiles;

	/**
	 * @var array An array of extra css files (within the application's htdocs/css directory) to be loaded.
	 */
	protected $cssFiles;




	/**
	 * @var object contains the settings of the site, as the view files that will be managed by controllers need to be able to call on it
	 */
	protected $settings;




	/**
	 * Outputs the whole page, comprised of the menu and the content together.
	 */
	abstract public function display();


	public function __construct()
	{
		$this->metadata = array();
		$this->jsFiles = array();
		$this->cssFiles = array();
		$this->settings = new Settings();
	}



	/**
	 *Every page will have a different name & title so we need to empower the programmer to set the HTML title tag value of
	 * every view file they display if they wish.
	 * They will do so by calling the setPageTitle() method of the DGZ_Controller class from the page controller when
	 * generating the view.
	 * They need to pass in a string argument for the view title.
	 *
	 * This title will then be passed over to this layout class so that it knows to set the title for every view file.
	 * This relay happens in DGZ_Controller class's display() method when we grab this
	 * layout class to be used in displaying the view and call this setPageTitle() method, passing
	 * it the value of its (DGZ_Controller's own) pageTitle property.
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
	 * Every time we display a view through a controller, we set the name of the view file here automatically.
	 * This name will be passed over from this controller to DGZ_Layout so that the layouts also know the name of the view
	 * they are working with. This relay happens in DGZ_Controller class's display() method when we grab this
	 * layout class to be used in displaying the view and call this setViewName() method, passing
	 * it the value of its (DGZ_Controller's own) viewName property.
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
	//Determine whether or not to show an image slider in the specific view file about to be displayed
	$layout->setImageSlider($this->showImageSlider);
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



	/**
	 * Takes the rendered HTML fragment and adds it to the layout ready for display
	 *
	 * @param string $content The HTML fragment which makes up the entire content.
	 */
	public function setContentHtml($content)
	{
		$this->content = $content;
	}




	/**
	 * Takes the rendered HTML fragment for warnings and adds it to the layout ready for display
	 *
	 * @param string $content The HTML fragment which makes up the warning section of the page (between header and content usually)
	 */
	public function setWarningHtml($warningHtml)
	{
		$this->warnings = $warningHtml;
	}





	/**
	 * Takes the rendered HTML fragment for warnings and adds it to the layout ready for display
	 *
	 * @param string $content The HTML fragment which makes up the warning section of the page (between header and content usually)
	 */
	public function setErrorsHtml($errorsHtml)
	{
		$this->errors = $errorsHtml;
	}





	/**
	 * Takes the rendered HTML fragment for errors and adds it to the layout ready for display
	 *
	 * @param string $content The HTML fragment which makes up the errors section of the page (between header and content usually)
	 */
	public function setExceptionHtml($exceptionHtml)
	{
		$this->exceptions = $exceptionHtml;
	}





	/**
	 * Takes the rendered HTML fragment for notices and adds it to the layout ready for display
	 *
	 * @param string $content The HTML fragment which makes up the notice section of the page (between header and content usually)
	 */
	public function setNoticeHtml($noticeHtml)
	{
		$this->notices = $noticeHtml;
	}





	/**
	 * Takes the rendered HTML fragment for success messages and adds it to the layout ready for display
	 *
	 * @param string $content The HTML fragment which makes up the success message section of the page (between header and content usually)
	 */
	public function setSuccessHtml($successHtml)
	{
		$this->successes = $successHtml;
	}





	/**
	 * Sets the list of custom javascript files (from the applications htdocs/js folder)
	 * which need to be included in the page
	 *
	 * @param array $fileArray An array of filenames to be included.
	 */
	public function setJavascriptFiles(array $fileArray)
	{
		$this->jsFiles = array_unique(array_merge($this->jsFiles, $fileArray));
	}




	/**
	 * Adds meta tags for a specific view be injected directly into the head tag of the layout page.
	 * Other generic meta data have been preset in the layout file and are applied to all pages with the exception of the following:
	 *		-description
	 *		-keywords
	 * You can add as many more as you see need. This is very handy for the SEO of specific views
	 *
	 * @param array $metadataTagsArray. An array containing strings of fully formed meta tags
	 * @example $page->addMetadata(
	 *					[
	 * 						'<meta name="description" content="Free Web tutorials">',
	'<meta name="keywords" content="HTML, CSS, JavaScript">',
	'<meta name="author" content="John Doe">'
	 *
	 * 					]);
	 *
	 */
	public function setMetadata(array $metadataTagsArray)
	{
		$this->metadata = $metadataTagsArray;
	}




	/**
	 * Sets the list of custom CSS files (from the applications htdocs/css folder)
	 * which need to be included in the page
	 *
	 * @param array $fileArray An array of filenames to be included.
	 */
	public function setCssFiles(array $fileArray)
	{
		$this->cssFiles = array_unique(array_merge($this->cssFiles, $fileArray));
	}




	/**
	 * Returns a block of <meta html tags to be injected amongst the layout's already existing meta tags
	 *
	 * @return string A set of meta tags with break tags at the end of each one, ready for inserting into the layout's head tag.
	 */
	public function getMetadata()
	{
		$metatagHtml = '';
		foreach ($this->metadata as $data) {
			$metatagHtml .= html_entity_decode($data) . PHP_EOL;
		}
		return $metatagHtml;
	}



	/**
	 * Returns a block of link html tags to load in the application's own CSS styles
	 *
	 * @return string A set of link tags ready for inserting into the layout.
	 */
	public function getCssHtml()
	{
		$cssHtml = '';
		foreach ($this->cssFiles as $file) {
			$cssHtml .= "<link rel='stylesheet' type='text/css' href='".$this->settings->getFileRootPath().'css/' . htmlspecialchars($file) . "'>" . PHP_EOL;
		}
		return $cssHtml;
	}






	/**
	 * Returns a block of script html tags to load in the application's own javascript files
	 *
	 * @return string A set of script tags ready for inserting into the layout.
	 */
	public function getJavascriptHtml()
	{
		$jsHtml = '';
		foreach ($this->jsFiles as $file) {
			$jsHtml .= "<script type='text/javascript' src='".$this->settings->getFileRootPath().'js/' . htmlspecialchars($file) . "'></script>" . PHP_EOL;
		}
		return $jsHtml;
	}





	public function setContentType($contentType)
	{
		$this->contentType = $contentType;
	}





	/**
	 * Fetches a layout class with the given name
	 *
	 * @param string $appName The name of this app so it knows how to scale the folder system for the right layout file
	 * @param string $layoutFolder The name of the folder to find the layout in
	 * @param string $layoutName The name of the layout to find
	 * @return \layouts\Layout The named layout if found
	 * @throws \Exception If the layout cannot be found
	 */
	public static function getLayout($useFullLayout, $appName, $layoutFolder, $layoutName)
	{
		$layoutSettings = new Settings();

		//While we're grabbing the right layout to show, take this opportunity to tell the layout class the app settings for layouts as it also needs to know
		self::$appName = $appName;
		self::$defaultLayoutDirectory = $layoutFolder;
		self::$defaultLayout = $layoutName;

		//if we are in the local environment
		if ($layoutSettings->getSettings()['live'] == false) {
			if ($useFullLayout) {
				$layoutFileName = $_SERVER['DOCUMENT_ROOT'] . '/' . $appName . '/layouts/' . $layoutFolder . '/' . $layoutName . '.php';
				//on live (Godaddy) $layoutFilename is: /home/i3v8zo1vaw30/public_html/nolimitmedia/layouts/nolimitmedia/nolimitmediaLayout.php

				//so document root is: /home/i3v8zo1vaw30/public_html/public_html/
				/*
				 * LAYOUT FILE IS: /home/i3v8zo1vaw30/public_html/nolimitmedia/layouts/nolimitmedia/nolimitmediaLayout.php
				 * LayoutFolder is: nolimitmedia
				 * Layout name: nolimitmediaLayout
				 * appName is: nolimitmedia
				*/
				$layoutClass = '\layouts\\' . $layoutFolder . '\\' . $layoutName;
			}
			else {
				$layoutFileName = $_SERVER['DOCUMENT_ROOT'] . '/' . $appName . '/layouts/' . $layoutFolder . '/' . 'BlankLayout.php';
				$layoutClass = '\layouts\\' . $layoutFolder . '\BlankLayout';
			}
		}
		else
		{
			//if we are on the live environment we get rid of appName
			if ($useFullLayout)
			{
				//there is no $appName folder (e.g. appName) inside the root folder //which is 'public_html'. Rather, we just have 'public_html/layouts'. 				//We did the same for the else clause below.
				$layoutFileName = $_SERVER['DOCUMENT_ROOT'] . '/layouts/' . $layoutFolder . '/' . $layoutName . '.php';

				$layoutClass = '\layouts\\' . $layoutFolder . '\\' . $layoutName;
			}
			else
			{
				$layoutFileName = $_SERVER['DOCUMENT_ROOT'] . '/layouts/' . $layoutFolder . '/' . 'BlankLayout.php';

				$layoutClass = '\layouts\\' . $layoutFolder . '\BlankLayout';
			}
		}

		if (file_exists($layoutFileName)) {
			//Now require the file so you can instantiate its class
			require_once($layoutFileName);

			return new $layoutClass();
		}


	}
}

<?php

namespace DGZ_library;



/**
 * Base Class for Views. Also contains the static method getView which looks for a named view and decides whether to use
 * the base version or an overridden version
 *
 * @author Gustav
 */
abstract class DGZ_View
{


	/**
	 * Returns a View class for displaying things in the desired format as defined in the URL (default to HTML if not set).
	 *
	 * Checks for the target view in three locations:
	 *        i) views/[format]/[viewName]
	 *        ii) DGZ_library/DGZ_views/[viewName]
	 *        iii) layouts/reserveViewTemplates/[viewName]
	 *
	 * @param string $viewName The name of the view you want
	 * @param /DGZ_Controller $container [Optional] If calling a view which descends from HtmlView, pass in a reference to the controller (view container) in which this view will be displayed.
	 * @param string $format [Optional] Specify a format if necessary, otherwise it uses the format of the url
	 * @param boolean $toolkit [Optional] indicates if the view file to be displayed is part of the toolkit folder containing useful frontend design components
	 *
	 * @return object A DGZ_View object if found,
	 * @throws /Exception if a view object cannot be found in the desired format.
	 */
	public static function getView($viewName, \DGZ_library\DGZ_Controller $pageController = null, $format = null, $toolkit = false)
	{

		if (is_null($format)) {
			$format = isset($_REQUEST['format']) ? strtolower($_REQUEST['format']) : 'html';
		}

		$fileName = './views/' . $viewName . '.php';
		$coreViewFile = 'DGZ_library/DGZ_views/' . $viewName . '.php';

		if ((file_exists($fileName)) && ($toolkit === false)) {
			include_once $fileName;
			$viewClass = 'views\\' . $viewName;
		}
		elseif ((file_exists($coreViewFile)) && ($toolkit === false)) {
			//we sometimes want to display Dorguzen's internal view files

			include_once($coreViewFile);
			$viewClass = 'DGZ_library\DGZ_views\\' . $viewName;
		}
		elseif ($toolkit === true) {
			//finally, if we are trying to grab a view from our UI design toolbox
			$toolboxFileNameBase = 'layouts/reserveViewTemplates/' . $viewName . '.phtml';
			if (file_exists($toolboxFileNameBase)) {
				include_once($toolboxFileNameBase);
				$viewClass = 'layouts\reserveViewTemplates\\' . $viewName;
			}
		}
		else {
			//the views file path does not exist
			throw new \DGZ_library\DGZ_Exception('DGZ_View "' . $viewName . '" not found', \DGZ_library\DGZ_Exception::NO_VIEW_FOUND, 'No view class could be found called "' . $viewName . '" for format "' . '"' . PHP_EOL . 'Please check that the class exists in either "' . $fileName . '" or in "' . $coreViewFile . '"');
		}


		//No need to specify the whole path in order to instantiate the class, as we have already included its file above
		$object = new $viewClass();

		if ($object instanceof \DGZ_library\DGZ_HtmlView) {

			if (!($pageController instanceof \DGZ_library\DGZ_Controller)) {
				throw new \DGZ_library\DGZ_Exception('Controller Object Required for HtmlView', \DGZ_library\DGZ_Exception::MISSING_PARAMETERS, 'When creating a view which extends from //DGZ_library//DGZ_View, you must provide your DGZ_Controller object as the second parameter ' . 'into DGZ_View::getView()' . PHP_EOL . '(got "' . (is_object($pageController) ? get_class($pageController) : (is_array($pageController) ? 'array' : print_r($pageController, true))) . '")' . 'This is because HTML views may need to add their own styles and scripts into your controller object in order for them to work properly.');
			}

			//Set the default title of the page to the view (class) name
			//This can be overridden in the controller when generating a view for a more SEO-friendly title
			$pageController->setPageTitle($viewName);
			$pageController->setViewName($viewName);
			$object->setContext($pageController);
		}

		//return the new view object
		return $object;

	}


	/**
	 * Returns a View class for displaying things in the desired format as defined in the URL (default to HTML if not set).
	 *
	 * This meant to facilitate separating admin views from regular site views. You probably want to apply extra security to limit unwanted access
	 * to these files, so it makes sense to have them in a separate folder. The admin sub-folder in the views folder is therefore the one and only
	 * location where this method checks for the target view.
	 *        views/admin/[format]/[viewName]
	 *
	 * @param string $viewName The name of the view you want
	 * @param /DGZ_Controller $container [Optional] If calling a view which descends from HtmlView, pass in a reference to the controller (view container) in which this view will be displayed.
	 * @param string $format [Optional] Specify a format if necessary, otherwise it uses the format of the url
	 *
	 * @return object A DGZ_View object if found,
	 * @throws /Exception if a view object cannot be found in the desired format.
	 */
	public static function getAdminView($viewName, \DGZ_library\DGZ_Controller $viewController = null, $format = null)
	{

		if (is_null($format)) {
			$format = isset($_REQUEST['format']) ? strtolower($_REQUEST['format']) : 'html';
		}

		$fileName = './views/admin/' . $viewName . '.php';

		if (file_exists($fileName)) {
			include_once $fileName;
			$viewClass = 'views\admin\\' . $viewName;
		}
		else {
			//the views file path does not exist
			throw new \DGZ_library\DGZ_Exception('DGZ_View "' . $viewName . '" not found', \DGZ_library\DGZ_Exception::NO_VIEW_FOUND, 'No view class could be found called "' . $viewName . '" for format "' . '"' . PHP_EOL . 'Please check that the class exists in either "' . $fileName . '" or in "');
		}


		//No need to specify the whole path in order to instantiate the class, as we have already included its file above
		$object = new $viewClass();

		if ($object instanceof \DGZ_library\DGZ_HtmlView) {

			if (!($viewController instanceof \DGZ_library\DGZ_Controller)) {
				throw new \DGZ_library\DGZ_Exception('Controller Object Required for HtmlView', \DGZ_library\DGZ_Exception::MISSING_PARAMETERS, 'When creating a view which extends from //DGZ_library//DGZ_View, you must provide your DGZ_Controller object as the second parameter ' . 'into DGZ_View::getView()' . PHP_EOL . '(got "' . (is_object($viewController) ? get_class($viewController) : (is_array($viewController) ? 'array' : print_r($viewController, true))) . '")' . 'This is because HTML views may need to add their own styles and scripts into your controller object in order for them to work properly.');
			}

			//Set the default title of the page to the view (class) name
			//This can be overridden in the controller when generating a view for a more SEO-friendly title
			$viewController->setPageTitle($viewName);
			$viewController->setViewName($viewName);
			$object->setContext($viewController);
		}

		//return the new view object
		return $object;


	}


	/**
	 * Returns a View class for displaying things in the desired format as defined in the URL (default to HTML if not set).
	 *
	 * This grabs another view file and display it within another view file like a widget. Unlike the other get...View
	 * methods in this class it does not use a layout or have its title or view name set in the parent layout object.
	 *This is because all these have already been set on the main view file this one will be displayed inside of.
	 *
	 *Checks for the target view in two locations:
	 *        i) views/[format]/[viewName]
	 *        ii) views/admin/[viewName]
	 *
	 * @param string $viewName The name of the view you want
	 * @param /DGZ_Controller $container [Optional] If calling a view which descends from HtmlView, pass in a reference to the controller (view container) in which this view will be displayed.
	 * @param string $format [Optional] Specify a format if necessary, otherwise it uses the format of the url
	 *
	 * @return object A DGZ_View object if found,
	 * @throws /Exception if a view object cannot be found in the desired format.
	 */
	public static function getInsideView($viewName, \DGZ_library\DGZ_Controller $viewController = null, $format = null)
	{

		if (is_null($format)) {
			$format = isset($_REQUEST['format']) ? strtolower($_REQUEST['format']) : 'html';
		}

		$fileName = './views/' . $viewName . '.php';
		$fileNameAdmin = './views/admin/' . $viewName . '.php';

		if (file_exists($fileName)) {
			include_once $fileName;
			$viewClass = 'views\\' . $viewName;
		}
		elseif (file_exists($fileNameAdmin)) {
			include_once $fileNameAdmin;
			$viewClass = 'views\admin\\' . $viewName;
		}
		else {
			//the views file path does not exist
			throw new \DGZ_library\DGZ_Exception('DGZ_View "' . $viewName . '" not found', \DGZ_library\DGZ_Exception::NO_VIEW_FOUND, 'No view class could be found called "' . $viewName . '" for format "' . '"' . PHP_EOL . 'Please check that the class exists in either "' . $fileName . '" or in "');
		}


		//No need to specify the whole path in order to instantiate the class, as we have already included its file above
		$object = new $viewClass();

		if ($object instanceof \DGZ_library\DGZ_HtmlView) {

			if (!($viewController instanceof \DGZ_library\DGZ_Controller)) {
				throw new \DGZ_library\DGZ_Exception('Controller Object Required for HtmlView', \DGZ_library\DGZ_Exception::MISSING_PARAMETERS, 'When creating a view which extends from //DGZ_library//DGZ_View, you must provide your DGZ_Controller object as the second parameter ' . 'into DGZ_View::getView()' . PHP_EOL . '(got "' . (is_object($viewController) ? get_class($viewController) : (is_array($viewController) ? 'array' : print_r($viewController, true))) . '")' . 'This is because HTML views may need to add their own styles and scripts into your controller object in order for them to work properly.');
			}

			$object->setContext($viewController);
		}

		//return the new view object
		return $object;

	}

}

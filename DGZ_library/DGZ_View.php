<?php

namespace DGZ_library;



/**
 * Base Class for Views. Also contains the static method getView which looks for a named view and decides whether to use
 * the base version or an overridden version
 *
 * @author Gustav
 */
abstract class DGZ_View {

	
	/**
	 * Returns a View class for displaying things in the desired format as defined in the URL (default to HTML if not set).
	 * 
	 * Checks for an overridden view in htdocs/views/[format]/[viewName]
	 * 
	 * @param string $viewName The name of the view you want
	 * @param /Framework/DGZ_Controller $container [Optional] If calling a view which descends from HtmlView, pass in a reference to the controller (page/container) in which this view will be displayed.
	 * @param string $format [Optional] Specify a format if necessary, otherwise it uses the format of the url
	 *
	 * @return object A DGZ_View object if found,
	 * @throws /Exception if a view object cannot be found in the desired format.
	 */
	public static function getView($viewName, \DGZ_library\DGZ_Controller $pageController = null, $format = null, $toolkit = false) {
		
		if(is_null($format)) {
			$format = isset($_REQUEST['format']) ? strtolower($_REQUEST['format']) : 'html';
		}

		$fileName = './views/' . $viewName . '.php';
		$fileNameBase = 'DGZ_library/DGZ_views/' . $viewName . '.php';

		if((file_exists($fileName)) && ($toolkit === false)) {
			include_once $fileName;
			$viewClass = 'views\\'. $viewName;
		} elseif((file_exists($fileNameBase)) && ($toolkit === false)) {
				include_once($fileNameBase);
				$viewClass = 'DGZ_library\DGZ_views\\'. $viewName;
			} elseif ($toolkit === true) {
				//finally, if we are trying to grab a view from our web build toolbox
				$toolboxFileNameBase = 'layouts/reserveViewTemplates/' . $viewName . '.phtml';
				if (file_exists($toolboxFileNameBase)) {
					include_once($toolboxFileNameBase);
					$viewClass = 'layouts\reserveViewTemplates\\' . $viewName;
				}
			}
			else
			{
				//the views file path does not exist
				throw new \DGZ_library\DGZ_Exception(
					'DGZ_View "' . $viewName . '" not found',
					\DGZ_library\DGZ_Exception::NO_VIEW_FOUND,
					'No view class could be found called "' . $viewName . '" for format "' . '"' . PHP_EOL
						. 'Please check that the class exists in either "' . $fileName . '" or in "' . $fileNameBase . '"'
				);
			}

		$object = new $viewClass();

		if($object instanceof \DGZ_library\DGZ_HtmlView) {

			 if(!($pageController instanceof \DGZ_library\DGZ_Controller)) {
				throw new \DGZ_library\DGZ_Exception(
					'Page Object Required for HtmlView',
					\DGZ_library\DGZ_Exception::MISSING_PARAMETERS,
					'When creating a view which extends from //DGZ_library//DGZ_View, you must provide your DGZ_Controller object as the second parameter '
						. 'into DGZ_View::getView()' . PHP_EOL
						. '(got "' . (is_object($pageController) ? get_class($pageController) : (is_array($pageController) ? 'array' : print_r($pageController, true))) . '")'
						. 'This is because HTML views may need to add their own styles and scripts into your page object in order for them to work properly.'
				);
			}

			//Set the default title of the page to the view (class) name
			//This can be overridden in the controller when generating a view for a more SEO-friendly title
			$pageController->setPageTitle($viewName);
			$object->setContext($pageController);
		}

		//return the new view object
		return $object;

		
	}



}

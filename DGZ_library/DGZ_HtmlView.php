<?php

namespace DGZ_library;

/**
 * Specialisation of the DGZ_View class for views which return HTML.
 *
 * This allows Html based Views to inject CSS and Javascripts which they require
 * into the parent page class which the view will be going into.
 *
 * XML, JSON and other views naturally don't have this need.
 *
 * @author Gustav
 *
 */
abstract class DGZ_HtmlView extends \DGZ_library\DGZ_View {

	/**
	 * Sets a reference to the page which this view is going in to,
	 * so that it can add it's own scripts and styles into it if required.
	 *
	 * Kept protected as we only want implementing views to access
	 * addStyle and addScript, not changing other things.
	 *
	 * @var \DGZ_library\DGZ_Controller $page A reference to the page this view is going into.
	 */
	protected $controller;
	




	public function setContext(\DGZ_library\DGZ_Controller &$pageController) {
		$this->controller = $pageController;
	}

	/**
	 * Add a style sheet to the parent page
	 *
	 * @param string $cssFileName The full URL of the stylesheet to load. (Hint: use the \Cdn\Cdn::getUrl(...) methods.)
	 *
	 */
	protected function addStyle($cssFileName) {
		$this->controller->addStyle($cssFileName);
	}

	/**
	 * Add a javascript file to the parent page
	 *
	 * @param string $jsFileName The full URL of the stylesheet to load. (Hint: use the \Cdn\Cdn::getUrl(...) methods.)
	 *
	 */
	protected function addScript($jsFileName) {
		$this->controller->addScript($jsFileName);
	}

}

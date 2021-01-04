<?php

namespace DGZ_library;

/**
 * Specialisation of the DGZ_View class for views which return HTML.
 *
 * This allows Html based Views to inject meta tags, CSS and Javascripts which they require
 * into the parent controller class which the view will be going into.
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
	 * Add meta tags unique to the current view in the parent controller to be injected later into the layout.
	 * Other generic meta data have been preset in the layout file and are applied to all pages with the exception of the following:
	 *		-description
	 *		-keywords
	 * You can add as many more as you see need. This is very handy for the SEO of specific views
	 *
	 * @param array $metadataTagsArray. An array containing strings of fully formed meta tags
	 *
	 */
	protected function addMetadata($metadataTagsArray) {
		$encodedTags = [];
		foreach($metadataTagsArray as $data)
		{
			$encodedTags[] = htmlentities($data);
		}
		$this->controller->addMetadata($encodedTags);
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





	/**
	 * This method is a shortcut way for view files to create links and define file paths.
	 * @example You can be call from within the href attribute in a view and pass it the path string to redirect to.
	 * 		So in a view file, you can create links in either of these ways:
	 * 			<a href="<?=$this->controller->settings->getFileRootPath()?>gallery";
	 * 			<a href="<?=$this->route('gallery')";
	 *
	 * 			<form method="post" action="<?=$this->controller->settings->getFileRootPath()?>controllerName/methodName">
	 * 			<form method="post" action="<?=$this->route('controllerName/methodName')">
	 *
	 * 			<a class="btn btn-primary"
					href="<?=$this->controller->settings->getFileRootPath()?>gallery/openalbum?
	 * 				album_id=<?php echo $view_album_id; ?>&amp;view_album=<?php echo $target_album;?>
	 * 				&amp;upload_imgs=1">Upload images to this album
	 * 			</a>
	 *
	 * @param string $path
	 * @return mixed
	 */
	protected function route($path)
	{
		return $this->controller->settings->getFileRootPath().$path;
	}



}

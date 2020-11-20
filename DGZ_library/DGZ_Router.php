<?php

use settings\Settings;
use DGZ_library\DGZ_Application;

/**
 * Description of DGZ_Router
 *
 * @author Gustav Ndamukong
 */
class DGZ_Router {

	/**
	 * Returns an array containing the controller and method
	 *
	 * @example list($controller, $method) = DGZ_Router::getControllerAndMethod();
	 *
	 */
	public static function getControllerAndMethod() {

		//------------------------------------------------------
		//lets separate the factions of the URL into controllers, their methods, and their args
		//Query string params can come after methods, and these will rep args e.g. the ID of a rec. Obviously, as many args can be passed as needed.

		//first lets match the URL string params-making sure to leave out the name of the site
		$urlString0 = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);

		$urlString = explode('/', $urlString0);

		//When developing locally, avoid the 1st & 2nd elems (elems 0 and 1) coz that would be the host and site names e.g. 'localhost/yourApp'
		//but when on a live environment, avoid ONLY the 1st elem (0) coz that
		//would be the hostname only, and there is no 'yourApp' param in the URL on live, but rather just 'hostname/'.
		//if the visitor only visits the index without typing anything into the URL (ie only the 3rd elem $urlString[2]),
		// refer them to the HomeController which will show the home page

		//check if we are on local or live environment
		$rootPath = false;
		$settings = new Settings();

		//if we are in the local environment
		if ($settings->getSettings()['live'] == false) {
			if (!empty($urlString[2])) {
				//For their convenience; if they only enter 'index', or 'index.php', we should show them the home page too
				if ($urlString[2] == 'index' || $urlString[2] == 'index.php') {
					//send them to the HomeController
					$get_input = 'Home';
				}
				else {
					$get_input = $urlString[2];
				}
			}
			else {
				//If they just visit the root of the app, show them the home page
				$get_input = 'Home';
				$rootPath = true;
			}


			//there may not be a method specified (3rd slash level) eg when a user visits the home page, so check if there's one
			if (isset($urlString[3])) {
				//Extract the method name by splitting the rest of the string by the '?' character if any
				$filterUrl = explode('?', $urlString[3]);
				$method = $filterUrl[0];
			}
			else {
				$method = [];
			}

			//there may be a 4th slash level and we will use this for situations when we want to call another method to work in collaboration with the method
			//in $urlString[3]. In this case we will have Dorguzen ignore the method in $urlString[3] and use the method in $urlString[4]
			//This is good for readability and SEO as we will end up with a URL like
			// http://yourSite/dorguzen/documentation or http://yourSite/dorguzen/documentation/databases
			//These URLs in Dorguzen read as follows; the DorguzenController/documentation() method or the DorguzenController/databases() method. It is then
			// obvious by looking at the URL that we are looking to get to the databases documentation (http://yourSite/dorguzen/documentation/databases)
			// even though in reality, the databases() method is a different method from the documentation() method.
			if (isset($urlString[4])) {
				//Extract the method name by splitting the rest of the string by the '?' character if any
				$filterUrl2 = explode('?', $urlString[4]);
				$method2 = $filterUrl2[0];
			}
			else {
				$method2 = [];
			}

		}
		else
		{
			//if we are in the live environment
			if (!empty($urlString[1])) {
				//For their convenience; if they only enter 'index', or 'index.phtml', we should show them the home page too
				if ($urlString[1] == 'index' || $urlString[1] == 'index.php')
				{
					//send them to the HomeController
					$get_input = 'Home';
				}
				else
				{
					$get_input = $urlString[1];
				}
			}
			else
			{
				//If they just visit the root of the app, show them the home page
				$get_input = 'Home';
				$rootPath = true;
			}

			//there may not be a method specified (2nd slash level) eg when a user visits the home page, so check if there's one
			if (isset($urlString[2]))
			{
				//Extract the method name by splitting the rest of the string by the '?' character if any
				$filterUrl = explode('?', $urlString[2]);
				$method = $filterUrl[0];
			}
			else
			{
				$method = [];
			}

			//there may be a 4th slash level and we will use this for situations when we want to call another method to work in collaboration with the method
			//in $urlString[2]. In this case we will have Dorguzen ignore the method in $urlString[2] and use the method in $urlString[3]
			//This is good for readability and SEO as we will end up with a URL like
			// http://yourSite/dorguzen/documentation or http://yourSite/dorguzen/documentation/databases
			//These URLs in Dorguzen read as follows; the DorguzenController/documentation() method or the DorguzenController/databases() method. It is then
			// obvious by looking at the URL that we are looking to get to the databases documentation (http://yourSite/dorguzen/documentation/databases)
			// even though in reality, the databases() method is a different method from the documentation() method.
			if (isset($urlString[3]))
			{
				//Extract the method name by splitting the rest of the string by the '?' character if any
				$filterUrl2 = explode('?', $urlString[3]);
				$method2 = $filterUrl2[0];
			}
			else
			{
				$method2 = [];
			}
		}


		$controller = 'controllers\\'. ucfirst($get_input).'Controller';

		try {
			$classReflector = new ReflectionClass($controller);

		} catch (\Exception $e) {
			throw new \DGZ_library\DGZ_Exception(
				'Controller not found',
				\DGZ_library\DGZ_Exception::PAGE_CLASS_NOT_FOUND,
				'No controller could be found in your application with the name "' . $controller . '". ' . PHP_EOL
				. 'Check that the name of the page in the address is correct and that the class exists in either your application\'s controllers folder. ' . PHP_EOL
				. 'Also, this error can happen if you have wrongly namespaced your controller class. Controller classes must live within the global namespace.'
			);
		}

		//Instantiate the controller class
		$object = $classReflector->newInstance();

		// note that if no method is defined, $object->getDefaultAction() below will be run and will exit this execution, esp coz the defaultAction() has
		// no arguments so there's nothing further to resolve. All controllers must therefore have a getDefaultAction() method that takes no arguments.
		// But before we get the default controller, we check if there's a method on the controller that happens to match the spelling of the controller
		// parameter passed in the URL ($get_input) and use that if its found; otherwise, we get the default method. This will prevent us needing to pass
		// URL parameters for methods that have the same spelling as the controller parameter, which will neither look sensible visually, nor be good for
		// search engines. This basically means we would end up having neat URLs that look like: 'http://appName/news' instead of 'http://appName/news/news'.
		if(empty($method)) {
			//we make an exception for the HomeController-if no controller & no method parameter are given in the URL, go straight to its defaultAction() method.
			if (strtoupper($get_input) == 'HOME') {
				if ($rootPath == true) {
					$method = $object->getDefaultAction();
				} else { $method = $get_input; }
			}
			else
			{
				if (\DGZ_library\DGZ_Controller::controllerMethodExists($controller, $get_input)) {
					$method = $get_input;
				}
				else {
					$method = $object->getDefaultAction();
				}
			}
		}


		//when we got the classReflector on this page above, it was just to see if the controller exists
		//If $method2 exists, it replaces $method, else we use the $method
		if ($method2)
		{
			$method = $method2;
		}



		//Do the developer a service here and either convert any hyphens in the method URL param to underscores or replace them with camel-casing names
		//---------------------------------------------------------------------------------------------------------------------------------//
		if (preg_match('/-/', $method))
		{
			$methodPieces = explode('-', $method);
			$method_underscored = implode('_', $methodPieces);

			$newMethodPieces = array_map('ucfirst', $methodPieces);
			$method_camelCased = implode($newMethodPieces);

			if (method_exists($object, $method_underscored))
			{
				//override the method
				$method = $method_underscored;
			}
			elseif (method_exists($object, $method_camelCased))
			{
				//override the method
				$method = $method_camelCased;
			}
		}
		//---------------------------------------------------------------------------------------------------------------------------------//
		return [$controller, $method];

	}









	/**
	 *
	 * Returns the URL required to get back to this controller and method.
	 * Used by Views which contain forms which need to do a postback.
	 *
	 * @param string $overrideMethod [Optional] If you want to set a different method, specify it here. Otherwise use
	 * the current method.
	 *
	 * @return string The base URL to get back to this controller/method.
	 *
	 * @throws \DGZ_library\DGZ_Exception if not all arguments required by the method are provided.
	 */
	public static function getPageUrl($overrideMethod = null) {
		list($controller, $method) = self::getControllerAndMethod();

		$classReflector = new \ReflectionClass($controller);
		$methodReflector = $classReflector->getMethod($method);
		$methodParameters = $methodReflector->getParameters();
		$inputParameters = [];
		foreach($methodParameters as $parameter) {
			$parameterName = $parameter->getName();
			if(isset($_REQUEST[$parameterName])) {
				$inputParameters[$parameterName] = $_REQUEST[$parameterName];
			} elseif($parameter->isDefaultValueAvailable()) {
				$inputParameters[$parameterName] = $parameter->getDefaultValue();
			} else {
				throw new \DGZ_library\DGZ_Exception(
					'Required parameter "' . $parameterName . '" not set',
					\DGZ_library\DGZ_Exception::MISSING_PARAMETERS,
					'If you have just submitted a form, please make sure all fields are set, otherwise this may be a programming error.'
				);
			}
		}

		return htmlspecialchars($controller)
		. htmlspecialchars(is_null($overrideMethod) ? $method : $overrideMethod)
		. '?' . http_build_query($inputParameters);

	}










	/**
	 * Reads the page and action passed in, processes them and loads the relevant
	 * DGZ_Controller object, then calls the method which matches the action name
	 *
	 * @throws /Exception If not all arguments required by the method are provided.
	 */
	public static function route() {

		try {

			// Determine the controller and method to load.
			// Primarily based on the URL but will substitute defaults if not set.
			list($controller, $method) = self::getControllerAndMethod();

			$classReflector = new \ReflectionClass($controller);
			$object = $classReflector->newInstance();

			try {
				/*If no method is passed to the URL and the controller has a default method (declared as 'defaultAction()'),
                the getControllerAndMethod() above would have set that default method as that $method.
                This script above will then automatically redirect and run that method on the controller and we will not get
                to this point, esp coz the defaultAction() method takes no arguments & there's nothing further to resolve.
                Therefore, we will only get in here if there is a method parameter in the URL, in which case we check for it
                in this try block.

                grab the method on the controller that was called in the URL */
				$methodReflector = $classReflector->getMethod($method);
			}
			catch (\ReflectionException $e) {
				throw new \DGZ_library\DGZ_Exception('No method to handle this request', \DGZ_library\DGZ_Exception::MISSING_HANDLER_FOR_ACTION, 'There is no method in your Controller class to handle handle "' . $method . '". ' . PHP_EOL . 'Check that the method name passed through is correct, and if required create a public function called "' . $method . '" in your ' . $controller . ' class.');
			}


			//we wont get here if an exception is thrown above. Grab any query string parameters passed in the URL.
			//These will be the arguments to pass to the method
			$methodParameters = $methodReflector->getParameters();
			$inputParameters = [];

			// Loop over the parameters:
			foreach ($methodParameters as $parameter) {
				// Is the parameter an object? We are not doing anything if it's an object for now-may become useful some day
				if ($parameter->getClass() instanceof \ReflectionClass) {
					$className = $parameter->getClass()->name;
					$parameterObject = new $className();
				}
				else {
					$parameterName = $parameter->getName();
					if (!empty($_REQUEST[$parameterName])) {
						$inputParameters[] = $_REQUEST[$parameterName];
					}
					//if a param has no value, see if the controller method takes a default value
					elseif ($parameter->isDefaultValueAvailable()) {
						$inputParameters[] = $parameter->getDefaultValue();
					}
					else {
						throw new \DGZ_library\DGZ_Exception('Required parameter "' . $parameterName . '" not set', \DGZ_library\DGZ_Exception::MISSING_PARAMETERS,
							'If you have just submitted a form, please make sure all fields are set, otherwise this may be a programming error.');
					}

				}

			}


			$object->display($method, $inputParameters);

		}
		catch (\Exception $e)
		{
			// If you have problems with the script never ending (or timing out after 30/300 seconds then
			//die($e->getMessage() . ' in ' . $e->getFile() . ' line ' . $e->getLine());

			/**
			 * WARNING!
			 *
			 * You must not allow any exception to be thrown out of this section,
			 * otherwise the framework just goes round in circles, and the page eventually times out.
			 *
			 */

			// Is this a DGZ_Exception?
			if($e instanceof \DGZ_library\DGZ_Exception) {

				$view = \DGZ_library\DGZ_View::getView('DGZExceptionView', null, 'html');
			} else {

				// If it's a normal exception then just use the default view
				$view = \DGZ_library\DGZ_View::getView('ExceptionView', null, 'html');
			}

			// If this is a HTML format response then we can use a default layout/view to show the error somewhat nicely.
			//WE DONT HAVE TO WORRY ABOUT THIS FOR NOW AS FORMAT WILL NOT BE USED-HOWEVER WE MAY COME BACK AT A LATER DATE AND DECIDE IF WE NEED FORMATS (FOR DATA)
			if(!isset($_REQUEST['format']) || $_REQUEST['format'] == 'html')
			{
				$config = new DGZ_Application();
				$layout = \DGZ_library\DGZ_Layout::getLayout($config->getUseFullLayoutSetting(), $config->getAppName(), $config->getDefaultLayoutDirectory(), $config->getDefaultLayout());

				$layout->setPageTitle('Error: ');

				// Use the view determined above to generate the HTML for the error
				ob_start();


				// Displays the error itself underneath.
				$view->show($e);


				$contentHtml = ob_get_clean();


				$layout->setContentHtml($contentHtml);

				// And display it.
				$layout->display();

			}
			//WE DONT HAVE TO WORRY ABOUT THIS NOW AS json FORMAT WILL NOT BE USED-HOWEVER WE MAY COME BACK AT A LATER DATE AND REVIEW IF WE NEED DATA FORMAT
			elseif(isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {

				$layout = \DGZ_library\Layout::getLayout('JsonLayout');

				ob_start();
				$view->show($e);
				$content = ob_get_clean();

				$layout->setContentHtml($content);
				$layout->display();

			}
			else
			{
				//THIS IS THE DEFAULT FORMAT WE WILL BE USING
				header('Content-Type: text/plain');

				$msg =  $e->getMessage();
				$file = $e->getFile();
				$line = $e->getLine();
				$stack = $e->getTraceAsString();
				if($e instanceof \DGZ_library\DGZ_Exception) {
					$hint = $e->getHint();
				} else {
					$hint = 'No further information available';
				}

				$errorText = <<<TEXT
Error: {$msg}
Hint:  {$hint}
File:  {$file}
Line:  {$line}

Stack
---------------------------------------------
{$stack}
TEXT;

				die($errorText);

			}

		}

	}


}

<?php

namespace DGZ_library;

use settings\Settings;
use ReflectionClass;


/**
 * Description of DGZ_Router
 *
 * @author Gustav Ndamukong
 */
class DGZ_Router {

	/**
	 * Returns an array containing the currently active controller and method
	 *
	 * @example list($controller, $method) = DGZ_Router::getControllerAndMethod();
	 *
	 * @param bool $stringFormat pass this as true if you want the current controller name returned as a string
	 * 		without checking if the controller object actually exists in the system
	 * @return array
	 * @throws \DGZ_library\DGZ_Exception
	 */
	public static function getControllerAndMethod($stringFormat = false) {
		$urlString0 = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);

		$urlString = explode('/', $urlString0);

		$rootPath = false;
		$settings = new Settings();

		if ($settings->getSettings()['live'] == false) {
			if (!empty($urlString[2])) {
				if ($urlString[2] == 'index' || $urlString[2] == 'index.php') {
					$get_input = 'Home';
				}
				else {
					$get_input = $urlString[2];
				}
			}
			else {
				$get_input = 'Home';
				$rootPath = true;
			}

			if (isset($urlString[3])) {
				$filterUrl = explode('?', $urlString[3]);
				$method = $filterUrl[0];
			}
			else {
				$method = [];
			}

			if (isset($urlString[4])) {
				$filterUrl2 = explode('?', $urlString[4]);
				$method2 = $filterUrl2[0];
			}
			else {
				$method2 = [];
			}

		}
		else
		{
			if (!empty($urlString[1])) {
				if ($urlString[1] == 'index' || $urlString[1] == 'index.php')
				{
					$get_input = 'Home';
				}
				else
				{
					$get_input = $urlString[1];
				}
			}
			else
			{
				$get_input = 'Home';
				$rootPath = true;
			}

			if (isset($urlString[2]))
			{
				$filterUrl = explode('?', $urlString[2]);
				$method = $filterUrl[0];
			}
			else
			{
				$method = [];
			}

			if (isset($urlString[3]))
			{
				$filterUrl2 = explode('?', $urlString[3]);
				$method2 = $filterUrl2[0];
			}
			else
			{
				$method2 = [];
			}
		}


		if ($stringFormat == true)
		{
			$controllerNameString = ucfirst($get_input);
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

		if(empty($method)) {
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

		if ($method2)
		{
			$method = $method2;
		}


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


		if ($stringFormat == true)
		{
			return [$controllerNameString, $method];
		}
		else
		{
			return [$controller, $method];
		}

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
			list($controller, $method) = self::getControllerAndMethod();

			$classReflector = new \ReflectionClass($controller);
			$object = $classReflector->newInstance();

			try {
				$methodReflector = $classReflector->getMethod($method);
			}
			catch (\ReflectionException $e) {
				throw new \DGZ_library\DGZ_Exception('No method to handle this request', \DGZ_library\DGZ_Exception::MISSING_HANDLER_FOR_ACTION, 'There is no method in your Controller class to handle handle "' . $method . '". ' . PHP_EOL . 'Check that the method name passed through is correct, and if required create a public function called "' . $method . '" in your ' . $controller . ' class.');
			}

			$methodParameters = $methodReflector->getParameters();
			$inputParameters = [];

			foreach ($methodParameters as $parameter) {
				if ($parameter->getClass() instanceof \ReflectionClass) {
					$className = $parameter->getClass()->name;
					$parameterObject = new $className();
				}
				else {
					$parameterName = $parameter->getName();
					if (!empty($_REQUEST[$parameterName])) {
						$inputParameters[] = $_REQUEST[$parameterName];
					}
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

			/**
			 * WARNING!
			 *
			 * You must not allow any exception to be thrown out of this section,
			 * otherwise the framework just goes round in circles, and the page eventually times out.
			 *
			 */

			if($e instanceof \DGZ_library\DGZ_Exception) {

				$view = \DGZ_library\DGZ_View::getView('DGZExceptionView', null, 'html');
			} else {

				// If it's a normal exception then just use the default view
				$view = \DGZ_library\DGZ_View::getView('ExceptionView', null, 'html');
			}

			if(!isset($_REQUEST['format']) || $_REQUEST['format'] == 'html')
			{
				$config = new DGZ_Application();
				$layout = \DGZ_library\DGZ_Layout::getLayout($config->getUseFullLayoutSetting(), $config->getAppName(), $config->getDefaultLayoutDirectory(), $config->getDefaultLayout());

				$layout->setPageTitle('Error: ');

				ob_start();

				$view->show($e);

				$contentHtml = ob_get_clean();

				$layout->setContentHtml($contentHtml);

				$layout->display();

			}
			elseif(isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {

				$layout = \DGZ_library\DGZ_Layout::getLayout('JsonLayout');

				ob_start();
				$view->show($e);
				$content = ob_get_clean();

				$layout->setContentHtml($content);
				$layout->display();

			}
			else
			{
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

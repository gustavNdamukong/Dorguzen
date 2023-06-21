<?php

namespace DGZ_library;

use configs\Config;
use ReflectionClass;
use ReflectionException;
use Exception;
use middleware\Middleware;
use ReflectionNamedType;

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

        //if an id value is given in the URL eg 123 in www.hostname/controller/method/123
        $straightUrlId = null;

        //check if we are on local or live environment
        $rootPath = false;
        $config = new Config();

        //if we are in the local environment
        if ($config->getConfig()['live'] == false) {
            if (!empty($urlString[2])) {
                //For their convenience; if they only enter 'index', or 'index.php', we should show them the home page too
                if ($urlString[2] == 'index' || $urlString[2] == 'index.php') {
                    //send them to the HomeController
                    $get_input = 'Home';
                }
                else {
                    $get_input = $urlString[2];
                    //check if it has a '?' character & filter on that
                    if (strrpos($get_input, '?'))
                    {
                        $cut = explode('?', $get_input);
                        $get_input = $cut[0];
                    }

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
            //force the site to run on https
            if ((array_key_exists('HTTPS', $_SERVER)) && ($_SERVER["HTTPS"] != "on"))
            {
                $newUrl = 'https://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
                header("Location: $newUrl");
            }

            if (!empty($urlString[1])) {
                if ($urlString[1] == 'index' || $urlString[1] == 'index.php')
                {
                    //send them to the HomeController
                    $get_input = 'Home';
                }
                else
                {
                    $get_input = $urlString[1];
                    //check if it has a '?' character & filter on that
                    if (strrpos($get_input, '?'))
                    {
                        $cut = explode('?', $get_input);
                        $get_input = $cut[0];
                    }
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


        //does the caller just want this as a string instead of as an object?
        if ($stringFormat == true)
        {
            $controllerNameString = ucfirst($get_input);
        }

        //DGZ runs all applications through controllers or modules. Therefore here, we load the target controller or module class
        /////$controller = 'controllers\\'. ucfirst($get_input).'Controller';
        /////$controllerPath = 'controllers\\'. ucfirst($get_input).'Controller.php';
        $controllerPath = $_SERVER['DOCUMENT_ROOT'].'/'.$config->getFileRootPath().'/controllers/'. ucfirst($get_input).'Controller.php';
        $modulePath = $_SERVER['DOCUMENT_ROOT'].'/'.$config->getFileRootPath().'/modules/'. strtolower($get_input).'/controllers/'.ucfirst($get_input) . 'Controller.php';
        /////$fileName = $folder .'/'. basename($className) . '.php';
		/////echo $fileName.'<br>';////////
		if (file_exists($controllerPath))
        {
            $controller = 'controllers\\'. ucfirst($get_input).'Controller';
            //die('IT EXISTS: '.'controllers\\'. ucfirst($get_input).'Controller');////////
        }
        else if (file_exists($modulePath))
        {
            $controller = 'modules\\'. strtolower($get_input).'\\controllers\\'.ucfirst($get_input).'Controller';
            //die('IT EXISTS: '.'modules\\'. strtolower($get_input).'\\'.ucfirst($get_input));////////
        }

        /////die('Controller: '.$controllerPath.' - Modules: '.$modulePath);////////////////////////

        try {
            $classReflector = new ReflectionClass($controller);

            if (!(get_class($classReflector)))
            {
                http_response_code(400);
                throw new DGZ_Exception(
                    'Controller not found',
                    DGZ_Exception::CONTROLLER_CLASS_NOT_FOUND,
                    'No controller could be found in your application with the name "' . $controller . '". ' . PHP_EOL
                    . 'Check that the name of the page in the address is correct and that the class exists in either your application\'s controllers folder. ' . PHP_EOL
                    . 'Also, this error can happen if you have wrongly namespaced your controller class. Controller classes must live within the global namespace.'
                );
            }
        }
        catch (Exception $e) {

            // Is this a DGZ_Exception?
            if ($e instanceof DGZ_Exception) {
                $view = DGZ_View::getView('DGZExceptionView', null, 'html');
            }
            else {
                // If it's a normal exception then just use the default view
                $view = DGZ_View::getView('ExceptionView', null, 'html');
            }

            $view->show($e);
            exit();
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
                if (DGZ_Controller::controllerMethodExists($controller, $get_input)) {
                    $method = $get_input;
                }
                else {
                    $method = $object->getDefaultAction();
                }
            }
        }


        //when we got the classReflector on this page above, it was just to see if the controller exists
        // Now we check if $method2 exists and make it the applicable method. However, before we pass the $urlString[3] for a method; (this is important),
        // we check that its value is not an integer. If the value is an integer, then we take that to be an ID & inject it as an argument into the method
        // at $urlString[2] (in our example above-the 'documentation()').
        // If it is not an integer, then it is another method eg 'databases()' within the same controller in our example.
        // Now, if $method2 exists & is not an integer, it replaces $method; else if it's an integer, we use $method & store $method2
        // to be injected down the line as its argument
        if ($method2) {
            if (filter_var($method2, FILTER_VALIDATE_INT))
            {
                $straightUrlId = $method2;
            } else {
                $method = $method2;
            }
        }

        if ($method) {
            if (filter_var($method, FILTER_VALIDATE_INT))
            {
                $straightUrlId = $method;
                $method = 'defaultAction';
            }
        }


        //Handle any hyphens in the URL method param by referring the handling to a matching underscored or camel-cased controller method
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
            return [$controllerNameString, $method, $get_input, $straightUrlId];
        }
        else
        {
            return [$controller, $method, $get_input, $straightUrlId];
        }
    }


    /**
     * Reads the page and action passed in, processes them and loads the relevant
     * DGZ_Controller object, then calls the method which matches the action name
     *
     * @throws /Exception If not all arguments required by the method are provided.
     */
    public static function route()
    {
        try {
            // Determine the controller and method to load.
            // Primarily based on the URL but will substitute defaults if not set.
            list($controller, $method, $controllerInput, $straightUrlId) = self::getControllerAndMethod();

            //check if $straightUrlId is applicable & if so, pass an 'id' param into the $_REQUEST object
            //It's then up to the target method to optionally have an $id argument to capture its value
            if (null !== $straightUrlId) {
                $_REQUEST['targetId'] = $straightUrlId;
            }

            $classReflector = new ReflectionClass($controller);
            $object = $classReflector->newInstance();

            //--------------------------- MIDDLEWARE ---------------------------------//;
            $middleware = new Middleware($controller, $method);

            $boot = $middleware->boot();
            if (array_key_exists($controllerInput, $boot)) {
                $middleWareIntent = $middleware->boot()[$controllerInput];
                
                if ($middleWareIntent === true) { 
                    //If its true, call the middleware method and proceed
                    if (call_user_func([$middleware, $controllerInput], $method)) { 
                    }
                    else { die('IT WAS FALSE');
                        throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You are trying to visit a restricted area of this application.');
                    }
                }
                if ($middleWareIntent === false) {
                    //If its false, call the middleware method and proceed
                    if (call_user_func([$middleware, $controllerInput], $method) != false) { die($middleWareIntent.' - '.$controllerInput.' RETURNED === FALSE');
                        throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You are trying to visit a restricted area of this application.');
                    }
                    else {
                    }
                }
                if ($middleWareIntent == 'divert') { 
                    //call the middleware method and proceed with a new controller & or, method
                    //$controller here will be sth like shopController, $newMethod is the desired method to call on shopController, & an optional array of args
                    // to pass to that method.
                    list($controller, $newMethod, $args) = call_user_func([$middleware, $controllerInput], $method);
                    $con = new $controller();
                    $con->display($newMethod, $args);
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
            }
            //--------------------------- END MIDDLEWARE ---------------------------------//

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
            catch (ReflectionException $e) {
                http_response_code(400);
                throw new DGZ_Exception('No method to handle this request', DGZ_Exception::MISSING_HANDLER_FOR_ACTION, 'There is no method in your Controller class to handle handle "' . $method . '". ' . PHP_EOL . 'Check that the method name passed through is correct, and if required create a public function called "' . $method . '" in your ' . $controller . ' class.');
            }


            //Grab any query string parameters passed in the URL.
            //These will be the arguments to pass to the method
            $methodParameters = $methodReflector->getParameters();
            $inputParameters = [];

            // Loop over the parameters:
            foreach ($methodParameters as $parameter) {
                // Is the parameter an object? We are not doing anything if it's an object for now-may become useful some day
                /*if ($parameter->getClass() instanceof ReflectionClass) {
                    $className = $parameter->getClass()->name;
                    $parameterObject = new $className();
                }*/
                //-----------------The above approach using 'getClass()' was deprecated from PHP 8.0, so we used the 'getType()' method instead
                if ($parameter->getType() instanceof ReflectionNamedType) {
                    $type = $parameter->getType();
                    if (!$type->isBuiltin()) {
                        $className = $type->getName();
                        $parameterObject = new ReflectionClass($className);
                    }
                }    
                //-----------------
                else {
                    $parameterName = $parameter->getName();
                    if (!empty($_REQUEST[$parameterName])) {
                        $inputParameters[] = $_REQUEST[$parameterName];
                    }
                    //if the expected argument is not provided, see if that controller method argument has a default value
                    elseif ($parameter->isDefaultValueAvailable()) {
                        $inputParameters[] = $parameter->getDefaultValue();
                    }
                    else {
                        throw new DGZ_Exception('Required parameter "' . $parameterName . '" not set', DGZ_Exception::MISSING_PARAMETERS, 'If you have just submitted a form, please make sure all fields are set, otherwise this may be a programming error.');
                    }
                }

            }

            $object->display($method, $inputParameters);

        }
        catch (Exception $e) {

            /**
             * WARNING!
             *
             * You must not allow any exception to be thrown out of this section,
             * otherwise the framework just goes round in circles, and the page eventually times out.
             *
             */
            // Is this a DGZ_Exception?
            if ($e instanceof DGZ_Exception) {
                $view = DGZ_View::getView('DGZExceptionView', null, 'html');
            }
            else {
                // If it's a normal exception then just use the default view
                $view = DGZ_View::getView('ExceptionView', null, 'html');
            }

            // If this is a HTML format response then we can use a default layout/view to show the error somewhat nicely.
            //WE DONT HAVE TO WORRY ABOUT THIS FOR NOW AS FORMAT WILL NOT BE USED-HOWEVER WE MAY COME BACK AT A LATER DATE AND DECIDE IF WE NEED FORMATS (FOR DATA)
            if (!isset($_REQUEST['format']) || $_REQUEST['format'] == 'html') {
                $config = new DGZ_Application();
                $layout = DGZ_Layout::getLayout($config->getUseFullLayoutSetting(), $config->getAppName(), $config->getDefaultLayoutDirectory(), $config->getDefaultLayout());

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
            //json return format that can be useful for cases like API calls
            elseif (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') {

                $layout = DGZ_Layout::getLayout('JsonLayout');

                ob_start();
                $view->show($e);
                $content = ob_get_clean();

                $layout->setContentHtml($content);
                $layout->display();

            }
            else {
                //THIS IS THE DEFAULT FORMAT WE WILL BE USING
                header('Content-Type: text/plain');

                $msg = $e->getMessage();
                $file = $e->getFile();
                $line = $e->getLine();
                $stack = $e->getTraceAsString();
                if ($e instanceof DGZ_Exception) {
                    $hint = $e->getHint();
                }
                else {
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

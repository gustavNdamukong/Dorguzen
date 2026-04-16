<?php

namespace Dorguzen\Middleware\GlobalMiddleware;


use Dorguzen\Core\DGZ_MiddlewareInterface;
use Dorguzen\Core\DGZ_Exception;
use Dorguzen\Config\Config;
use Dorguzen\Models\Users;
use Dorguzen\Controllers\LocationController;
use Dorguzen\Core\DGZ_Request;

/**
 * Validate or divert requests
 *
 * Created by PhpStorm.
 * User: gustavndamukong
 * Date: 19/01/2021
 * Time: 12:28
 */
class BaseMiddleware implements DGZ_MiddlewareInterface
{
    public string $name = 'BaseMiddleware'; 

    private $controller;

    private $method;

    private $config;

    private $users;

    private $request;

    public int $priority = 2;

    private array $booted = [];


    public function __construct()
    {
        $this->config = container(Config::class);
        $this->users = container(Users::class, [container(Config::class)]);
    } 


    /**
     * This middleware's boot() method contains an associative array of key-action pairs, where if a key matches the current controller
     *      (in its lowercase name without the 'Controller' suffix) the outcome is that the routing will be handled according to the value 
     *      of the key.
     *      To achieve this outcome, this class MUST have a method matching a value of the key in the boot() method. 
     *      Note that the keys in boot() represent controller names. At every request, these keys here will be checked for a key that matches 
     *      the current request controller. 
     *      -There is an exception-if the value of the key is a boolean (true/false), then a method in this class matching the name of the 
     *       key wil be looked for and called. This method is then expected to return that boolean.
     *  
     *      Here is an example of the content of boot() and what its contents mean:
     * 
     *          public function boot()
     *           { 
     *               return [
     *                   'test' => true,
     *                   'jump' => false,
     *                   'category' => 'divert',
     *                   'seo' => 'authorised',
     *                   'admin' => 'authenticated'
     *               ];
     *           }
     * 
     *      -First of all, we need to understand that this middleware class is instantiated with two arguments; a controller and method which
     *         represent the exact target controller & method of the current request. 
     *         This could look like this: 
     * 
     *          Controller: 'controllers\TestController 
     *          Method:     'defaultAction'
     * 
     *      -According to the above example; there must be methods test() and jump() in this middleware class which return true and false 
     *       respectively. Following the above example again, of keys in the boot() method, the following methods will have to me in this class:
     * 
     *          test()
     *          jump()
     *          divert()
     *          authorised() 
     *          authenticated()
     * 
     *      -The above methods are for demonstrative purposes only. Feel free to add more options as your 
     *          application demands. Just remember to add an if statement block in DGZ_Router.php and DGZ_Controller where the
     *          Middleware call is being made, so you can capture and handle the response from your new Middleware methods. 
     * 
     *      -Remember that the logic in the methods should ideally result in an outcome described by the name of that value in the 
     *       booth() method. This will help make what the function is doing reasonable & clear for potential colleague developers.
     * 
     *      -As we know in DGZ, when making the request to a controller named TestController, the input in the URL would be 'test'
     *          So here in boot(), we have a 'test' key that will match this. Because its value is true, it implies that we should 
     *          run the test() method in this (middleware) class and ONLY proceed with the request to the intended controller and 
     *          method (TestController and defaultAction()) if the outcome of test() is true. This is not an explanation of how to
     *          run tests in DGZ. It is rather just a demonstration of how this middleware works.
     * 
     *      -If the value of the test key in boot() was false, then the opposite would have been true, as in, the intended request 
     *          will ONLY proceed with the request to TestController and defaultAction() if the outcome of test() is false. This 
     *          boolean true or false may be used for example to validate a request to a shop management page, whereby you run a
     *          check to see if the ID of the currently logged-in user matches that of the owner ID of the shop in question. You 
     *          would then return a true or false and in the calling script (in in DGZ_Router.php and DGZ_Controller), handle that 
     *          outcome and reject or proceed with the request accordingly.
     * 
     *      -If the value of the key in boot() is 'divert', then INSTEAD of finding a method in this middleware class that matches 
     *          the value of the boot() key, it finds a method that matches the actual boot() key and calls it. Secondly, the outcome 
     *          of calling this method (here in the middleware) MUST be a rerouting of the current request. Basically, the method  
     *          should modify the request target controller and method. It does this by simply returning an array containing three things;
     * 
     *              i) a controller name, 
     *              ii) the desired method to call on the new controller, 
     *              iii) & an optional array of arguments to pass to that method
     * 
     *          The idea is that the DGZ's routing process goes through this middleware, and redirect the request to a different
     *          controller & method from those of the original request. That is why we call it divert, because it is essentially 
     *          a re-routing, or diversion. 
     *          -If the rerouting is to a different method within the same controller, your custom function in here will return  
     *               to the caller the current controller and new method like so: 
     * 
     *                  return [$this->controller, 'newMethodName', [$argument(s)...]];
     * 
     *              Here's how the full custom function will look like:
     * 
     *                  public function myFunc($methodOptionalArgument = '')
     *                   {
     *                       return [$this->controller, 'newMethodName', [$methodOptionalArgument]];
     *                   }
     * 
     *          -You can also reroute to an entirely different controller from the current one. To do that; 
     *                  a) make sure you pull into this class file at the top, the new controller you will want to redirect to 
     *                      like so:
     * 
     *                      use Dorguzen\Controllers\DifferentController;
     * 
     *                  b) Then reroute in your custom function to the new controller like so:
     * 
     *                      public function myFunc($methodOptionalArgument = null)
     *                       {
     *                           return [DifferentController::class, 'newMethodName', [$methodOptionalArgument]];
     *                       }  
     *               
     * 
     *      -You can very easily make multiple requests to different controllers all target the same method in this 
     *          middleware class. This will work for cases where for example, you want to apply the same logic for many different 
     *          requests, say for example user authentication. It would be messy to write the same authentication function inside many 
     *          different middleware methods. This can be done by making the value of the boot() key for multiple request controller 
     *          names all match the same Middleware method (boot() key value). 
     *          -This is possible because before calling the Middleware class, the caller always makes two calls;
     * 
     *              i) one to get the value of the boot() key to know what the expected outcome of the routing should be
     *              ii) then it calls the Middleware's method that matches that value, using a conditional if statement 
     *                  to verify if the response matches what it knows to be the expected outcome. 
     * 
     *                  We can change this by making the value of boot() key the method to be called, instead of the the boot() key 
     *                  by simply switching the method paremeter being used in the caller from the boot() key to the boot() key value 
     *                  as a string. This means that instead of limiting that call to one controller in your application, as would have 
     *                  been the case if the Middleware function had to match the boot() key name-since array key names are unique; 
     *                  we can make the middleware method to call match the boot() key value instead, which allows us to have that 
     *                  same value repeated for multiple boot() keys if needed. We are therefore able to call the same middleware 
     *                  method for multiple requests from different controllers. Here is an example:
     * 
     *              public function boot()
     *              { 
     *                 return [
     *                   'test' => true,
     *                   'jump' => authorised,
     *                   'admin' => 'authorised',
     *                   'seo' => 'authorised',
     *                 ];
     *              }
     * 
     *              You will then make a modification in the Middleware caller so requests to jumpController, adminController & seoController 
     *              are all calling the authorised() middleware method like so
     * 
     *              $boot = $middleware->boot();
     *              if (array_key_exists($controllerShortName, $boot)) {
     *                 $middleWareIntent = $middleware->boot()[$controllerShortName];

     *                 if ($middleWareIntent === authorised) { 
     *                   //If its authorised, call the middleware method matching the value of the controller key (in this case authorised()) 
     *                      //not the key itself
     *                   //CHANGE: if (call_user_func([$middleware, $controllerShortName], $method)) { ... 
     *                          //which will call $boot->jump() 
     *                   //TO
     *                   if (call_user_func([$middleware, $middleWareIntent], $method)) { ... 
     *                          //which will call $boot->authorised()
     *                      //Request is authorised
     *                   }   
     *                   else { 
     *                      //request is not authorised, so throw an exception
     *                      throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 
     *                          'You are trying to visit a restricted area of this application.');
     *                   }
     *                 }
     *               }
     * 
     *              The middleware method authorised() will then check if the current user is logged in or not & return true or false accordingly.
     *          -Note, whenever you add an new middleware method that returns a new outcome, remember to add an if block for it both in DGZ_Router
     *              and DGZ_Controller.  
     *
     * @return array
     */
    public function boot(): array
    { 
        if (!empty($this->booted)) {
            return $this->booted;
        }

        return $this->booted = [
            'admin' => 'authenticated',
            'shop' => 'divert',
            'category' => 'divert',
            //-----Locations (list all location names here to be diverted to the LocationsController) ------//
            'bafoussam' => 'divert',
            'bamenda' => 'divert',
            'bertoua' => 'divert',
            'buea' => 'divert',
            'douala' => 'divert',
            'ebolowa' => 'divert',
            'garoua' => 'divert',
            'maroua' => 'divert',
            'ngaoundere' => 'divert',
            'yaounde' => 'divert',
            //-----Modules (list all here to activate their config restrictions) ------//
            'seo' => 'isActiveModule',
            'payments' => 'isActiveModule',
            'sms' => 'isActiveModule',
        ];
    }


    /**
     * handle() is the method that applies the main logic in the middleware.  
     * @param string $controller
     * @param string $controllerShortName is the lowercase alias of a controller without the 'Controller' part.
     * @param string $method the method in the target controller to handle the request. 
     * @throws DGZ_Exception
     * @return bool
     */
    public function handle(string $controller, string $controllerShortName, string $method): bool
    {
        $this->controller = $controller;
        $this->method = $method;

        /* // THIS HAS BEEN MOVED TO CsrfMiddleware.php - do delete from here when it's tested & working
        if ($this->checkCsrfProtection() === false)
        {
            throw new DGZ_Exception(
                'Not authorized', 
                DGZ_Exception::PERMISSION_DENIED, 
                'Invalid or missing CSRF token. If you submitted a form, make sure the form has a hidden field of
                the name _csrf_token, and its value the result of calling the global getCsrfToken() 
                function, or if it was an AJAX request, be sure to send the X-CSRF-TOKEN header with its value as the 
                generated token from getCsrfToken()'
            );
        }*/

        $boot = $this->booted ?? null;
        if (!array_key_exists(strtolower($controllerShortName), $boot)) {
            return true; // Not handled by this middleware
        }

        $intent = $boot[$controllerShortName];

        switch ($intent) {
            case 'authenticated': 
                if (!$this->authenticated()) {
                    throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You must be logged in to access this section.');
                }
                break;

            case 'authorized': 
                if (!$this->authorised())
                {
                    throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You are trying to visit a restricted area of this application.');
                }
                break;

            case 'isActiveModule': 
                //Check if a module is active & deny user access if not
                if (!$this->isActiveModule($controllerShortName)) {
                    throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You are trying access a non-existent module.');
                }
                break;

            case 'divert': 
                //call the middleware method and proceed with a new controller & or, method
                //$controller here will be sth like shopController, $newMethod is the desired method to call 
                //on shopController, & an optional array of args to pass to that method.
                list($controller, $newMethod, $args) = call_user_func([$this, strtolower($controllerShortName)], $method);

                $con = container($controller);
                $con->display($newMethod, $args, false); 
                //You must exit here to not break your layout/view files
                exit();
                break;

            case true: 
                if (!call_user_func([$this, strtolower($controllerShortName)], $method) === true)
                { 
                    throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You are trying to visit a restricted area of this application.');
                }
                break;

            case false: 
                if (!call_user_func([$this, strtolower($controllerShortName)], $method) === false) 
                { 
                    throw new DGZ_Exception('Not authorized', DGZ_Exception::PERMISSION_DENIED, 'You are trying to visit a restricted area of this application.');
                }
                break;
        }

        return true;
    }




    /**
     * Use this to restrict a resource to logged-in users
     */
    public function authenticated()
    {
        if (
            (isset($_SESSION['authenticated'])) && 
            ($_SESSION['authenticated'] == 'Let Go-'.$this->config->getConfig()['appName'])
        ) {
            return true;
        }
        else
        { 
            return false;
        }
    }


    /**
     * This method is for demonstration of this middleware.
     * You are free to refactor as you see fit, to suit your needs
     * It returns false if the user is not authenticated. You can then throw a 'not authorised' exception
     * @return boolean
     */
    public function authorised()
    {
        if (
            (isset($_SESSION['authenticated'])) && 
            ($_SESSION['authenticated'] == 'Let Go-'.$this->config->getConfig()['appName']) &&
            ($this->users->isAdmin($_SESSION['custo_id']))
        ) {
            return true;
        }
        else
        { 
            return false;
        }
    }


    /**
     * Checks in the site config if the given module in $moduleName is active
     * @param string $moduleName
     * @return boolean
     */
    public function isActiveModule($moduleName)
    { 
        if (
            (array_key_exists($moduleName, $this->config->getConfig()['modules'])) &&
            ($this->config->getConfig()['modules'][$moduleName] == 'on')
        )
        { 
            return true;
        }
        else
        { 
            return false;
        }
    }


    /**
     * This method is provided to you for your API development. It needs an ApiController in your controllers directory.
     * DGZ ships with an ApiController setup for you. This also demonstrates DGZ middelware divert behaviour.
     * @param $targetController
     * @return array
     * 
     */
    /*public function divert($targetController) { 
        if (preg_match('/-/', $targetController)) {
            $control = explode('-', $targetController);
            return [$this->controller, 'api', [$control[0], $control[1]]];
        }

        return [$this->controller, 'api', [$targetController]];
    }*/


    public function shop($methodOrShopName = '')
    {
        return [$this->controller, 'shop', [$methodOrShopName]];
    }

     
    /**
     * divert this request to the CategoryController, calling the 'resolveCategory' method on it, passing it 
     * an argument which should be the category name or ID to fetch
     * @param mixed $categoryNameOrId
     * @return array<array|mixed|string>
     */
    public function category($categoryNameOrId = null)
    {
        return [$this->controller, 'category', [$categoryNameOrId]];
    }

    /**
     * With locations its all about category IDs of category names, so the URL
     * '.../buea/20 will send a targetId 20 to the $_REQUEST array. This should be a category ID
     * '.../buea/clothes will send a value of 'clothes' as an argument to the receiving method 'handleLocation()'
     *  which will be the category to process in that location 
     * 
     * Notice how, in order for the LocationController's handleLocation() method to know which location is 
     *  requested, we MUST pass in the original controller of the request ($this->controller) to handleLocation().
     */
    public function bafoussam($categoryNameOrId = null)
    {
        return [LocationController::class, 'handleLocation', [$this->controller, $categoryNameOrId]];
    }
    public function bamenda($categoryNameOrId = null)
    { 
        return [LocationController::class, 'handleLocation', [$this->controller, $categoryNameOrId]];
    }
    public function bertoua($categoryNameOrId = null)
    {
        return [LocationController::class, 'handleLocation', [$this->controller, $categoryNameOrId]];
    }
    public function buea($categoryNameOrId = null)
    {
        return [LocationController::class, 'handleLocation', [$this->controller, $categoryNameOrId]];
    }
    public function douala($categoryNameOrId = null)
    {
        return [LocationController::class, 'handleLocation', [$this->controller, $categoryNameOrId]];
    }
    public function ebolowa($categoryNameOrId = null)
    {
        return [LocationController::class, 'handleLocation', [$this->controller, $categoryNameOrId]];
    }
    public function garoua($categoryNameOrId = null)
    {
        return [LocationController::class, 'handleLocation', [$this->controller, $categoryNameOrId]];
    }
    public function maroua($categoryNameOrId = null)
    {
        return [LocationController::class, 'handleLocation', [$this->controller, $categoryNameOrId]];
    }
    public function ngaoundere($categoryNameOrId = null)
    {
        return [LocationController::class, 'handleLocation', [$this->controller, $categoryNameOrId]];
    }
    public function yaounde($categoryNameOrId = null)
    {
        return [LocationController::class, 'handleLocation', [$this->controller, $categoryNameOrId]];
    }

}
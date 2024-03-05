<?php

namespace middleware;


use configs\Config;
use Users;

/**
 * Validate or divert requests
 *
 * Created by PhpStorm.
 * User: gustavndamukong
 * Date: 19/01/2021
 * Time: 12:28
 */
class Middleware
{

    private $controller;

    private $config;

    private $method;

    private $users;


    public function __construct($controller, $method)
    {
        $this->controller = $controller;
        $this->config = new Config();
        $this->method = $method;
        $this->users = new Users();
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
     *                   'api' => 'divert',
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
     *      -If the value of the key in boot() is 'divert', it means that the outcome of the method here in the middleware should
     *          modify the original target controller and method before returning them to the caller. It does this by simply 
     *          returning an array containing three things;
     *              i) a controller name, 
     *              ii) the desired method to call on the new controller, 
     *              iii) & an optional array of arguments to pass to that method
     *          The idea is that the DGZ's routing process goes through this middleware, and the return value is a different
     *          controller & method from those of the original request. That is why we call it divert, because it is essentially 
     *          a re-routing, or diversion. 
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
     *              if (array_key_exists($controllerInput, $boot)) {
     *                 $middleWareIntent = $middleware->boot()[$controllerInput];

     *                 if ($middleWareIntent === authorised) { 
     *                   //If its authorised, call the middleware method matching the value of the controller key (in this case authorised()) 
     *                      //not the key itself
     *                   //CHANGE: if (call_user_func([$middleware, $controllerInput], $method)) { ... 
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
    public function boot()
    { 
        return [
            'api' => 'divert',
            'admin' => 'authenticated',
            //-----Modules (list all here to activate their config restrictions) ------//
            'seo' => 'isActiveModule',
            'payments' => 'isActiveModule',
            'sms' => 'isActiveModule',
        ];
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
    public function divert($targetController) { 
        if (preg_match('/-/', $targetController)) {
            $control = explode('-', $targetController);
            return [$this->controller, 'api', [$control[0], $control[1]]];
        }

        return [$this->controller, 'api', [$targetController]];
    }
}
<?php

namespace middleware;


use configs\Config;

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

    private $method;


    public function __construct($controller, $method)
    {
        $this->controller = $controller;
        $this->method = $method;
    }


    /**
     * This middleware's boot() method contains an associative array of key-action pairs, where if a key matches the current controller
     *      (in its lowercase name without the 'Controller' suffix) the outcome is that the routing will be handled according to the value 
     *      of the key
     *      To achieve this outcome, this class MUST have a method with the matched key in boot(), which essentially is the controller name.
     *  Here is an example of the content of boot() and what it content means:
     * 
     *          public function boot()
     *           { 
     *               return [
     *                   'test' => true,
     *                   'jump' => false,
     *                   'api' => 'divert',
     *                   'seo' => 'authorised',
     *               ];
     *           }
     * 
     *      -First of all, we need to understand that this middle class is instantiated with a controller and method that the routing was intended 
     *          for. This could look like this: 
     * 
     *          Controller: 'controllers\TestController 
     *          Method:     'defaultAction'
     * 
     *      -Then for each key in boot() there must be a method in this Middleware class by that name. So in this Middleware class,
     *          we must have the following methods:
     * 
     *          test()
     *          jump()
     *          api()
     *          seo() 
     * 
     *      -There are 4 possible outcomes for your Middleware methods.
     *          -true
     *          -false
     *          -divert
     *          -authorised
     * 
     *          I have listed 4 keys (methods) to demonstrate the 4 possible outcomes. feel free to add mode options as your 
     *          application demands. Juest remember to add an if statement block in DGZ_Router.php and DGZ_Controller where the
     *          Middleware call is being made and then capture and handle the response. 
     * 
     *      -Remember that the logic of that method must result in an outcome described by the value of that key in booth()
     * 
     *      -As we know in DGZ, when making the request to a controller named TestController, the input in the URL would be 'test'
     *          So here in boot(), we have a 'test' key that will match this. Because its value is true, it implies that we should 
     *          run the test() method in this (middleware) class and ONLY proceed with the request to the intended controller and 
     *          method (TestController and defaultAction()) if the outcome of test() is true. This is not an explanation of how to
     *          run tests in DGZ. It is rather just a demonstration of how this middleware works.
     * 
     *      -If the value of the test key in boot() was false, then the opposite would have been true, as in, the intended request 
     *          will ONLY proceed with the request to TestController and defaultAction() if the outcome of test() is false. This 
     *          boolean true or false may be used for example to validate a request to a shop management pasge, whereby you run a
     *          check to see if the ID of the currently logged-in user matches that of the owner ID of the shop in question. You 
     *          would then return a true or false and in the calling script, handle that outcome and reject or proceed with the 
     *          request accordingly.
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
     *      -However, this approach of having each key in boot() represent a method in this Middleware class is limiting because it
     *          does that one controller at a time. That is great, but as your application grows, it may lead to a huge number of 
     *          methods needed to be added to the Middleware for as many controllers you want to add restrictions on. There is a 
     *          better way you can use to optionally make multiple requests to different controllers target only one method in this 
     *          middleware class. This will work for cases where for example you want to apply the same logic for many different 
     *          request, say user authentication. It would be messy to write the same authentication function inside many different
     *          middleware methods that match different controller names. This can be done by making the value of the boot() key the
     *          target method in the middleware to be called. 
     *          -This is possible because before calling the Middleware class, the caller always makes two calls;
     * 
     *              i) one to get the value of the boot() key to know what the expected outcome of the routing should be
     *              ii) then it calles the middleware's method that matches the name of the key, using a conditional if statement 
     *                  to verify if the response matches what it knows to be the expected outcome. 
     *                  We can change this by making the value of the key the method to be called by simple switching the method
     *                  paremeter being yused in the caller from the controller to the boot() key value as a string. This means that
     *                  instead of limiting that call to one controller in your application, we can have multiple request from 
     *                  different controllers have that one and the same string value, which represents a single method in this middleware 
     *                  class that will be called for all of them. here is an example:
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
     *              You will then make a modification in Middleware caller so requests to jumpController, adminController & seoController 
     *              are all calling the authorised() middleware method like so
     * 
     *              $boot = $middleware->boot();
     *              if (array_key_exists($controllerInput, $boot)) {
     *                 $middleWareIntent = $middleware->boot()[$controllerInput];

     *                 if ($middleWareIntent === authorised) { 
     *                   //If its authorised, call the value of the method matching the controller key (in this case authorised()) not the key itself
     *                   //CHANGE: if (call_user_func([$middleware, $controllerInput], $method)) { 
     *                   //TO
     *                   if (call_user_func([$middleware, $middleWareIntent], $method)) {
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
            'admin' => 'authorised',
            'seo' => 'authorised',
        ];
    }


    public function shop($methodOrShopName = '')
    {
        return [$this->controller, 'shop', [$methodOrShopName]];
    }

    /**
     * This method is for demonstration of this middleware.
     * You are free to refactor as you see fit, to suit your needs
     * It returns false if the user is not authenticated. You can then throw a 'not authorised' exception
     * @return boolean
     */
    public function authorised()
    {
        $config = new Config();
        if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$config->getConfig()['appName'])) {
            return true;
        }
        else
        { 
            return false;
        }
    }


    /**
     * This method is provided to you for your API development. It needs an ApiController in your controllers directory.
     * DGZ ships with an ApiController setup for you. This also demonstrates the second behaviour on the DGZ middelware-diversion.
     * @param $targetController
     * @return array
     * 
     */
    public function api($targetController) {
        if (preg_match('/-/', $targetController)) {
            $control = explode('-', $targetController);
            return [$this->controller, 'api', [$control[0], $control[1]]];
        }

        return [$this->controller, 'api', [$targetController]];
    }
}
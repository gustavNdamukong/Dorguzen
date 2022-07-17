<?php

namespace middleware;


use settings\Settings;

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
     * This middleware's boot() method contains an associative array of key-action pairs, where if a key matches the current controller;
     *      the action will be taken. There must also be a method with a name in this class that matches the array key in boot().
     * There are two possible behaviours/actions the middleware can take; check for a condition and return a boolean, or divert the route.
     * For example; if the current route's controller has a key in here:
     *      -i) The method name that matches the key will be called. If the value of the key is true, then the method must return true in order
     *          for the routing process of DGZ to carry on as normal, with the request being sent to the intended controller.
     *          If the value of the key is false, DGZ will deny access to the intended route and throw an exception displaying a 'Not authorised'
     *          view.
     *      -ii) The second behaviour happens if the value of the matching key in boot() is 'divert'. The method going by that same key name MUST
     *              then perform a rerouting of the current request. It does this by simply returning an array containing three things;
     *              a controller name, the desired method to call on the shopController, & an optional array of arguments to pass to that method
     *              The idea is that the DGZ's routing process goes through this middleware, and the return value is a different
     *              controller & method from those of the original request. That is why we call it divert
     *
     * @return array
     */
    public function boot()
    {
        return [
            'shop' => 'divert',
            'api' => 'divert',
            'authorised' => true
        ];
    }


    public function shop($methodOrShopName = '')
    {
        return [$this->controller, 'shop', [$methodOrShopName]];
    }

    /**
     * This method is for demonstration of this middleware-refactor to suit your needs
     * It returns false if the user is not authenticated. You can then throw a 'not authorised' exception
     * @return boolean
     */
    public function authorised()
    {
        $settings = new Settings();
        if ((isset($_SESSION['authenticated'])) && ($_SESSION['authenticated'] == 'Let Go-'.$settings->getSettings()['appName'])) {
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
     */
    public function api($targetController) {
        if (preg_match('/-/', $targetController)) {
            $control = explode('-', $targetController);
            //die($control[0].' - '.$control[1]);
            return [$this->controller, 'api', [$control[0], $control[1]]];
        }

        return [$this->controller, 'api', [$targetController]];
    }

}
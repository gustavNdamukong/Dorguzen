<?php

namespace controllers;

use DGZ_library\DGZ_Validate;
use DGZ_library\DGZ_Translator;
use ReflectionClass;
use ReflectionException;
use controllers\AuthController;


class ApiController extends \DGZ_library\DGZ_Controller
{

    public function __construct()
    {
        parent::__construct();
    }


    public function getDefaultAction()
    {
        return 'defaultAction';
    }


    public function defaultAction()
    {

    }




    public function api($targetController, $targetMethod)
    {
        //POST
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            //REQUESTS USING THIS
            /*
             * -login
             */

            $this->setHeaders();
            $controllerName = ucfirst($targetController).'Controller';

            //-----------TESTING (THIS WORKS TOO)-----------------
            /*$classReflector = new ReflectionClass('\controllers\\'.$controllerName);
            $object = $classReflector->newInstance();
            echo '<pre>';
            die(var_dump($object));*/
            //-----------END TESTING-------------------------------
            $classPath = '\controllers\\%s';
            $controller = sprintf(
                $classPath,
                $controllerName
            );

            $object = new $controller();
            //echo '<pre>';
            //die(print_r($_POST));
            //$object->$targetMethod(); die('DONE');
            $response = $object->$targetMethod();
            if ($response['status'] == 'true') {
                http_response_code(200);
                $response['response_code'] = 200;
            }
            else if ($response['status'] == 'false') {
                http_response_code(400);
                $response['response_code'] = 400;
            }

            die(json_encode($response));
        }

        //GET
        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            //REQUESTS USING THIS
            /*
             * -logout
             */

            $this->setHeaders();
            $controllerName = ucfirst($targetController).'Controller';

            $classPath = '\controllers\\%s';
            $controller = sprintf(
                $classPath,
                $controllerName
            );

            $object = new $controller();
            $response = $object->$targetMethod();
            if ($response['status'] == 'true') {
                http_response_code(200);
                $response['response_code'] = 200;
            }
            else if ($response['status'] == 'false') {
                http_response_code(400);
                $response['response_code'] = 400;
            }

            die(json_encode($response));

        }

        //PATCH
        if ($_SERVER['REQUEST_METHOD'] == 'PATCH') {
            $this->setHeaders();

        }

        //DELETE
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            $this->setHeaders();
        }
    }

    public function setHeaders()
    {
        header("Access-Control-Allow-Origin: ".$this->settings->getHomePage());
        //allow cookies to be used in the communication (once this is used, the '...Allow-Origin...' header above will no longer work with a wildcard
        //-u would have to explicitly spec the domain where these cookies are allowed to be used in. Tis is coz domains are not normally allowed to
        //be used across multiple platforms)
        header("Access-Control-Allow-Credentials: true");
    }

















}
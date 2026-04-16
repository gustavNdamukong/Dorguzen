<?php 

use Dorguzen\Core\DGZ_Request;
use Dorguzen\Core\DGZ_Response;
use Dorguzen\Core\DGZ_Application;
use Dorguzen\Core\DGZ_Logger;
use Dorguzen\Core\DGZ_Auth;
use Dorguzen\Core\DGZ_Messenger;
use Dorguzen\Core\DGZ_Exception;
use Dorguzen\Controllers\ExceptionController;
use Symfony\Component\VarDumper\VarDumper;
use Dorguzen\Config\Config;


/*/----------------------------------------------------------------------
|
|                    CUSTOM GLOBAL HELPER FUNCTIONS                  
|
//---------------------------------------------------------------------*/

// ----------------------------------------------------------------------
//  OPTIONAL FUNCTION NAME LIST FOR QUICK LOOK UP
// ----------------------------------------------------------------------
/*
    -testing()        retrieve DGZ_Auth instance (data on current user).


*/
// ----------------------------------------------------------------------


// ----------------------------------------------------------------------
// Function group heading here
// ----------------------------------------------------------------------

/**
 * testing function just to demonstrate (please delete or modify to use). 
 * This function can be called anywhere in your application
 */
if (!function_exists('testing()')) {
    function testing(): string
    {
        $config = container(Config::class);
        $appName = $config->has('app.appName') ? $config->get('app.appName') : "application";
        return "Test string from your awesome {$appName}";
    }
} 


// ----------------------------------------------------------------------
// Another function group heading here
// ----------------------------------------------------------------------
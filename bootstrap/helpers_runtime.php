<?php 

/*
    This file is meant to provide support to 'bootstrap/helpers.php'. 
    While the latter runs at boot time, it runs at runtime 
*/
$config = $GLOBALS['config'];
$env = ($config->getConfig()['live'] == 'true') ? 'live' : 'local';

//------------------------------------------------------------------------
// Leave this blank to momentarily allow requests from all proxies.
// Once you enter any proxy IP here, all other proxy requests will be 
// rejected except the ones entered here.
//------------------------------------------------------------------------
$trustedProxies = [
    // '127.0.0.1',
    // '10.0.0.1',
];
//------------------------------------------------------------------------



//------------------------------------------------------------------------
// Force browsing on HTTPS if user is detected to be on the non-secure URL
// This will only apply in the live/production environment.
//------------------------------------------------------------------------
enforceHttps($env, $trustedProxies);
//------------------------------------------------------------------------



//------------------------------------------------------------------------
//some global constants
//------------------------------------------------------------------------
//on a local MAMP server this points to something like: 'Applications/MAMP/htdocs/appname'
// but on a live server it may point to something like this: /home/i3v8zo1vaw30/public_html
define('CWD', getcwd());
//------------------------------------------------------------------------



//------------------------------------------------------------------------
// Optionally set default timezone, headers, etc.
//------------------------------------------------------------------------
date_default_timezone_set('UTC');
//------------------------------------------------------------------------

//------------------------------------------------------------------------
setupErrorHandling($env);
//------------------------------------------------------------------------
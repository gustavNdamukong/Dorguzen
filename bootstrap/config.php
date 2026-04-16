<?php 

use Dorguzen\Core\Config\EnvLoader;
use Dorguzen\Core\Config\ConfigLoader;
use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Exception;

// ensure DGZ_BASE_PATH is defined. This prevents accidental Composer execution forever.
if (!defined('DGZ_BASE_PATH')) {
    throw new DGZ_Exception('config.php must be loaded via index.php', DGZ_Exception::INVALID_CONFIG);
}

$configDir = DGZ_BASE_PATH . DIRECTORY_SEPARATOR . 'configs';
$cacheFile = DGZ_BASE_PATH . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'config.php';

// 1) load .env (optional)
$envLoader = new EnvLoader(DGZ_BASE_PATH);
$envLoader->load(); // env() helper will read them

// 2) load config files with caching
$loader = new ConfigLoader($configDir, $cacheFile, true);

$configArray = $loader->load();

// 3) instantiate repository and set the whole object into a global array
$GLOBALS['config'] = new Config($configArray);

// store the config object in DI container too
if (!isset($GLOBALS['container'])) {
    throw new RuntimeException('Container not initialized before config bootstrap.');
}
$container = $GLOBALS['container'];
$container->singleton(
    Config::class,
    fn () => $GLOBALS['config']
);

/*
    ONLY ONE INSTANCE of config/Config should ever be used; so, never instantiate the class. 
    Now you have three ways of retrieving the Config data globally without needing to ever instantiate it:
    -1) Viw the $GLOBALS variable e.g.
        $GLOBALS['config']->getConfig()
        $GLOBALS['config']->get();
        $GLOBALS['config']->get('app');
        $appName = $GLOBALS['config']->get('app.name');
        $GLOBALS['config']->all()

        // with fallback value
        $debug = $GLOBALS['config']->get('app.debug', false);


    -2) Via the global helper method
        // use the config() helper in bootstrap/helpers.php (this helps u not to type globals everywhere)

        config('app.name'); // its equivalent of $GLOBALS['config']->get('app.name');


    -3) Via the DI container e.g.

        $config = container(Config::class);
        $config->get('app.name');

        or

        $config->getConfig();

    // extra notes
    Why is there a return at the end of this bootstrap/config.php (below)?

    The reason is subtle and important. Here it is:

    The code:
    $GLOBALS['config'] = new Config($configArray);
    return $GLOBALS['config'];

    What this does

        It allows two valid usage styles:
            Style 1 (global, current)
                require_once 'bootstrap/config.php';
                config('app.name');

                Style 2 (explicit, future-proof)
                $config = require 'bootstrap/config.php';
                $config->get('app.name');

        This gives you:
            -CLI compatibility later
            -Testability (you can load config in isolation)
            -Optional DI container usage
            -Zero downside
            -Laravel, Symfony, and Slim all do this in some form.

         Think of bootstrap/config.php as a factory file, not just a script.
*/

return $GLOBALS['config'];






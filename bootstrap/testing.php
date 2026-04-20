<?php

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Container;
use Dorguzen\Core\Config\ConfigLoader;
use Dorguzen\Core\Config\EnvLoader;

use Dorguzen\Core\Database\Drivers\DGZ_DBDriverInterface;
use Dorguzen\Core\Database\Drivers\DGZ_MySQLiDriver;
use Dorguzen\Core\Database\Drivers\DGZ_SQLiteDriver;
use Dorguzen\Core\CLI\Application;

// This is the framework bootstrapping file for the testing environment ONLY.
// This is to avoid memory exhaustion, a classic issue arising when web bootstraps
// are reused in PHPUnit. We solve this by creating (this) a bootstrapping
// for testing that is separate from that of the web.

// Load Composer autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Define base path
if (!defined('DGZ_BASE_PATH')) {
    define('DGZ_BASE_PATH', dirname(__DIR__));
}

// Force testing env EARLY (authoritative) before your app's configs load.
// 🔑 Rule: This MUST always be declared before ever calling EnvLoader
putenv('APP_ENV=testing');
$_ENV['APP_ENV'] = 'testing';


//--------------------------------------------------------------------------------------
// Define the container early, & make it globally accessible
//--------------------------------------------------------------------------------------
$container = new DGZ_Container();

// make it globally accessible (There must be exactly ONE container instance in the entire request lifecycle.)
// a single source of container truth
$GLOBALS['container'] = $container;


// 1. Now load env
$envLoader = new EnvLoader(DGZ_BASE_PATH);
$envLoader->load();

// 2. Build config AFTER env
$configDir = DGZ_BASE_PATH . DIRECTORY_SEPARATOR . 'configs';
$cacheFile = DGZ_BASE_PATH . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'config.php';

// add config files-but IMPORTANT! NEVER use cache
$configLoader = new ConfigLoader($configDir, $cacheFile, false);
$configArray = $configLoader->load();

// 3) instantiate repository and set the whole object into a global array
$GLOBALS['config'] = $configClass = new Config($configArray);

// 3. Bind config into container
$container->singleton(Config::class, fn () => $configClass);

// Bind DB driver interface for tests

$dbData = $configArray['database']['DBcredentials'];

$dbCredentials = [
    'host'           => $dbData['host'],
    'username'       => $dbData['username'],
    'pwd'            => $dbData['pwd'],
    'db'             => $dbData['db'],
    'connectionType' => $dbData['connectionType'],
    'key'            => $dbData['key'],
    'sqlite_path'    => $dbData['sqlite_path'] ?? '',
];

$dbConnection = $dbData['connectionType'] ?? 'mysqli';

$container->singleton(
    DGZ_DBDriverInterface::class,
    fn () => $dbConnection === 'sqlite'
        ? new DGZ_SQLiteDriver($dbCredentials)
        : new DGZ_MySQLiDriver($dbCredentials)
);

$container->singleton(
    Application::class,
    fn () => new Application(container())
);

// Test formatter
// Bind DGZ_FormatterInterface to JSON formatter for testing
use Dorguzen\Core\DGZ_FormatterInterface;
use Dorguzen\Core\DGZ_JsonFormatter;

$container->singleton(DGZ_FormatterInterface::class, fn () => new DGZ_JsonFormatter());




// Optional helper binding if needed
if (!function_exists('container')) {
    function container(?string $abstract = null, array $parameters = [])
    {
        global $container;

        if (!$container) {
            $container = new DGZ_Container();
        }

        // If no class was requested, return the container itself (for bindings, etc.)
        if ($abstract === null || $abstract === '') {
            // ok if intentionally requested
            return $container;
        }

        static $resolving = [];

        // Detect circular recursion & return NULL to prevent an infinite loop
        if (isset($resolving[$abstract])) {
            error_log("⚠️  Circular dependency detected while resolving: {$abstract}");
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            return null;
        }

        // Mark this class as currently being resolved
        $resolving[$abstract] = true;

        try {
            $object = $container->get($abstract, $parameters);

            if (!is_object($object)) {
                throw new Exception("container() failed to resolve object for {$abstract}");
            }

            return $object;
        } finally {
            // Remove from stack after resolution
            unset($resolving[$abstract]);
        }
    }
}




// Helpers
require_once DGZ_BASE_PATH . '/bootstrap/helpers.php';
require_once DGZ_BASE_PATH . '/bootstrap/custom_helpers.php';

// DO NOT:
// ❌ start sessions
// ❌ enforce auth
// ❌ load routes
// ❌ bind models
// ❌ bind queues
// ❌ bind events
// ❌ touch cookies
// ❌ touch $_SESSION
// This is intentional minimalism.

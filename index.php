<?php ob_start();

use Dorguzen\Core\DGZ_Router;

// Define the application base path
define('DGZ_BASE_PATH', __DIR__);

$currentPage = basename($_SERVER['SCRIPT_FILENAME']);

// Load Composer autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

// Bootstrap the application
require_once __DIR__ . '/bootstrap/app.php';
require_once __DIR__ . '/bootstrap/config.php';
require_once __DIR__ . '/bootstrap/helpers.php';
require_once __DIR__ . '/bootstrap/custom_helpers.php';
require_once __DIR__ . '/bootstrap/helpers_runtime.php';


//------------------------------------------------------------------------
// Load defined routes
//------------------------------------------------------------------------
$router = new DGZ_Router($container->get(Dorguzen\Core\DGZ_Request::class));
DGZ_Router::setInstance($router);

$cachedRouteFile = DGZ_BASE_PATH . '/storage/cache/routes.php';
$routeFiles      = [__DIR__ . '/routes/web.php', __DIR__ . '/routes/api.php'];

if (file_exists($cachedRouteFile)) {
    $router->setRoutes(require $cachedRouteFile);
} else {
    foreach ($routeFiles as $file) {
        if (file_exists($file)) {
            include_once $file;
        }
    }
    $router->finalizeRoutes();
}

DGZ_Router::route();

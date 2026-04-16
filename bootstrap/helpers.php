<?php 

use Dorguzen\Core\DGZ_Request;
use Dorguzen\Core\DGZ_Response;
use Dorguzen\Core\DGZ_Application;
use Dorguzen\Core\DGZ_Router;
use Dorguzen\Core\DGZ_Logger;
use Dorguzen\Core\DGZ_Auth;
use Dorguzen\Core\DGZ_Messenger;
use Dorguzen\Core\DGZ_Exception;
use Dorguzen\Controllers\ExceptionController;
use Symfony\Component\VarDumper\VarDumper;
use Dorguzen\Core\Events\EventService;
use Dorguzen\Core\Queues\QueueManager;
use Dorguzen\Core\DGZ_Share;


/*/----------------------------------------------------------------------
|
|                    GLOBAL HELPER FUNCTION & VARIABLES                    
|
//---------------------------------------------------------------------*/

// ----------------------------------------------------------------------
//  AVAILABLE GLOBAL HELPER FUNCS
// ----------------------------------------------------------------------
/*
    -Auth()                 retrieve DGZ_Auth instance (data on current user).

    -dump()                 dumps the given resourse using Symfony's VarDumper() that has 
                            nice colour coding. It does not stop execution, so its suitable for 
                            debugging inline.
    
    -dd()                   From Symfony (vendor/symfony/var-dumper/Resources/functions/dump.php) 

    -dgzie()                A rich debugger from DGZ, similar to Laravel's dd(); It wraps Symfony's 
                            symfony/var-dumper dependency. Inspired by Symfony's dd() but with added 
                            syntactical sugar.


    -request()              returns the DGZ_Request object.

    -response()             returns the DGZ_Response object.

    -app()                  returns the DGZ_Application object.

    -config()               It returns Dorguzen\Configs\Config->getConfig().

    -env()                  Get the current environment info

    -config_path()          Locate configs directory

    -createCsrfToken()      creates & returns a CSRF token.

    -getCsrfToken()         gets, of creates & returns a CSRF token.

    -enforceHttps()         used internally to enforce HTTPS.

    -setUpErrorHAndling()   used to set the DGZ custom error handler

    -base_path()        Get the root path of the application.
                            It gives you the same result as the 
                            global constant DGZ_BASE_PATH, and 
                            appends whatever path string you pass to it.
    
    -core_path()        Get the root path of the application.
                            It gives you the same result as 
                            base_path() and the global constant 
                            DGZ_BASE_PATH

    -route()            Route a request to a particular route using the 
                        user-defined route name. Pass in the route name 
                        as an argument, and optionally, any route 
                        parameters.

    -event()            dispatch an Event's listener(s) for processing

    -dispatch()         dispatch a Job to the queue

    -queue()            dispatch a Job to the queue

*/
// ----------------------------------------------------------------------


// ----------------------------------------------------------------------
// Global helper: Current (authenticated) User
// ----------------------------------------------------------------------
/**
 * Retrieve the global DGZ_Auth instance.
 *
 * Usage in controllers:
 *   if (!Auth()->check()) {
 *      return redirect('/login');
 *   }
 *   
 *   if (Auth()->isAdmin()) {
 *       // admin-only area
 *   }
 * 
 * Usage in views:
 * 
 *      <?php if (Auth()->check()): ?>
 *          <p>Welcome back, <?= Auth()->username(); ?>!</p>
 *      <?php else: ?>
 *          <a href="/login">Login</a>
 *      <?php endif; ?>
 *   
 * Admin-only view content:
 *
 *   <?php if (Auth()->isAdmin()): ?>
 *      <a href="/admin">Go to Admin Panel</a>
 *   <?php endif; ?>
 * 
 * @return DGZ_Auth
 */
if (!function_exists('Auth()')) {
    function Auth(): DGZ_Auth
    {
        static $auth = null;
        if ($auth === null) 
        {
            $auth = container(DGZ_Auth::class);
        }
        return $auth;
    }
}

// ----------------------------------------------------------------------
// Global helper: Request instance
// ----------------------------------------------------------------------
if (!function_exists('request')) {
    /**
     * Retrieve the global DGZ_Request instance.
     *
     * Usage:
     *   $name = request()->get('name');
     *   $json = request()->getJson();
     * 
     * @return DGZ_Request
     */
    function request(): DGZ_Request
    {
        return container(DGZ_Request::class);
    }
}



if (!function_exists('dump')) {
    /**
     * Dump the passed variables using Symfony's VarDumper
     * (from the symfony/var-dumper package).
     * dump() doesn’t terminate — you can use it mid-execution.
     *
     * @param  mixed  ...$vars
     * @return void
     */
    function dump(...$vars): void
    {
        foreach ($vars as $v) {
            VarDumper::dump($v);
        }

        // Symfony’s VarDumper doesn’t automatically terminate — 
        // that’s dgzie()’s job, not dump()’s.
    }
}



if (!function_exists('dgzie')) {
    /**
     * Dump the passed variables and end the script.
     * dgzie() terminates immediately after dumping.
     * 
     * You can call it in any of these ways:
     *
     *  dgzie($user);
     *  dgzie($user, $posts, $config);
     *  dgzie(['user' => $user, 'posts' => $posts]);
     *
     * @param  mixed  ...$vars
     * @return never
     */
    function dgzie(...$vars): void
    {
        // Detect context (web or CLI)
        $isCli = php_sapi_name() === 'cli';

        // Add request info if running under web server
        if (!$isCli && isset($_SERVER['REQUEST_METHOD'])) {
            $requestInfo = [
                'method' => $_SERVER['REQUEST_METHOD'] ?? null,
                'uri' => $_SERVER['REQUEST_URI'] ?? null,
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'script' => $_SERVER['SCRIPT_NAME'] ?? null,
            ];

            echo "<div style='background:#1e1e1e;color:#eee;padding:10px;border-radius:8px;font-family:monospace'>";
            echo "<strong>🧭 Request Context</strong><br>";
            echo "<pre>" . htmlspecialchars(print_r($requestInfo, true)) . "</pre>";
            echo "</div><br>";
        }

        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0];
        $file = $trace['file'] ?? 'unknown';
        $line = $trace['line'] ?? 'unknown';

        echo "<small style='color:#888;'>Called from <b>{$file}</b> on line <b>{$line}</b></small><br>";

        // Try to dump DGZ_Request object if available
        try {
            $request = container(DGZ_Request::class);
            if ($request) {
                echo "<div style='background:#282c34;color:#dcdcdc;padding:10px;border-radius:8px;margin-bottom:10px;font-family:monospace'>";
                echo "<strong>🌐 DGZ Request Object</strong><br>";
                VarDumper::dump($request);
                echo "</div><br>";
            }
        } catch (Throwable $e) {
            echo "<small style='color:#888;'>['dgzie()'] Could not dump DGZ_Request: {$e->getMessage()}</small><br>";
        }

        // Dump provided variables
        foreach ($vars as $v) {
            VarDumper::dump($v);
        }
        exit(1);
    }
}



// ----------------------------------------------------------------------
// Global helper: Response instance
// ----------------------------------------------------------------------
if (!function_exists('response')) {
    /**
     * Retrieve the global DGZ_Response instance.
     *
     * Usage:
     *   return response()->json(['success' => true]);
     *   return response()->setStatusCode(404)->send();
     * 
     * @return DGZ_Response
     */
    function response(): DGZ_Response
    {
        return container(DGZ_Response::class);
    }
}


/** @return DGZ_Application */
if (!function_exists('app')) {
    function app(): DGZ_Application
    {
        return container(DGZ_Application::class);
    }
}



// ----------------------------------------------------------------------
//                  Config helper function
// ----------------------------------------------------------------------
/**
 * Usage:
 * 
 *      config('app.name') 
 * OR 
 *      config() to get all
 * OR 
 *      config('events') to get all events & their listeners
 * OR
 *      config('database')['DBcredentials']
 *
 * @param string|null $key
 * @param mixed $default
 * @return mixed
 */
if (!function_exists('config')) {
    function config(?string $key = null, $default = null)
    {
        if (!isset($GLOBALS['config'])) {
            throw new DGZ_Exception(
                'Wrong use of Config object',
                DGZ_Exception::EXCEPTION,
                "Config repository not initialized.."
            );
        }

        if ($key === null || $key === '') {
            return $GLOBALS['config']->all();
        }

        return $GLOBALS['config']->get($key, $default);
    }
}
// ----------------------------------------------------------------------




// ----------------------------------------------------------------------
//                  Get the current environment info
// ----------------------------------------------------------------------
/**
 * env('APP_ENV', 'production')
 * safely read system environment variables no matter how PHP is configured
 *      It uses getenv() and $_ENV
 *      It is Request-scoped (lasts the lifespan of the current request) 
 *      It is immutable 
 *      It is ideal for secrets and per-machine values
 * 
 * Meant to be used by config files only.
 * For example, quickly check what environment you are in:
 * 
 *      $env = env('APP_ENV', 'production');
 * 
 * This code gets the currently set environment, or sets it to 'production' 
 * if none is specified.
 */
if (!function_exists('env')) { 
    function env(string $key, $default = null)
    {
        // Never rely on getenv() as primary. But use it first, if set.
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }

        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }

        if (isset($_SERVER[$key])) {
            return $_SERVER[$key];
        }

        return $default;
    }
}
// ----------------------------------------------------------------------



// ----------------------------------------------------------------------
//                  Get the Config path
// ----------------------------------------------------------------------
/**
 * config_path helper to locate configs directory. It resolves absolute config directory path
 * 
 * define('DGZ_BASE_PATH', '/var/www/myapp');
 * config_path();
 * 
 * This will result in: 
 *      /var/www/myapp/configs
 * 
 * config_path('app.php');
 * 
 * This will result in: 
 *      /var/www/myapp/configs/app.php
 * 
 * You can load a config file manually:
 *      require config_path('app.php');
 * 
 * Load a module config:
 *      require config_path('modules/blog.php');
 * 
 * Having this function
 *      -This makes Dorguzen portable
 *      -Makes CLI possible
 *      -Avoids brittle includes
 *      -Matches the patterns of other frameworks out there
 * 
 */
function config_path(string $fileName = ''): string
{
    $base = defined('DGZ_BASE_PATH') ? DGZ_BASE_PATH : __DIR__;
    $cfg = rtrim($base . DIRECTORY_SEPARATOR . 'configs', DIRECTORY_SEPARATOR);
    return $fileName ? $cfg . DIRECTORY_SEPARATOR . $fileName : $cfg;
}
// ----------------------------------------------------------------------




// ----------------------------------------------------------------------
/**
 * You can now do this anywhere in your application to get the root path:
 * 
 *  base_path()
 * 
 * Example use: 
 *  base_path("src/Jobs/className.php") will produce:
 * 
 *      yourAppName/src/Jobs/className.php
 */
// ----------------------------------------------------------------------
if (!function_exists('base_path')) { 
    function base_path(string $path = ''): string
    {
        $base = defined('DGZ_BASE_PATH')
            ? DGZ_BASE_PATH
            : dirname(__DIR__, 1);

        return $path ? $base . DIRECTORY_SEPARATOR . ltrim($path, '/') : $base;
    }
}

// ----------------------------------------------------------------------
/**
 * You can now do this anywhere in your application to get the core path:
 * 
 *  core_path()
 */
// ----------------------------------------------------------------------
if (! function_exists('core_path')) {
    function core_path(string $path = ''): string
    {
        return base_path('core' . ($path ? '/' . ltrim($path, '/') : ''));
    }
}
// ----------------------------------------------------------------------




// ----------------------------------------------------------------------
/**
 * You can now do this anywhere in your application:
 * 
 *  createCsrfToken()
 */
// ----------------------------------------------------------------------
if (!function_exists('createCsrfToken')) { 
    function createCsrfToken()
    {
        // get it from container
        $request = request();
        return $request->createCsrfToken();
    }
}
//------------------------------------------------------------------------


//------------------------------------------------------------------------
/**
 * You can now do this anywhere in your application:
 * 
 *  getCsrfToken()
 */
// ----------------------------------------------------------------------
if (!function_exists('getCsrfToken')) { 
    function getCsrfToken()
    {
        // get it from container
        $request = request();
        return $request->getCsrfToken();
    }
}
// ----------------------------------------------------------------------




// ----------------------------------------------------------------------
/**
 * Enforce HTTPS in production
 * It redirects to HTTPS if not on HTTPS (safe & robust).
 * 
 * How to use this function:
 *  Trusting forwarded headers: Only set $trustedProxies to IPs you control (load balancer / reverse proxy). 
 *  If you leave it empty, the function will accept forwarded headers from any client — that’s convenient 
 *  but less secure. If you want strict security, fill the list.
 *  Local testing: Keep $env non-production while developing so you don’t get redirected to non-existent HTTPS.
 *  HSTS header: I included Strict-Transport-Security — only use this after you’re sure HTTPS is stable on your 
 *  domain (it tells browsers to always use HTTPS).
 *  Exit after redirect: always exit after header('Location:...') to prevent further code execution.
 *  HTTP_HOST vs SERVER_NAME: HTTP_HOST is preferred (it keeps port if present). SERVER_NAME can differ 
 *  depending on server config.
 *
 * - Only runs when $env === 'live' (so you can test locally).
 * - Detects HTTPS using multiple signals:
 *   - $_SERVER['HTTPS']
 *   - $_SERVER['SERVER_PORT'] == 443
 *   - X-Forwarded-Proto (common for proxies)
 *   - X-Forwarded-Ssl (on)
 *   - CF-Visitor (Cloudflare)
 *
 * @param string $env         current environment, e.g. 'live' or 'local'
 * @param array  $trustedProxies Optional list of proxy IPs to trust for forwarded headers (optional)
 */
// ----------------------------------------------------------------------
function enforceHttps(string $env = 'live', array $trustedProxies = [])
{
    // Don't run for CLI or in non-production environment
    if (php_sapi_name() === 'cli' || $env !== 'live') {
        return;
    }

    // Optional: if you passed trusted proxies and the remote addr isn't in that list,
    // ignore forwarded headers (more secure).
    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? null;
    $useForwardedHeaders = empty($trustedProxies) || 
        ($remoteAddr && in_array($remoteAddr, $trustedProxies, true));

    // Helper to check if proto header includes https (handles "https,http" lists)
    $isProtoHttps = function ($value) {
        if (!is_string($value) || $value === '') return false;
        $value = strtolower($value);
        // sometimes X-Forwarded-Proto can contain a comma-separated list, check for 'https'
        return strpos($value, 'https') !== false;
    };

    // Detect HTTPS via multiple signals
    $isHttps = false;

    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') 
    {
        $isHttps = true;
    } 
    elseif (!empty($_SERVER['SERVER_PORT']) && (int)$_SERVER['SERVER_PORT'] === 443) 
    {
        $isHttps = true;
    } 
    elseif ($useForwardedHeaders && !empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && 
        $isProtoHttps($_SERVER['HTTP_X_FORWARDED_PROTO'])) 
    {
        $isHttps = true;
    } 
    elseif ($useForwardedHeaders && !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && 
        strtolower($_SERVER['HTTP_X_FORWARDED_SSL']) === 'on') 
    {
        $isHttps = true;
    } 
    elseif ($useForwardedHeaders && !empty($_SERVER['HTTP_CF_VISITOR']) && 
        strpos($_SERVER['HTTP_CF_VISITOR'], '"scheme":"https"') !== false) 
    {
        // Cloudflare provides CF-Visitor: {"scheme":"https"}
        $isHttps = true;
    }

    if ($isHttps) {
        return; // already HTTPS — nothing to do
    }

    // Build host (prefer HTTP_HOST which can include port)
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
    $uri  = $_SERVER['REQUEST_URI'] ?? '/';

    // Avoid redirect loops: if host is empty, bail out
    if ($host === '') {
        return;
    }

    $newUrl = "https://{$host}{$uri}";

    // Use 301 for permanent redirect
    header("Location: {$newUrl}", true, 301);
    // Optional: add Strict-Transport-Security header (only when you are sure HTTPS is properly configured)
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    exit;
}
// ----------------------------------------------------------------------






//------------------------------------------------------------------------
//                  CUSTOM ERROR HANDLING
//------------------------------------------------------------------------

/* This is called in the front controller (index.php) like so: 

    setupErrorHandling();
*/
function setupErrorHandling($env): void
{
    //turn PHP errors on
    /*
    By default, PHP already has its own built-in error handler that:
    Displays errors (if display_errors is on)
    Or logs them (if log_errors is on)

    If you’re happy using PHP’s built-in error display/logging, you don’t need to 
    define your own set_error_handler().
        In other words:
    ini_set() + error_reporting() already configure PHP’s built-in error handling.
    */
    
    if ($env === 'local') {
        ini_set('display_errors', '1');
        ini_set('display_startup_errors', '1');
        error_reporting(E_ALL);
    } else {
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        ini_set('log_errors', '1');
        ini_set('error_log', __DIR__ . '/logs/php_errors.log');
        error_reporting(0);
    }

    set_error_handler(
        function($errno, $errstr, $errfile = '', $errline = 0, $errcontext = []) {

            try {
                switch ($errno) {
                    case E_COMPILE_ERROR:
                        $errorType = DGZ_Exception::PHP_FATAL_ERROR;
                        break;
                    case E_WARNING:
                        $errorType = DGZ_Exception::PHP_WARNING;
                        break;
                    case E_NOTICE:
                        $errorType = DGZ_Exception::PHP_NOTICE;
                        break;
                    case E_ERROR:
                        $errorType = DGZ_Exception::PHP_ERROR;
                        break;
                    default:
                        $errorType = DGZ_Exception::PHP_OTHER_ERROR;
                        break;
                }

                if (is_array($errcontext)) {
                    $errcontextstring = print_r($errcontext, true);
                }
                elseif (is_object($errcontext)) {
                    $errcontextstring = print_r($errcontext, true);
                }
                else {
                    $errcontextstring = $errcontext;
                }

                $hintstr = <<<TEXT
                This is an internal error generated by the server and usually indicates a programming error.
                                
                File: {$errfile}
                Line: {$errline}
                Context: {$errcontextstring}
    TEXT;

                throw new DGZ_Exception($errstr, $errorType, $hintstr);
            }
            catch (\Exception $e)
            {  
                $hint = '';
                $traceHint = '';
                $message = '';
                $file = '';
                $line = '';
                $time = date("d-m-y h:i:s");

                // Is this a DGZ_Exception?
                if ($e instanceof DGZ_Exception) {

                    $hint = nl2br(htmlspecialchars($e->getHint()));
                    if($e->getTraceAsString()) {
                        $traceHint = nl2br(htmlspecialchars($e->getTraceAsString()));
                    }
                    $message = nl2br(htmlspecialchars($e->getMessage()));
                    $file = $e->getFile();
                    $line = $e->getLine();
                }
                else {
                    // If it's a normal exception then just use the default view
                    $message = nl2br(htmlspecialchars($e->getMessage()));
                    if($e->getTraceAsString()) {
                        $hint = nl2br(htmlspecialchars($e->getTraceAsString()));
                    }
                }

                //We have thrown an exception
                $logs = container(DGZ_Logger::class);
                $exceptionTitle = 'Runtime error';
                $exceptionData = 'Hint: From index page'.$hint.'<br>'.
                    '| Trace Hint: <b>'.$traceHint.'</b><br>'.
                    '| Message: <b>'.$message.'</b><br>'.
                    '| File: <b>'.$file.'</b><br>'.
                    '| Line: <b>'.$line.'</b><br>'.
                    '| Time: <b>'.$time.'</b><br>';
                $logs::log($exceptionTitle, $exceptionData);
                error_log($exceptionData); 

                //Determine if we are live, then Send email to the site admin about this error
                if (config('live') == 'true')
                {
                    $messenger = new DGZ_Messenger();
                    $send = $messenger->sendErrorLogMsgToAdmin($message);
                }

                $controller = new ExceptionController();
                $controller->addException($e);
                $controller->redirect('exception', 'error');
            }
        }
        /* 
            Returning true prevents PHP’s internal handler 
            from also processing the same error.
        */
        //return true;
    );
}



// ------------------------------------------------------------------------
//             PERFORM ROUTE OPERATIONS WITH EASE
// ------------------------------------------------------------------------
/**
 * 
 * 
 * Example usage:
 * 
 *      $router->get('/products/{id}', 'ProductsController@show')->name('products.show');
 *      echo route('products.show', ['id' => 42]);
 *      // will output /products/42
 * 
 *  In an anchor tag:
 *      <a href="<?=route('users', ['id' => 6])?>">
 *          Users
 *      </a>
 */
function route(string $name, array $params = []): string
{ 
    $router = DGZ_Router::getInstance(); 

    $route = $router->getRouteByName($name);

    if (!$route) {
        throw new Exception("Route [{$name}] not found.");
    }

    $uri = $route['uri'];

    foreach ($params as $key => $value) {
        $uri = str_replace('{' . $key . '}', $value, $uri);
    }

    $basePath = rtrim(container(\Dorguzen\Config\Config::class)->getFileRootPath());

    return $basePath . ltrim($uri, '/');
}


// ------------------------------------------------------------------------
//             GET THE CURRENTLY ACTIVE ROUTE
// ------------------------------------------------------------------------
/**
 * Returns an array representing the currently active route.
 *
 * @return array  [$controller, $method, $controllerInput, $straightUrlId]
 *
 * Example usage:
 *      [$controller, $method] = getCurrentRoute();
 *      // $controller → 'AdminController', $method → 'contactMessages'
 *
 *      if (strtoupper(getCurrentRoute()[1]) !== 'COMMERCIALS') { ... }
 */
function getCurrentRoute(): array
{
    try {
        return DGZ_Router::getInstance()->getCurrentRoute();
    } catch (\Throwable $e) {
        // The current URL may not map to a valid controller (e.g. during error page rendering).
        // Return safe defaults so layouts don't blow up when included on exception pages.
        return ['', ''];
    }
}




// ------------------------------------------------------------------------
//             DISPATCH THE LISTENER OF AN EVENT FOR PROCESSING
// ------------------------------------------------------------------------
/**
 * It is recommended to use event() to dispatch Events, as Events 
 * are meant to pass through the EventService class pipeline. 
 * 
 * Events are not candidates for the queueing system, though an Event may be 
 * marked for queueing by making its listener implement the ShouldQueue interface
 * (core/events/ShouldQueue.php), in which case, event() will detect that internally 
 * and hand the Event over to the queue system to be dispatched as a job.
 * 
 * Example usage:
 *  event(new TestEvent('hello'));
 */
if (!function_exists('event')) {
    function event(object $event): void
    {
        container(EventService::class)->dispatch($event);
    }
}




// ------------------------------------------------------------------------
//                  DISPATCH A JOB TO THE QUEUE
// ------------------------------------------------------------------------
/**
 * It is not recommended to use dispatch() to dispatch Events, as Events 
 * are meant to pass through the EventService class pipeline. 
 * dispatch() on the otherhand is intended for jobs, because jobs are first class 
 * candidates of the queues system.
 * 
 * Example usage:
 *  dispatch(new SendEmailJob($user));
 *  dispatch(new TestJob(), 10);
 */
if (!function_exists('dispatch')) {
    function dispatch(object $job, ?int $delaySeconds = null): void
    {
        container(QueueManager::class)->push($job, $delaySeconds);
    }
}




// ------------------------------------------------------------------------
//                  DISPATCH A JOB TO THE QUEUE
// ------------------------------------------------------------------------
/**
 * queue() does the exact same thing as dispatch(), pushing a job to the 
 * queue. It is meant as an extra, more aptly-named helper for dispatching 
 * jobs. Use whichever sounds best to you; queue() or dispatch(). 
 * 
 * Example usage:
 *  queue(new SendEmailJob($user));
 *  queue(new TestJob(), 10);
 */
if (!function_exists('queue')) {
    function queue(object $job, ?int $delaySeconds = null): void
    {
        container(QueueManager::class)->push($job, $delaySeconds);
    }
}


// ------------------------------------------------------------------------
//                  SOCIAL MEDIA SHARE BUTTONS
// ------------------------------------------------------------------------
/**
 * Render social media share buttons for the given URL.
 *
 * Usage in any view:
 *   <?= shareButtons($currentUrl, $adTitle) ?>
 *
 * Override which platforms appear:
 *   <?= shareButtons($url, $title, ['platforms' => ['whatsapp', 'facebook', 'copy']]) ?>
 *
 * Available platforms: 'facebook', 'whatsapp', 'twitter', 'email', 'copy'
 *
 * @param string $url     The URL to share.
 * @param string $title   Short title or description sent with the share.
 * @param array  $options Optional: 'platforms', 'label', 'size', 'class'
 * @return string         HTML string — echo it directly in your view.
 */
if (!function_exists('shareButtons')) {
    function shareButtons(string $url, string $title = '', array $options = []): string
    {
        return DGZ_Share::buttons($url, $title, $options);
    }
}

// ------------------------------------------------------------------------
//                  SETUP LOGGING FUNCTIONALITY
// ------------------------------------------------------------------------
// DGZ_Logger::init(__DIR__ . '/../storage/logs');
// DGZ_Logger::info("Dorguzen Framework booted…");

// ------------------------------------------------------------------------
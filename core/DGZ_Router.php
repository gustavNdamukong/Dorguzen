<?php

namespace Dorguzen\Core;

use Dorguzen\Config\Config;
use ReflectionClass;
use ReflectionException;
use Exception;
use ReflectionNamedType;
use Dorguzen\Models\Logs;
use Dorguzen\Core\DGZ_Request;
use Dorguzen\Controllers\ExceptionController;
use Dorguzen\Core\DGZ_Exception; 
use Dorguzen\Core\Exceptions\ValidationException;
use Dorguzen\Core\Psr\PsrRequestAdapter;
use Dorguzen\Core\Psr\SimpleRequestHandler;
use Dorguzen\Core\DGZ_MiddlewareInterface;
use Dorguzen\Core\Kernel\HttpKernel;
use Dorguzen\Testing\Http\KernelResponse;
use RuntimeException;

/**
 * Description of DGZ_Router
 *
 * @author Gustav Ndamukong
 */
class DGZ_Router {

    //------------------------------ DEFINED ROUTES ------------------------------------
    /**
     * Holds all registered routes.
     * Each route is a flat associative array.
     */
    protected array $routes = []; 


    // This is a lookup for routes, structured like a DB index
    protected array $namedRoutes = [];

    /**
     * middlewareStack will allow routes to be grouped under a middleware.
     * It will store the group of middlewares to be applied to the given route(s)
     * @var array
     */
    protected array $middlewareStack = [];

    protected DGZ_Request $request; 

    // Static property to hold a single instance of this object
    protected static ?DGZ_Router $instance = null;

    public function __construct(DGZ_Request $request)
    {
        $this->request = $request;
        // when a router is instantiated, remember it globally
        // this creates a static entry point which static methods
        // can use to call non-static methods. This also allows  
        // the same instance of this class to be used all over 
        // the application in a singleton fashion  
        self::$instance = $this;
    }

    /**
     * Ensure we always have a valid instance. Though this is a static 
     * method, it returns an instance (new or existing one), so you not only 
     * end up with the same object, but it's a gateway from which you can call 
     * all members on this class regardless of their visibility. Call it like so:
     * 
     *      $router = self::getInstance();
     * 
     * You can then call other properties in this class like this:
     * 
     *      $data = $router->someProperty;
     */
    public static function getInstance(): DGZ_Router
    {
        if (!self::$instance) {
            throw new RuntimeException('Router not bootstrapped');
            // or: return new self(container(DGZ_Request::class)); // careful: can recurse
        }
        return self::$instance;
    }


    public static function bootstrap(DGZ_Request $request): self
    {
        self::$instance = new self($request);
        return self::$instance;
    }


    public static function setInstance(DGZ_Router $router): void
    {
        self::$instance = $router;
    }


    // Used to register named routes
    public function registerNamedRoute(string $name, array &$route): void
    {
        $this->namedRoutes[$name] = &$route;
    }


    public function getRouteByName(string $name): ?array
    {
        return $this->namedRoutes[$name] ?? null;
    }

    public function getNamedRoutes(): ?array
    {
        return $this->namedRoutes ?? [];
    }

    /**
     * To be called from the front controller to ensure that 
     * named routes are initialised when routes are being loaded 
     * from files because they are not cached.
     * @return void
     */
    public function finalizeRoutes(): void
    {
        foreach ($this->routes as &$route) {
            if (!empty($route['name'])) {
                $this->namedRoutes[$route['name']] = &$route;
            }
        }
    }


    /**
     * middleware() is used to a middleware the developer intends to apply to the given route(s).
     * Routes registered after this inherit it.
     * It supports nesting naturally (return $this)
     * To store groups to the middleware context, group() is called on it, which contains the routes, 
     * to come under that middleware.
     * Note that the calling code ($this->group()) will clear that entry after each call
     * to clear it up for other route definitions that may need middleware groupings. 
     * @param array $middleware
     * @return DGZ_Router
     */
    public function middleware(array $middleware): self
    {
        $this->middlewareStack[] = $middleware;
        return $this;
    }


    /**
     * Executes a group of routes under the current middleware stack.
     * Once the group finishes, the middleware is popped.
     * This ensures that middleware applies only inside the group
     * and does not leak to other routes to be added subsequently.
     */
    public function group(\Closure $callback): void
    {
        $callback($this);

        // Remove the last middleware layer after group finishes
        array_pop($this->middlewareStack);
    }

    /*public function get(string $uri, string $action, string $apiVersion = '', string $module = '')    { $this->addRoute('GET', $uri, $action, $apiVersion, $module); }
    public function post(string $uri, string $action, string $apiVersion = '', string $module = '')   { $this->addRoute('POST', $uri, $action, $apiVersion, $module); }
    public function put(string $uri, string $action, string $apiVersion = '', string $module = '')    { $this->addRoute('PUT', $uri, $action, $apiVersion, $module); }
    public function delete(string $uri, string $action, string $apiVersion = '', string $module = '') { $this->addRoute('DELETE', $uri, $action, $apiVersion, $module); }

    protected function addRoute(string $method, string $uri, string $action, string $apiVersion = '', $module = ''): void
    {
        $this->routes[] = compact('method', 'uri', 'action', 'apiVersion', 'module');
    } */

    public function get(string $uri, string $action, string $module = '')    
    { 
        return $this->addRoute('GET', $uri, $action, '', $module); 
    
    }

    public function post(string $uri, string $action, string $module = '')   
    { 
        return $this->addRoute('POST', $uri, $action, '', $module); 
    
    }

    public function patch(string $uri, string $action, string $module = '')    
    { 
        return $this->addRoute('PATCH', $uri, $action, '', $module); 
    
    }

    public function put(string $uri, string $action, string $module = '')    
    { 
        return $this->addRoute('PUT', $uri, $action, '', $module); 
    
    }

    public function delete(string $uri, string $action, string $module = '') 
    { 
        return $this->addRoute('DELETE', $uri, $action, '', $module); 
    }


    /**
     * Registers a route definition.
     * Automatically attaches middleware inherited from any active group.
     */
    protected function addRoute(
        string $method, 
        string $uri, 
        string $action, 
        string $apiVersion = '', 
        $module = ''
    ): DGZ_RouteDefinition
    {
        // Collect middleware from all active groups
        $middleware = [];

        foreach ($this->middlewareStack as $stack) {
            // empty array if no middleware group
            $middleware = array_merge($middleware, $stack);
        }

        // Store the route data on the router $routes property first so it stays in scope for access later
        $this->routes[] = [
            'method'     => $method,
            'uri'        => $uri,
            'action'     => $action,
            'apiVersion' => $apiVersion,
            'module'     => $module,
            'middleware' => $middleware,
            'name'       => null,
        ];

        // Then get the reference to the actual stored route
        $routeIndex = array_key_last($this->routes);
        $routeRef   = &$this->routes[$routeIndex];

        return new DGZ_RouteDefinition($this, $routeRef);
    }

    public function apiGet(string $uri, string $action, string $apiVersion, string $module = '')    
	{ 
		return $this->addRoute('GET', $uri, $action, $apiVersion, $module); 
	}

    public function apiPost(string $uri, string $action, string $apiVersion, string $module = '')   
	{ 
		return $this->addRoute('POST', $uri, $action, $apiVersion, $module); 
	}

    public function apiPatch(string $uri, string $action, string $apiVersion, string $module = '')    
	{ 
		return $this->addRoute('PATCH', $uri, $action, $apiVersion, $module); 
	}

    public function apiPut(string $uri, string $action, string $apiVersion, string $module = '')    
	{ 
		return $this->addRoute('PUT', $uri, $action, $apiVersion, $module); 
	}
    
	public function apiDelete(string $uri, string $action, string $apiVersion, string $module = '') 
	{ 
		return $this->addRoute('DELETE', $uri, $action, $apiVersion, $module); 
	}


    /**
     * // @param mixed $request
     * @return array{controller: string, method: string, params: array|null|null}|null
     */
    protected function matchDefinedRoute()
    {
        $config = container(Config::class);
        $method = $this->request->method();

        // remove the base folder (application) name before matching routes
        $base = trim($config->getFileRootPath(), '/'); 
        $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

        if ($base && strpos($path, $base) === 0) {
            $path = trim(substr($path, strlen($base)), '/');
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            $uri = $route['uri'] === '/' ? '/' : trim($route['uri'], '/');
            $pattern = preg_replace('/\{[a-zA-Z_]+\}/', '([^/]+)', $uri);
            $pattern = "@^" . $pattern . "$@";
            $apiVersion = $route['apiVersion'];
            $module = $route['module'];
            $routeName = $route['name'] ?? null;

            $middleware = $route['middleware']; 

            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                [$controller, $action] = explode('@', $route['action']);
                return [
                    'uri'           => $uri,
                    'controller'    => $controller,
                    'method'        => $action,
                    'params'        => $matches,
                    'apiVersion'    => $apiVersion,
                    'module'        => $module,
                    'name'          => $routeName,
                    'middleware'    => $middleware,
                ];
            }
        } 
        return null;
    }


    /**
     * The URI looks something like this: '/api/users/{id}' or '/api/users/'
     */
    public function hasRoute(string $method, string $uri): bool
    {
        // Normalize method (GET, POST, etc.)
        $method = strtoupper($method);

        // Normalize URI (remove leading/trailing slashes)
        $uri = trim($uri, '/');

        foreach ($this->routes as $route) {
            if (strtoupper($route['method']) !== $method) {
                continue;
            }

            // Prepare regex pattern for route URI
            $routeUri = $route['uri'] === '/' ? '/' : trim($route['uri'], '/');
            $pattern = preg_replace('/\{[a-zA-Z_]+\}/', '([^/]+)', $routeUri);
            $pattern = "@^" . $pattern . "$@";

            if (preg_match($pattern, $uri)) {
                return true;
            }
        }

        return false;
    }



    /**
     * Used to return all user-defined routes in application.
     * For example, it is being used by the RoutesCommand to list all routes
     */
    public function getDefinedRoutes(): array
    {
        $thisClass = self::getInstance();
        return $thisClass->routes;
    }


    public function getRoutes(): array
    {
        return $this->getDefinedRoutes();
    } 


    /**
     * setRoutes() is used to set defined routes on the fly, 
     * e.g. from cache at boot time
     * @param array $routes
     * @return void
     */
    public function setRoutes(array $routes): void
    {
        $this->routes = $routes;

        $this->namedRoutes = [];

        foreach ($this->routes as &$route) {
            if (!empty($route['name'])) {
                $this->namedRoutes[$route['name']] = &$route;
            }
        }
    }


    public static function cacheRoutes(): void
    {
         // We will assume that a route cache exists if the cache file exists.  
         $routeCacheExists = false;
         $cacheDir = DGZ_BASE_PATH . '/storage/cache/';
         $routeCacheFile = DGZ_BASE_PATH . '/storage/cache/routes.php';

        // cache directory exists, so check if the route cache file exists in it
        if (file_exists($routeCacheFile))
        {
            $routeCacheExists = true;
        } else {
            if (!is_dir($cacheDir)) {
                mkdir($cacheDir, 0777, true);
                $routeCacheExists = false;
            }
        }
        
        if ($routeCacheExists === false)
        {
            // we already created the file above, so insert the cache contents
            $thisClass = self::getInstance();
            $routes = $thisClass->routes;
            file_put_contents(
                $routeCacheFile,
                "<?php\n\nreturn " . var_export($routes, true) . ";\n"
            );

            // Update the $namedRoutes property
            foreach ($routes as &$route) {
                if (!empty($route['name'])) {
                    $thisClass->namedRoutes[$route['name']] = &$route;
                }
            }
        } 
    }



    protected function getDefinedRouteControllerPath($routeData)
    {
        $config = new Config();
        $controller = $routeData['controller'];
        //$method = $routeData['method'];
        $controllerInput = self::controllerToRouteName($controller);

        $apiVersion = $routeData['apiVersion']; 
        $module = $routeData['module'];

        // establish controller path for these user-defined routes
        // We need to check for the following 3 routes: 1) API routes | 2) Module routes | 3) Regular controller routes 


        //------------------------------------------------------------------------------------
        // Work out where to get controller files.
        /* There differnt types of routes to handle:
                -1) regular routes (non-module)
                -2) regular API routes (non-module)
                -3) regular modules 
                -4) API modules
        */
        // Only the presence of a module name makes a route a module route
        // Only the presence of a module name & version makes a route an API module route
        // So when defining APIs in defined routes, the version number MUST be given.

        // -1) if version not given, & no module name given, this is not a module & not an API. ------ GOOD (regular route)
        if ($apiVersion == '' && $module == '')
        {
            // regular, non-module & non-API controllers path
            // TODO: The line above was original line, restore if issues arise (01/31/2026)
            $controllerRootPath = '/src/controllers/'. ucfirst($controllerInput).'Controller.php';
            $controllerPath = base_path($controllerRootPath);
            
            if (file_exists($controllerPath))
            {
                /*throw new Exception(
                'in DGZ_Router getDefinedRouteControllerPath() Controller EXISTS: ' .
                json_encode($controllerPath)
            );*/
                $controller = 'Dorguzen\Controllers\\'. ucfirst($controllerInput).'Controller';
            }
            else 
            {
                http_response_code(400);
                throw new DGZ_Exception(
                    'Missing Controller file',
                    DGZ_Exception::FILE_NOT_FOUND,
                    'Requested controller file: ' . $controllerPath
                    . ' not found. Make sure it exists.'
                    );
            }
        }

        // -2) if version given & no module name given, this is a non-module API.               ------ GOOD (regular API route)
        else if ($apiVersion != '' && $module == '')
        {
            // TODO: The line above was original line, restore if issues arise (01/31/2026)
            $apiRootPath = '/src/api/' . ucfirst($apiVersion).'/Controllers/'.ucfirst($controllerInput).'Controller.php';
            $apiPath = base_path($apiRootPath);

            if (isset($apiPath) && file_exists($apiPath))
            {
                $controller = 'Dorguzen\\Api\\'. ucfirst($apiVersion).'\\Controllers\\'.ucfirst($controllerInput).'Controller';
            } 
            else 
            {
                http_response_code(400);
                throw new DGZ_Exception(
                    'Missing API Controller file',
                    DGZ_Exception::FILE_NOT_FOUND,
                    'Requested API controller file: ' . $apiPath
                    . ' not found. Make sure it exists.'
                    );
            }
        }

        // -3) if no version given & module name given, this is a non-API module.               ------ GOOD (regular module)
        else if ($apiVersion == '' && $module != '')
        {
            // Module controllers path
            // TODO: The line above was original line, restore if issues arise (01/31/2026)
            $moduleRootPath = '/modules/' . ucfirst($module).'/Controllers/'.ucfirst($controllerInput).'Controller.php';
            $modulePath = base_path($moduleRootPath);
            
            // module controllers
            if (file_exists($modulePath))
            {
                $controller = 'Dorguzen\\Modules\\'. ucfirst($module).'\\Controllers\\'.ucfirst($controllerInput).'Controller';
            }
            else  
            {
                DGZ_Logger::error('Missing Module Controller file', [
                    'path' => $modulePath,
                ]);

                http_response_code(400);
                throw new DGZ_Exception(
                    'Missing Module Controller file',
                    DGZ_Exception::FILE_NOT_FOUND,
                    'Requested Module controller file: ' . $modulePath
                    . ' not found. Make sure it exists.'
                    );
            }
        }

        
        return $controller;
    }



    /** 
     * runGlobalMiddleware() runs the global middlewares
     * It complies with PSR-15 Standards.
     * @param mixed $controller
     * @param mixed $controllerShortName the alias name of the controller without the 'Controller' part
     * @param mixed $method the method in the target controller to handle the request
     */
    // TODO: We should probably move this from the router to the Kernel
    public function runGlobalMiddleware($controller, $controllerShortName, $method)
    {
        // NOTE: The commented out code below was moved to loadGlobalMiddleware()
        // below so we can take advantage of middleware caching.

        /*$middlewareDir = DGZ_BASE_PATH . '/middleware/globalMiddleware';
        $middlewareFiles = glob($middlewareDir . '/*Middleware.php');
        $middlewares = [];

        // Load classes from files and instantiate
        foreach ($middlewareFiles as $file) {
            $contents = file_get_contents($file);
            if (preg_match('/^namespace\s+([^;]+);/m', $contents, $matches)) {
                $namespace = trim($matches[1]);
                $className = $namespace . '\\' . basename($file, '.php');
            } else {
                $className = basename($file, '.php');
            }

            if (!class_exists($className)) {
                require_once $file;
            }

            // instantiate safely (assume parameterless constructor for legacy middleware)
            $middlewares[] = new $className();
        }*/

        // Load global middleware (from cache if available, filesystem otherwise)
        $middlewares = $this->getGlobalMiddlewareCache();

        // Sort by optional property 'priority' (default 10)
        /*usort($middlewares, function ($a, $b) {
            $aPriority = property_exists($a, 'priority') ? $a->priority : 10;
            $bPriority = property_exists($b, 'priority') ? $b->priority : 10;
            return $aPriority <=> $bPriority;
        });*/ 

        // Sort middleware descriptors by 'priority' (default 10-lower runs first)
        usort($middlewares, function ($a, $b) {
            $aPriority = $a['priority'] ?? 10;
            $bPriority = $b['priority'] ?? 10;

            return $aPriority <=> $bPriority;
        });

        // Build a pipeline of callables. Each callable accepts a PsrRequestAdapter and returns a value.
        // The final callable will execute legacy DGZ middlewares that still expect handle() to be called,
        // and will ultimately return true to allow routing to proceed.
        $pipeline = [];

        // Capture application DGZ_Request for adapters
        $dgzRequest = container(DGZ_Request::class);
        $psrRequestFactory = function() use ($dgzRequest) {
            return new PsrRequestAdapter($dgzRequest);
        };

        // Build from last to first so each callable can call the next
        $last = function ($psrRequest) use ($controller, $controllerShortName, $method) {
            // At the end of the PSR pipeline — we now must run remaining legacy DGZ_MiddlewareInterface middlewares
            // (but in practice we've integrated both styles together below).
            // Returning true means "proceed with dispatch".
            return true;
        };

        // Iterate in reverse to wrap callables
        $next = $last;
        // We'll create an array of closures representing middleware execution order
        for ($i = count($middlewares) - 1; $i >= 0; $i--) {
            $mw = $middlewares[$i]['instance'];

            // If class implements legacy DGZ middleware interface, wrap it with a callable that
            // matches the PSR pipeline signature (fn(PsrRequestAdapter): mixed) so both styles can be chained.
            if ($mw instanceof DGZ_MiddlewareInterface) {
                $callable = function ($psrRequest) use ($mw, $controller, $controllerShortName, $method, $next) {
                    // Legacy middleware uses handle(controller, controllerShortName, method)
                    // It should return true to proceed or throw/redirect to abort.
                    $mw->boot(); // preserve existing boot() behaviour
                    $result = $mw->handle($controller, $controllerShortName, $method);
                    if ($result === true) {
                        // continue pipeline
                        return $next($psrRequest);
                    }
                    // If a middleware returned something else (e.g., forced redirect), treat as stop.
                    return $result;
                };
            }
            // Else if middleware has a PSR-style 'process' method, treat as PSR-15
            elseif (method_exists($mw, 'process')) {
                $callable = function ($psrRequest) use ($mw, $controller, $controllerShortName, $method, $next) {
                    // Ensure boot() is called if provided
                    if (method_exists($mw, 'boot')) {
                        $mw->boot();
                    }

                    // Build a SimpleRequestHandler that invokes the next pipeline element
                    $handler = new SimpleRequestHandler(function ($req) use ($next) {
                        return $next($req);
                    });

                    // PSR middleware expects (ServerRequestInterface $req, RequestHandlerInterface $handler)
                    // Our PsrRequestAdapter is passed so middleware can read method/body/headers.
                    return $mw->process($psrRequest, $handler);
                };
            }
            // else unknown shape - try safe fallback: if handle() exists with three args, use that
            elseif (method_exists($mw, 'handle')) {
                $callable = function ($psrRequest) use ($mw, $controller, $controllerShortName, $method, $next) {
                    if (method_exists($mw, 'boot')) {
                        $mw->boot();
                    }
                    $res = $mw->handle($controller, $controllerShortName, $method);
                    if ($res === true) {
                        return $next($psrRequest);
                    }
                    return $res;
                };
            } else {
                // Unknown middleware shape: skip it (but you might want to log)
                $callable = function ($psrRequest) use ($next) {
                    return $next($psrRequest);
                };
            }

            // wrap the current callable into the $next variable
            $next = (function ($callable, $next) {
                return function ($psrRequest) use ($callable, $next) {
                    // Each middleware callable may return:
                    //  - true => continue
                    //  - anything else => short-circuit
                    $result = $callable($psrRequest);
                    // If middleware explicitly returns a boolean true, continue the pipeline (already done inside)
                    // Otherwise, if result is null/true continue; else short-circuit by returning result.
                    return $result;
                };
            })($callable, $next);
        }

        // Kick the pipeline using a fresh PsrRequestAdapter
        $psrReq = $psrRequestFactory();
        $finalResult = $next($psrReq);

        // If any middleware returned strictly false or a non-true response we will treat as abort.
        // Otherwise, proceed (i.e. return true so router continues).
        return ($finalResult === null || $finalResult === true) ? true : $finalResult;
    }

    //---------------------------- use caching with middleware handling ----------------------
    protected function getGlobalMiddlewareCache(): array
    {
        $cacheFile = DGZ_BASE_PATH . '/storage/cache/middleware.php';

        // ✅ Prefer cache if it exists and is readable
        if (file_exists($cacheFile)) {
            // require immediately gives usable middleware instances
            // No need for reflection, or autoloading, or filesystem scan
            $cached = require $cacheFile;

            if (isset($cached['global']) && is_array($cached['global'])) {
                return $this->hydrateGlobalMiddlewareFromCache($cached['global']);
            }
        }

        // 🔁 Fallback to legacy filesystem scan (no cache)
        return $this->scanGlobalMiddlewareFromFilesystem();
        
    }



    /**
     * Convert cached middleware metadata into live middleware objects.
     * This is because cached middleware data comes as metadata,
     * but runGlobalMiddleware() expects already instantiated objects. 
     * This keeps caching separate from execution.
     */
    protected function hydrateGlobalMiddlewareFromCache(array $cached): array
    {
        $middlewares = [];

        foreach ($cached as $entry) {
            // 1️⃣ Ensure the file is loaded (cache stores absolute path)
            if (!class_exists($entry['class'])) {
                require_once $entry['file'];
            }

            // 2️⃣ Instantiate middleware (legacy-compatible: no constructor args)
            $instance = new $entry['class']();

            // 3️⃣ Ensure to preserve priority (used later in sorting)
            $middlewares[] = [
                'instance' => $instance,
                'priority' => $entry['priority'] ?? 10,
            ];
        }

        return $middlewares;
    }



    /**
     * Discover and instantiate global middleware from the filesystem.
     *
     * ⚠️ IMPORTANT:
     * This method is a direct extraction of the original logic from runGlobalMiddleware().
     * It performs NO caching and introduces NO new behaviour.
     *
     * Why this exists:
     * - To make middleware discovery replaceable (filesystem vs cache)
     * - To keep runGlobalMiddleware() readable and stable
     */
    protected function scanGlobalMiddlewareFromFilesystem(): array
    {
        $middlewareDir = DGZ_BASE_PATH . '/middleware/globalMiddleware';
        $middlewareFiles = glob($middlewareDir . '/*Middleware.php');

        $middlewares = [];

        // Load classes from files and instantiate
        foreach ($middlewareFiles as $file) {
            // Read file contents to detect namespace
            $contents = file_get_contents($file);

            if (preg_match('/^namespace\s+([^;]+);/m', $contents, $matches)) {
                $namespace = trim($matches[1]);
                $className = $namespace . '\\' . basename($file, '.php');
            } else {
                // No namespace declared — fallback to class name only
                $className = basename($file, '.php');
            }

            // Load class definition if not already loaded
            if (!class_exists($className)) {
                require_once $file;
            }

            // Instantiate middleware (legacy assumption: no constructor args)
            $instance = new $className();

            /**
             * 🔑 IMPORTANT:
             * We normalize filesystem-loaded middleware into the SAME
             * descriptor format used by cached middleware.
             *
             * This guarantees the router never needs to care
             * where the middleware came from.
             */
            $middlewares[] = [
                'instance' => $instance,
                'priority' => property_exists($instance, 'priority')
                    ? $instance->priority
                    : 10,
            ];
                
        }

        return $middlewares;
    }
    //--------------------------- end of using caching with middleware handling ----------------




    /**
     * Returns to you the current route which consists of the current controller and the active
     * method in string format e.g. 'auth/login'
     *
     * @return array containing the controller and the method
     */
    public function getCurrentRoute()
    {
        list($controller, $method, $controllerInput, $straightUrlId) = self::getControllerAndMethod(true);
        return [$controller, $method, $controllerInput, $straightUrlId];
    }
    //------------------------------ END DEFINED ROUTES ----------------------------------


    /**
     * Returns an array containing the currently active controller and method
     *
     * @example list($controller, $method) = DGZ_Router::getControllerAndMethod();
     *
     * @param bool $stringFormat pass this as true if you want the current controller name returned as a string
     * 		without checking if the controller object actually exists in the system
     * @return array
     * @throws \Dorguzen\Core\DGZ_Exception
     */
    public static function getControllerAndMethod($stringFormat = false) 
    {
        //---------------------------------------------------------
        // Check if routes are cached. If not cache them & proceed.
         self::cacheRoutes();
        //---------------------------------------------------------
        $router = self::getInstance();
        // First, we check for user-defined routes
        if ($match = $router->matchDefinedRoute()) 
        {
            $controller = $match['controller'];
            $method = $match['method'];
            $controllerInput = self::controllerToRouteName($controller);
            $params = $match['params'];
            $apiVersion = $match['apiVersion'];
            $module = $match['module'];
            $middleware = $match['middleware']; 
            $straightUrlId = !empty($params) ? $params[0] : null; 
            $match['controllerShortName'] = $controllerInput;

            //----------------------------------------------------------------------
            // We must run route middleware here if this route's in a middleware group.
            // Wrapping the call in this 'if' block short-circuits the task-no expensive
            // checking for middleware if there's none defined for the current route.
            //----------------------------------------------------------------------
            if (!empty($middleware))
            {
                $httpKernel = new HttpKernel();
                $middlewareClasses = $httpKernel->runRouteMiddleware($middleware); 
                $httpKernel->executeRouteMiddlewarePipeline($middlewareClasses, $match);
            }
            //----------------------------------------------------------------------

            $controller = $router->getDefinedRouteControllerPath($match);
            if ($stringFormat == true)
            {
                $controllerNameString = ucfirst($controllerInput);
                return [$controllerNameString, $method, $controllerInput, $straightUrlId];
            }
            else
            {
                return [$controller, $method, $controllerInput, $straightUrlId];
            }
        }
        else 
        {
            // Now we handle auto-discovery routes
            $urlString0 = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);

            $urlString = explode('/', $urlString0);

            //if an id value is given in the URL eg 123 in www.hostname/controller/method/123
            $straightUrlId = null;

            //check if we are on local or live environment
            $rootPath = false;
            $config = container(Config::class);
            $isModule = false;
            $modules = SELF::getModules();
            $moduleOrControllerBaseName = '';
            $moduleString = '';

            $method = []; 
            $method2 = []; 

            // for module second method
            $method3 = []; 
            $apiVersion = $config->getConfig()['api_module_default_version'] ?? '';

            //if we are in the local environment (but NOT the built-in server, which has no app-name prefix in the URL)
            if ($config->getConfig()['live'] == 'false' && php_sapi_name() !== 'cli-server') {
                // http://localhost:port/yourApp/text
                if (!empty($urlString[2])) { 
                    // For their convenience; if they only enter 'index', 
                    // or 'index.php', we should show them the home page too
                    if ($urlString[2] == 'index' || $urlString[2] == 'index.php') {
                        //send them to the HomeController
                        $get_input = 'Home';
                    }
                    else {
                        $checkApiVersion = SELF::correctModuleOrControllerName($urlString[2]);
                        $get_input = $checkApiVersion[0];
                        $apiVersion = $checkApiVersion[1] != '' ? $checkApiVersion[1] : $apiVersion;
                        
                        //check if it has a '?' character & filter on that
                        if (strrpos($get_input, '?'))
                        {
                            $cut = explode('?', $get_input);
                            $get_input = $cut[0];
                        } 
                        
                        if (isset($modules[$get_input]))
                        {
                            $isModule = true;
                        }
                        $moduleOrControllerBaseName = $get_input; 
                    }
                }
                else {
                    //If they just visit the root of the app, show them the home page
                    $get_input = 'Home';
                    $rootPath = true;
                }


                // there may not be a method specified (3rd slash level) eg when a user visits the home page, 
                // so check if there's one
                // Make sure its not NULL-which is the case if nothing follows the trailing slash
                // http://localhost:port/yourApp/
                if (!empty($urlString[3])) { 
                    // TODO: COPY THIS SECTION OVER TO DOCS, TO EXPLAIN HOW DGZ PROCESSES ROUTE VIA AUTO-DISCOVERY 
                    /*//---------------------------------- START OF DOCS NOTES --------------------------------
                                
                    We need to handle modules here. So, because $urlString[3] exists, it means $urlString[2]
                    exists, so we must check 
                        if $urlString[2] is a module. If so, we must 
                                also check 
                            if this $urlString[3] is one of the controllers in that module. 
                            If both checks return true, we should set the target controller to $urlString[3] in
                                that module. Then we must also check 
                                if $urlString[4] exists and set it as the method of the 
                                    controller in that module, 
                                else if $urlString[4] doesn't exist, the default method will be called on that 
                                    module controller, so we dont need to do anything.
                            else if $urlString[2] is a module but $urlString[3] is not its controller, we simply
                                use $urlString[3] as method2 (2nd method) in that module's main controller class
                        if $urlString[2] is not a module, we simply use $urlString[3] as a method 
                            of the given controller (in $urlString[2]).

                    We should do all this in an if-else statements inside this block

                    If this works, create it on LIVE too.
                    ApiController has these controllers: ['AllController', 'AdController'];
                    Test calling any of its controllers like so: AllController->getAllAds();
                    //------------------------------- END OF DOCS NOTES -----------------------------------------*/


                    $requestParts = SELF::correctModuleOrControllerName($urlString[2]);
                    $moduleString = $requestParts[0];
                    if ($requestParts[1] != "")
                    {
                        $apiVersion = $requestParts[1];
                    }

                    // Check if it's a module
                    if (isset($modules[$moduleString]))
                    {
                        $moduleControllerName = ucfirst($moduleString).'Controller';
                        $moduleOrControllerBaseName = strtolower($moduleString);
                        $isModule = true;

                        // We need to know all controllers in the module
                        // If it is the API module, set its path differently
                        if ($moduleString == 'api')
                        {
                            $moduleControllerPathStringBase = '\Dorguzen\\Modules\\%s\\%s\Controllers\\%s';
                            $moduleControllerPathString = sprintf(
                                $moduleControllerPathStringBase,
                                ucfirst($moduleString),
                                $apiVersion,
                                $moduleControllerName
                            );
                            
                            $moduleControllerClass = new $moduleControllerPathString();
                        }
                        else
                        {
                            $moduleControllerPathStringBase = '\Dorguzen\\Modules\\%s\Controllers\\%s';
                            $moduleControllerPathString = sprintf(
                                $moduleControllerPathStringBase,
                                ucfirst($moduleString),
                                $moduleControllerName
                            );
                            
                            $moduleControllerClass = new $moduleControllerPathString();
                        }

                        // get the module's controllers
                        $moduleControllerClass->getControllers();
                        $controllersOfThisModule = $moduleControllerClass->getControllers();

                        // next check if $urlString[3] is one of these controllers
                        if ($controllersOfThisModule && in_array(ucfirst($urlString[3]).'Controller', $controllersOfThisModule))
                        {
                            $targetControllerString = ucfirst(trim($urlString[3]));

                            // set the request controller to the module's controller
                            $get_input = $targetControllerString;

                            // Then we must also check if $urlString[4] exists and set it as the method of the
                            // controller in that module.
                            if (isset($urlString[4]) && !empty($urlString[4])) {
                                $filterUrl2 = explode('?', $urlString[4]);
                                $method2 = $filterUrl2[0];
                            }
                        }
                        else
                        {
                            // $urlString[3] is not a sub-controller — treat it as a method on the module's main controller
                            $filterUrl = explode('?', $urlString[3]);
                            $method = $filterUrl[0];
                        }
                    }
                    else
                    {
                        // $urlString[2] is not a module
                        // we therefore simply use $urlString[3] as a method  
                        // of the given controller (in $urlString[2]).
                        //Extract the method name by splitting the rest of the string by the '?' character if any
                        $filterUrl = explode('?', $urlString[3]);
                        $method = $filterUrl[0];
                    }
                }
    
                if (!empty($urlString[4])) { 
                    //Extract the method name by splitting the rest of the string by the '?' character if any
                    $filterUrl4 = explode('?', $urlString[4]);
                    $method2 = $filterUrl4[0];
                }

                if (!empty($urlString[5])) { 
                    //Extract the method name by splitting the rest of the string by the '?' character if any
                    $filterUrl5 = explode('?', $urlString[5]);
                    $method3 = $filterUrl5[0];
                }
            }






            //------------- THE NEXT PART DEALS WITH THE PRODUCTION ENVIRONMENT --------------------//
            // STILL NEED TO TEST ON LIVE FOR MODULE ROUTES








            else
            {
                //http://yourApp/text
                if (!empty($urlString[1])) {
                    if ($urlString[1] == 'index' || $urlString[1] == 'index.php')
                    {
                        //send them to the HomeController
                        $get_input = 'Home';
                    }
                    else
                    {
                        $straightUrlId = null;
                        //check if we are on local or live environment
                        $rootPath = false;
                        $config = container(Config::class);
                        $isModule = false;
                        $modules = SELF::getModules();
                        $moduleOrControllerBaseName = '';
                        $moduleString = '';

                        $method = []; 
                        $method2 = []; 
                        $method3 = []; // for module second method
                        $apiVersion = $config->getConfig()['api_module_default_version'] ?? '';


                        ////$get_input = $urlString[1];
                        $checkApiVersion = SELF::correctModuleOrControllerName($urlString[1]);
                        $get_input = $checkApiVersion[0];
                        $apiVersion = $checkApiVersion[1] != '' ? $checkApiVersion[1] : $apiVersion;
                        //check if it has a '?' character & filter on that
                        if (strrpos($get_input, '?'))
                        {
                            $cut = explode('?', $get_input);
                            $get_input = $cut[0];
                        }
                        if (isset($modules[$urlString[1]]))
                        {
                            $isModule = true;
                        }
                        $moduleOrControllerBaseName = $get_input;
                    }
                }
                else
                {
                    //If they just visit the root of the app, show them the home page
                    $get_input = 'Home';
                    $rootPath = true;
                }

                //there may not be a method specified (2nd slash level) eg when a user visits the home page, so check if there's one
                //http://yourApp/text/text2
                if (!empty($urlString[2])) { // TODO: ADD THIS CHANGE TO MAKE SURE ITS NOT NULL-wh is the case if nothing follows the trailing slash (mon 29/9/25)

                    //- - - - - - - - - - - START- - - - - - - - - - - - - - - - //
                    /*//---------------------------------- TESTING START --------------------------------
                                
                    We need to handle modules here. So, because $urlString[2] exists, it means $urlString[1]
                    exists, so we must check 
                        if $urlString[1] is a module. If so, we must 
                                also check 
                            if this $urlString[2] is one of the controllers in that module. 
                            If both checks return true, we should set the target controller to $urlString[2] in
                                that module. Then we must also check 
                                if $urlString[3] exists and set it as the method of the 
                                    controller in that module, 
                                else if $urlString[3] doesn't exist, the default method will be called on that 
                                    module controller, so we dont need to do anything.
                            else if $urlString[1] is a module but $urlString[2] is not its controller, we simply
                                use $urlString[2] as method2 (2nd method) in that module's main controller class
                        if $urlString[1] is not a module, we simply use $urlString[2] as a method 
                            of the given controller (in $urlString[1]).

                    If this works, create it on LIVE too.
                    ApiController has these controllers: ['AllController', 'AdController'];
                    Test calling any of its controllers like so: AllController->getAllAds();
                    //---------------------------------- TESTING END ----------------------------------*/
                    
                    //------------------------------ IMPLEMENTING THE TEST -----------------------------
                    $requestParts = SELF::correctModuleOrControllerName($urlString[1]);
                    $moduleString = $requestParts[0];
                    if ($requestParts[1] != "")
                    {
                        $apiVersion = $requestParts[1];
                    }



                    // Check if it's a module
                    if (isset($modules[$moduleString]))
                    {
                        $moduleControllerName = ucfirst($moduleString).'Controller';
                        $moduleOrControllerBaseName = strtolower($moduleString);
                        $isModule = true;

                        // We need to know all controllers in the module
                        // If it is the API module, set its path differently
                        if ($moduleString == 'api')
                        {
                            $moduleControllerPathStringBase = '\modules\\%s\\%s\controllers\\%s';
                            $moduleControllerPathString = sprintf(
                                $moduleControllerPathStringBase,
                                $moduleString,
                                $apiVersion,
                                $moduleControllerName
                            );
                            $moduleControllerClass = new $moduleControllerPathString();
                        }
                        else
                        {
                            $moduleControllerPathStringBase = '\modules\\%s\Controllers\\%s';
                            $moduleControllerPathString = sprintf(
                                $moduleControllerPathStringBase,
                                ucfirst($moduleString),
                                $moduleControllerName
                            );
                            
                            $moduleControllerClass = new $moduleControllerPathString();
                        }

                        // get the module's controllers
                        $moduleControllerClass->getControllers();

                        $controllersOfThisModule = $moduleControllerClass->getControllers();

                        // next check if $urlString[2] is one of the sub-controllers in this module
                        if ($controllersOfThisModule && in_array(ucfirst($urlString[2]).'Controller', $controllersOfThisModule))
                        {
                            $targetControllerString = ucfirst(trim($urlString[2]));

                            // set the request controller to the module's sub-controller
                            $get_input = $targetControllerString;

                            // $urlString[3] is the method on that sub-controller
                            if (isset($urlString[3]) && !empty($urlString[3])) {
                                $filterUrl2 = explode('?', $urlString[3]);
                                $method2 = $filterUrl2[0];
                            }
                        }
                        else
                        {
                            // $urlString[2] is not a sub-controller — treat it as a method on the module's main controller
                            $filterUrl = explode('?', $urlString[2]);
                            $method = $filterUrl[0];
                        }
                    }
                    else
                    {
                        // $urlString[2] is not a module
                        // we therefore simply use $urlString[3] as a method  
                        // of the given controller (in $urlString[2]).
                        //Extract the method name by splitting the rest of the string by the '?' character if any
                        $filterUrl = explode('?', $urlString[2]);
                        $method = $filterUrl[0];
                    }

                    //--------------------------- END OF IMPLEMENTING THE TEST -------------------------
                    //- - - - - - - - - - - - END- - - - - - - - - - - - - - - //
                }

                if (!empty($urlString[3]))
                {
                    /*//---------------------------------- TESTING START --------------------------------
                                
                    We need to handle modules here. So, because $urlString[3] exists, it means $urlString[1]
                    and $urlString[2] existed, so we must check 
                        if $urlString[1] is a module. If so, we must 
                                also check 
                            if this $urlString[2] is one of the controllers in that module. 
                                If both checks return true, we should use $urlString[3] as a method in the 
                                    $urlString[2] controller of that module, otherwise, 
                            if $urlString[1] is a module but if $urlString[2] is not its controller, we simply
                                use $urlString[3] as method2 (2nd method) in that module's main controller class
                        if $urlString[1] is not a module, we use $urlString[2] as a method of the given 
                            controller, and $urlString[3] as method2 (2nd method) in the $urlString[1]
                            controller. 
                    We should do all this in an if-else statements inside this block

                    If this works, create it on LIVE too.
                    //---------------------------------- TESTING END ----------------------------------*/

                    //Extract the method name by splitting the rest of the string by the '?' character if any
                    $filterUrl3 = explode('?', $urlString[3]);
                    $method2 = $filterUrl3[0];
                }

                if (!empty($urlString[4])) { 
                    // HTTPS://CAMCOM/API/ALL/5
                    //Extract the method name by splitting the rest of the string by the '?' character if any
                    $filterUrl4 = explode('?', $urlString[4]);
                    $method3 = $filterUrl4[0];
                }

            }



            //does the caller just want this as a string instead of as an object?
            if ($stringFormat == true)
            {
                $controllerNameString = ucfirst($get_input);
            }

            // We need to know if this request is a module, & if so, if it's an API module-both are handled differently
            if ($isModule)
            {
                // is it the API module
                if (strtolower($moduleOrControllerBaseName) == 'api')
                {
                    // This is a special kind of module; APIs, which has versions, & so needs to be routed differently 
                    if ($apiVersion)
                    {
                        // TODO: The line above was original line, restore if issues arise (01/31/2026)
                        $apiRootPath = '/modules/' . strtolower($moduleOrControllerBaseName) .'/'. strtolower($apiVersion).'/controllers/'.ucfirst($get_input).'Controller.php';
                        $apiPath = base_path($apiRootPath);


                        if (isset($apiPath) && file_exists($apiPath))
                        {
                            $controller = 'Dorguzen\\Modules\\'. strtolower($moduleOrControllerBaseName) . '\\' . strtolower($apiVersion).'\\controllers\\'.ucfirst($get_input).'Controller';
                        } 
                        else
                        { 
                            throw new DGZ_Exception(
                                'Missing API Version Directory',
                                DGZ_Exception::FILE_NOT_FOUND,
                                'A request is being made to the API module version '.$apiVersion.' 
                                but no directory and files have been set up for that version yet ' . PHP_EOL
                                . 'Contact the developer (s), if its not you, and make sure this director exists: ' . PHP_EOL
                                . 'modules/api/'.$apiVersion.'/controllers/ApiController.' . PHP_EOL
                            );
                        }
                    }
                    else
                    {
                        throw new DGZ_Exception(
                            'Missing API Versioning',
                            DGZ_Exception::IDENTIFIER_NOT_FOUND,
                            'You are using an API module with no versioning defined' . PHP_EOL
                            . 'Set the versioning number for your API module routes eg V1 or V2 in configs/Config.php.' . PHP_EOL
                            . 'Like so: "api_module_default_version" => "v1" because the version is needed for routing requests to the endpoint.'
                        );
                    }
                }
                else
                {
                    // this is a non-API module
                    // TODO: The line above was original line, restore if issues arise (01/31/2026)
                    $moduleRootPath = '/modules/' . ucfirst(strtolower($moduleOrControllerBaseName)) .'/Controllers/'.ucfirst($get_input).'Controller.php';
                    $modulePath = base_path($moduleRootPath);


                    if (file_exists($modulePath))
                    {
                        $controller = 'Dorguzen\\Modules\\'. ucfirst($moduleOrControllerBaseName).'\\Controllers\\'.ucfirst($get_input).'Controller';
                    }
                }
            }
            else
            {
                // this is a regular (non-module) request
                // DGZ runs all requests through controllers or modules. Therefore here, we load the target controller 
                // or module class
                // TODO: The line above was original line, restore if issues arise (01/31/2026)
                $controllerRootPath = '/src/controllers/'. ucfirst($get_input).'Controller.php';
				$controllerPath = base_path($controllerRootPath);


                
                if (file_exists($controllerPath))
                {
                    $controller = 'Dorguzen\\Controllers\\'. ucfirst($get_input).'Controller';
                } 
            }


            try {
                // If no controller was resolved from the URL, there is no route to dispatch.
                if (!isset($controller))
                {
                    http_response_code(404);
                    throw new DGZ_Exception(
                        'Page not found',
                        DGZ_Exception::CONTROLLER_CLASS_NOT_FOUND,
                        'No controller was found to handle the request for "' . ($get_input ?? 'unknown') . '". '
                        . PHP_EOL . 'Check that the URL is correct. '
                        . PHP_EOL . 'If you are a developer, make sure a controller exists for this route.'
                    );
                }

                $classReflector = new ReflectionClass($controller);

                if (!(get_class($classReflector)))
                {
                    http_response_code(400);
                    throw new DGZ_Exception(
                        'Controller not found',
                        DGZ_Exception::CONTROLLER_CLASS_NOT_FOUND,
                        'No controller found with the name "' . $controller . '". ' . PHP_EOL
                        . 'Check that the name of the page in the address is correct and that the class exists in either your application\'s controllers folder. ' . PHP_EOL
                        . 'Also, this error can happen if you have wrongly namespaced your controller class. Controller classes must live within the global namespace.'
                    );
                }
            }
            catch (Exception $e) {
                // Re-throw to the outer catch which handles layout rendering correctly
                throw $e;
            }

            // Resolve/nstantiate the controller class
            try {
                $object = container($controller); 
            } 
            catch (Exception $e) 
            {
                try {
                    // Fallback to Reflection if the container cannot build it (legacy support)
                    $object = $classReflector->newInstance(); 

                } catch (Exception $e2) {
                    throw new DGZ_Exception(
                        'Controller not found',
                        DGZ_Exception::CONTROLLER_CLASS_NOT_FOUND,
                        'No controller found with the name "' . $controller . '".'
                    );
                }
                
            }
            //--------------------

            // note that if no method is defined, $object->getDefaultAction() below will be run and will exit this execution, esp coz the defaultAction() has
            // no arguments so there's nothing further to resolve. All controllers must therefore have a getDefaultAction() method that takes no arguments.
            // But before we get the default controller, we check if there's a method on the controller that happens to match the spelling of the controller
            // parameter passed in the URL ($get_input) and use that if its found; otherwise, we get the default method. This will prevent us needing to pass
            // URL parameters for methods that have the same spelling as the controller parameter, which will neither look sensible visually, nor be good for
            // search engines. This basically means we would end up having neat URLs that look like: 'http://appName/news' instead of 'http://appName/news/news'.
            if(empty($method)) {
                //we make an exception for the HomeController-if no controller & no method parameter are given in the URL, go straight to its defaultAction() method.
                if (strtoupper($get_input) == 'HOME') {
                    if ($rootPath == true) {
                        $method = $object->getDefaultAction();
                    } else { $method = $get_input; }
                }
                else
                {
                    if (DGZ_Controller::controllerMethodExists($controller, $get_input)) {
                        $method = $get_input;
                    }
                    else {
                        $method = $object->getDefaultAction();
                    }
                }
            }


            //when we got the classReflector on this page above, it was just to see if the controller exists
            // Now we check if $method2 exists and make it the applicable method. However, before we pass the $urlString[3] 
            // for a method; (this is important), we check that its value is not an integer. If the value is an integer, then 
            // we take that to be an ID & inject it as an argument into the method at $urlString[2] (in our example above-the 
            // 'documentation()'). If it is not an integer, then it is another method eg 'databases()' within the same controller 
            // in our example. Now, if $method2 exists & is not an integer, it replaces $method; else if it's an integer, we use 
            // $method & store $method2 to be injected down the line as its argument
            if ($method3) {
                // method3 only serves as a holder for any resource id (int) for method2
                if (filter_var($method3, FILTER_VALIDATE_INT))
                {
                    $straightUrlId = $method3;
                } 
            }

            if ($method2) {
                if (filter_var($method2, FILTER_VALIDATE_INT))
                {
                    $straightUrlId = $method2;
                } else {
                    $method = $method2;
                }
            }

            if ($method) {
                if (filter_var($method, FILTER_VALIDATE_INT))
                {
                    $straightUrlId = $method;
                    $method = 'defaultAction';
                }
            }


            //Handle any hyphens in the URL method param by referring the handling to a matching underscored or camel-cased controller method
            if (preg_match('/-/', $method))
            {
                $methodPieces = explode('-', $method);
                $method_underscored = implode('_', $methodPieces);

                $newMethodPieces = array_map('ucfirst', $methodPieces);
                $method_camelCased = implode($newMethodPieces);

                if (method_exists($object, $method_underscored))
                {
                    //override the method
                    $method = $method_underscored;
                }
                elseif (method_exists($object, $method_camelCased))
                {
                    //override the method
                    $method = $method_camelCased; 
                }
            }

            if ($stringFormat == true)
            {
                return [$controllerNameString, $method, $get_input, $straightUrlId];
            }
            else
            {
                return [$controller, $method, $get_input, $straightUrlId];
            }
        } 
    }


    public static function route(): void
    {
        self::getInstance()->dispatchRequest();
    }


    /**
     * Reads the controller & method passed in, processes them and loads the relevant
     * DGZ_Controller object, & calls the method that matches the requested method name
     *
     * @throws /Exception If not all arguments required by the method are provided.
     */
    public function dispatchRequest()
    {
        /*if ($router === null) {
            echo "ROUTER IS NULL) ooh\n";
            $router = self::getInstance(); // fallback for web
        }*/

        $config = container(Config::class);

        // Static-asset 404 guard: when a referenced asset file is missing from disk,
        // Apache's !-f condition passes the request to PHP. Detect these by file extension
        // and return a plain 404 immediately — no controller lookup, no DB logging.
        $requestPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $realFilePath = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . $requestPath;
        $staticExtensions = ['css', 'js', 'png', 'jpg', 'jpeg', 'gif', 'svg', 'ico',
                             'woff', 'woff2', 'ttf', 'eot', 'map', 'webp', 'pdf',
                             'mp4', 'mp3', 'ogg', 'wav', 'zip', 'gz'];
        $ext = strtolower(pathinfo($requestPath, PATHINFO_EXTENSION));
        if (in_array($ext, $staticExtensions) && !file_exists($realFilePath)) {
            http_response_code(404);
            return;
        }



       try {
            // --------------- FALL-BACK AUTO ROUTE DISCOVERY -------------------
            // Determine the controller and method to load.
            // Primarily based on the URL but will substitute defaults if not set.
            list($controller, $method, $controllerInput, $straightUrlId) = self::getControllerAndMethod();
            $controllerInput = strtolower($controllerInput);

            // check if $straightUrlId is applicable & if so, pass an 'id' param into the $_REQUEST object
            // It's then up to the target method to optionally have an $id argument to capture its value
            if (null !== $straightUrlId) {
                // the old way-for backwards compatability
                $_REQUEST['targetId'] = $straightUrlId;
                $requestObject = container(DGZ_Request::class);
                $requestObject->setTargetId($straightUrlId);
            }

            //--------------------
            // 'new ReflectionClass()' does not instantiate the given class eg $controller
            // it just instantiates the ReflectionClass() with the given class, ready for use
            $classReflector = new ReflectionClass($controller);

            // Ask the container to resolve the controller
            try {
                $object = container($controller);
            } 
            catch (Exception $e) 
            {
                try {
                    // Fallback to Reflection if the container cannot build it (legacy support)
                    $object = $classReflector->newInstance();

                } catch (Exception $e2) {
                    throw new DGZ_Exception(
                        'Controller not found',
                        DGZ_Exception::CONTROLLER_CLASS_NOT_FOUND,
                        'No controller found with the name "' . $controller . '".'
                    );
                }
                
            }
            //--------------------

            //--------------------------- MIDDLEWARE (ADD TO DOC THEN DELETE)---------------------------------//;
            /*  TODO: MAKE MIDDLEWARE CALL MODULAR
                -add user middleware to /middleware/moduleNameMiddleware.php starting with the 
                    DGZ's own middleware calls as seen below. The current MiddleWare.php will 
                    be DGZ's middleware, and the code to process the checks (conditionals below)
                    will be placed in a handler() method of that Middleware class. All user-defined
                    (module) middleware should run in the same fashion.
                -loop thru all these middlewar (mw) & call their boot() 
                -then call their handle() method passing it controllerInput and $method
                    so they can be granular about handling the request to the method level
                -each of the handlers should have code in it that looks like all the if statements below
                -this should suffice seeing that each time any if statements or whatever conditionals the 
                    user placed in there throws an exception, this script will abort, and the exception 
                    will be caught and handled nicely below.  
            */

            /*
            Notes
                -You load every *Middleware.php file in /middleware.
                -You instantiate each middleware.
                -You sort them safely by $priority (default 10 if not set).
                -You run them in proper order.
            */
            $this->runGlobalMiddleware($controller, $controllerInput, $method);
            //--------------------------- END MIDDLEWARE DOCS NOTES ---------------------------------//
            try {
                /*If no method is passed to the URL and the controller has a default method (declared as 'defaultAction()'),
                the getControllerAndMethod() above would have set that default method as that $method.
                This script above will then automatically redirect and run that method on the controller and we will not get
                to this point, esp coz the defaultAction() method takes no arguments & there's nothing further to resolve.
                Therefore, we will only get in here if there is a method parameter in the URL, in which case we check for it
                in this try block.

                grab the method on the controller that was called in the URL */
                $methodReflector = $classReflector->getMethod($method);
            }
            catch (ReflectionException $e) {
                http_response_code(404);
                throw new DGZ_Exception('No method to handle this request', DGZ_Exception::MISSING_HANDLER_FOR_ACTION, 'There is no method in your Controller class to handle "' . $method . '". ' . PHP_EOL . 'Check that the method name passed through is correct, and if required create a public function called "' . $method . '" in your ' . $controller . ' class.');
            }

            // Grab any query string parameters passed in the URL.
            // These will be the arguments to pass to the method
            $methodParameters = $methodReflector->getParameters();
            $inputParameters = [];

            // Loop over the parameters:
            foreach ($methodParameters as $parameter) {
                if ($parameter->getType() instanceof ReflectionNamedType) {
                    $type = $parameter->getType();
                    if (!$type->isBuiltin()) {
                        $className = $type->getName();
                        $parameterObject = new ReflectionClass($className);
                    }
                }    
                else {
                    $parameterName = $parameter->getName();
                    if (!empty($_REQUEST[$parameterName])) {
                        $inputParameters[] = $_REQUEST[$parameterName];
                    }
                    elseif ($straightUrlId != null) { 
                        $inputParameters[] = $straightUrlId;
                    }
                    //if the expected argument is not provided, see if that controller method argument has a default value
                    elseif ($parameter->isDefaultValueAvailable()) {
                        $inputParameters[] = $parameter->getDefaultValue();
                    }
                    else {
                        throw new DGZ_Exception('Required parameter "' . $parameterName . '" not set', DGZ_Exception::MISSING_PARAMETERS, 'If you have just submitted a form, please make sure all fields are set, otherwise this may be a programming error.');
                    }
                }
            }

            $object->display($method, $inputParameters);
        }
        catch (Exception $e) {
            /**
             * WARNING!
             *
             * You must not allow any exception to be thrown out of this section,
             * otherwise the framework just goes round in circles, and the page eventually times out.
             *
             */
            $exceptionController = container(ExceptionController::class);
            //-------------START TESTING ----------------------------------------
            // is it a ValidationException
            if ($e instanceof ValidationException) {
                $errorMsg = "";
            
                foreach ($e->getValidationErrorMessages() as $key => $valError)
                {
                    if (is_array($valError))
                    {
                        foreach ($valError as $val)
                        {
                            $errorMsg .= $val.'<br>';
                        }
                    }
                    else
                    {
                        $errorMsg .= $valError.'<br>';
                    }
                }
                $exceptionController->addErrors($errorMsg, "There were errors");

                // you may optionally use existing controller helpers for notices
                // Redirect back to referer or a provided redirectTo
                $redirectTo = $e->redirectTo ?? $_SERVER['HTTP_REFERER'] ?? '/';
                $exceptionController->redirect($redirectTo);
                exit;
            }
            else
            //-------------------END TESTING----------------------------------


            // Is this a DGZ_Exception?
            if ($e instanceof DGZ_Exception) {
                $view = DGZ_View::getView('DGZExceptionView', $exceptionController, 'html');
            }
            else {
                // If it's a normal exception then just use the default view
                $view = DGZ_View::getView('ExceptionView', $exceptionController, 'html');
            }

            $app = new DGZ_Application();
            // If this is a HTML format response then we can use a default layout/view to show the error somewhat nicely.
            //WE DONT HAVE TO WORRY ABOUT THIS FOR NOW AS FORMAT WILL NOT BE USED-HOWEVER WE MAY COME BACK AT A LATER DATE AND DECIDE IF WE NEED FORMATS (FOR DATA)
            if (!isset($_REQUEST['format']) || $_REQUEST['format'] == 'html') 
            {
                
                $layout = DGZ_Layout::getLayout($app->getUseFullLayoutSetting(), $app->getAppName(), $app->getDefaultLayoutDirectory(), $app->getDefaultLayout());
                $layout->setPageTitle('Error: ');

                // Use the view determined above to generate the HTML for the error
                ob_start();


                //----------------------------------------------------------
                // Log full error rather, and only display minimal info to user
                //----------------------------------------------------------
                $hint = ($e instanceof DGZ_Exception) ? $e->getHint() : 'No hint available.';

                DGZ_Logger::error($e->getMessage(), [
                    'hint'  => $hint,
                    'file'  => $e->getFile(),
                    'line'  => $e->getLine(),
                    'trace' => $e->getTraceAsString(),
                ]);
                //----------------------------------------------------------

                // only show a hint to the user, for security reasons 
                $view->show($e->getMessage());


                $contentHtml = ob_get_clean();


                $layout->setContentHtml($contentHtml);

                // And display it.
                $layout->display();
            }
            // json return format that can be useful for cases like API calls
            elseif (isset($_REQUEST['format']) && $_REQUEST['format'] == 'json') 
            {
                $layout = DGZ_Layout::getLayout($app->getUseFullLayoutSetting(), $app->getAppName(), $app->getDefaultLayoutDirectory(),'JsonLayout');

                ob_start();
                $view->show($e);
                $content = ob_get_clean();

                $layout->setContentHtml($content);
                $layout->display();

            }
            else {
                // fall back for plain text format
                header('Content-Type: text/plain');

                $msg = $e->getMessage();
                $file = $e->getFile();
                $line = $e->getLine();
                $stack = $e->getTraceAsString();
                if ($e instanceof DGZ_Exception) {
                    $hint = $e->getHint();
                }
                else {
                    $hint = 'No further information available';
                }

                $errorText = <<<TEXT
Error: {$msg}
Hint:  {$hint}
File:  {$file}
Line:  {$line}

Stack
---------------------------------------------
{$stack}
TEXT;

                die($errorText);
            }
        }
    }


    /**
     * dispatchForTesting() is a tests-only request dispatcher
     * @return void
     */ // @return KernelResponse
    public function dispatchForTesting(): void
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri    = $_SERVER['REQUEST_URI'];

        $match = $this->resolveDefinedRouteForTesting($httpMethod, $uri);

        $controllerClass = $match['controller'];
        $method          = $match['method'];
        $params          = $match['params'];

        $controller = container($controllerClass);

        call_user_func_array(
            [$controller, $method],
            $params
        );
    }



    /**
     * resolveDefinedRouteForTesting() does routing exclusively for tests
     * @param string $httpMethod
     * @param string $uri
     * @throws RuntimeException
     * @return array{controller: mixed, method: mixed, middleware: mixed, params: mixed}
     */
    public function resolveDefinedRouteForTesting(string $httpMethod, string $uri): array 
    {
        foreach ($this->routes as $route) {
            if (
                $route['method'] === strtoupper($httpMethod) &&
                $route['uri'] === $uri
            ) {

            $routeData = $this->buildRouteForTesting($route);
            /*throw new Exception(
                'in DGZ_Router resolveDefinedRouteForTesting() CONTENT OF $routeData is: ' .
                json_encode($routeData)
            );*/

            /* {
                    "uri":"ping",
                    "controller":"TestController",
                    "method":"ping",
                    "params":[],
                    "apiVersion":"",
                    "module":"","name":null,
                    "middleware":[]
                }
            */

            /*
                return [
                'uri'           => $uri,
                'controller'    => $controller,
                'method'        => $action,
                'params'        => $matches,
                'apiVersion'    => $apiVersion,
                'module'        => $module,
                'name'          => $routeName,
                'middleware'    => $middleware,
            ];
            */

                return [
                    'controller' => $this->getDefinedRouteControllerPath($routeData),
                    'method'     => $routeData['method'],
                    'params'     => $routeData['params'] ?? [],
                    'middleware' => $routeData['middleware'] ?? [],
                ];
            }
        }

        throw new RuntimeException("No route matched [$httpMethod $uri]");
    }

    public function buildRouteForTesting(array $route)
    {
        // remove the base folder (application) name before matching routes
        $config = container(Config::class);

        // remove the base folder (application) name before matching routes
        $base = trim($config->getFileRootPath(), '/'); 
        $path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
        $uri = $route['uri'] === '/' ? '/' : trim($route['uri'], '/');
        $pattern = preg_replace('/\{[a-zA-Z_]+\}/', '([^/]+)', $uri);
        $pattern = "@^" . $pattern . "$@";
        $apiVersion = $route['apiVersion'];
        $module = $route['module'];
        $routeName = $route['name'] ?? null;

        $middleware = $route['middleware']; 

        if (preg_match($pattern, $path, $matches)) {
            array_shift($matches);
            [$controller, $action] = explode('@', $route['action']);
            return [
                'uri'           => $uri,
                'controller'    => $controller,
                'method'        => $action,
                'params'        => $matches,
                'apiVersion'    => $apiVersion,
                'module'        => $module,
                'name'          => $routeName,
                'middleware'    => $middleware,
            ];
        }

        return null;
    }


    /**
     * controllerToRouteName() returns the alias name of a controller withou the 'Controller' part
     * @param string $controllerName the full name of the controller to handle a request eg 'HomeController' 
     * @return string the alias name derived from the full controller name, which is the lowercase name 
     *      of the controller without the 'Controller' part. It is also referred to in other parts of the 
     *      application as controllerShortName.
     */
    public static function controllerToRouteName(string $controllerName): string
    {
        // If the name ends with 'Controller', remove it
        if (str_ends_with($controllerName, 'Controller')) {
            $controllerName = substr($controllerName, 0, -strlen('Controller'));
        }

        // Convert to lowercase
        return strtolower($controllerName);
    }

    public static function getModules()
    {
        $config = container(Config::class);

        $activeModules = array_filter($config->getConfig()['modules'], function($value) {
            return $value == 'on';
        });
        return $activeModules;
    }

    /**
     * For API requests, the controller segment of the URL (/api/), could either be just '/api/' 
     * or it could contain a hyphen followed by the version number eg /api-v2/
     * In that case therefore, we need to split the request string by a hyphen in case there is one,  
     * and extract the API version. We then return an array with the string 'api', and the version digit.  
     * If no hyphen is matched, we still return the array with the request string ('/api'/), and a blank 
     * string for the version digit.
     * @param string $string
     * @return array
     */
    public static function correctModuleOrControllerName($string)
    {
        if (preg_match('/-v\d+/i', $string, $matches)) {
            [$newString, $apiVersion] = explode('-', $string, 2);
            $apiVersion = strtolower($apiVersion);
            return [$newString, $apiVersion];
        }
        else {
            return [trim($string), ''];
        }
    }
}

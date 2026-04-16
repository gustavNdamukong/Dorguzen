<?php
namespace Dorguzen\Core\Kernel;


use Dorguzen\Core\DGZ_Exception;
use Dorguzen\Core\Psr\PsrRequestAdapter;
use Dorguzen\Core\Psr\SimpleRequestHandler;

use Dorguzen\Core\DGZ_Request;
use Dorguzen\Core\DGZ_Response;

use Dorguzen\Core\DGZ_Router;
use Dorguzen\Testing\Http\KernelResponse;

class HttpKernel extends BaseKernel
{
    public function __construct()
    {
        parent::__construct(base_path());
    }

    protected DGZ_Router $router;

    protected function bootstrap(): void
    {
        parent::boot();
    }


    /**
     * Dispatch an HTTP request in a testing-safe environment.
     * This MUST NEVER be used in production.
     * Usage example in tests
     * 
     *       $kernel = new HttpKernel();
     *
     *       $response = new TestResponse(
     *           $kernel->dispatchForTesting()
     *       );
     *
     *       $response
     *           ->assertStatus(200)
     *           ->assertSee('Welcome')
     *           ->assertHeader('Content-Type', 'application/json');
     */
    public function dispatchForTesting(): KernelResponse
    {
        $request = request();

        //--------------------------------------------------------------------------------------
        // Bootstrap the router and request objects explicitly for testing
        // Golden rule is; tests must explicitly bootstrap anything they depend on.
        //--------------------------------------------------------------------------------------

        // ✅ Create router and register it as the singleton
        $router = DGZ_Router::bootstrap($request);

        // 🔑 LOAD ROUTES EXPLICITLY
        require base_path('/routes/web.php');
                
        // Reset globals that affect HTTP state
        http_response_code(200);
        header_remove();

        ob_start();

        try {
            $router->dispatchForTesting();
        } catch (\Throwable $e) {
            ob_end_clean();
            throw $e;
        }

        $body = ob_get_clean();

        return new KernelResponse(
            http_response_code(),
            headers_list(),
            $body
        );
    }


    /**
     * Resolve route middleware aliases into instantiated middleware objects.
     * Get them from cache if one exists, or load them from the file system.
     *
     * @param array $aliases  List of middleware aliases (e.g. ['auth', 'csrf'])
     * @return array          Array of middleware instances
     */
    public function runRouteMiddleware(array $aliases): array
    {
        $resolved = [];
        $cachedRouteMiddleware = null;

        // Location of middleware cache
        $cacheFile = DGZ_BASE_PATH . '/storage/cache/middleware.php';

        /**
         * 1️⃣ Attempt to load route middleware metadata from cache
         */
        if (file_exists($cacheFile)) {
            $cached = require $cacheFile;

            if (isset($cached['route']) && is_array($cached['route'])) {
                $cachedRouteMiddleware = $cached['route'];
            }
        }

        /**
         * 2️⃣ Resolve each alias individually
         *     (auth, csrf, etc.)
         */
        foreach ($aliases as $alias) {
            $alias = strtolower($alias);

            /**
             * 🔹 Case A: Cache exists and contains this alias
             */
            if (
                $cachedRouteMiddleware !== null &&
                isset($cachedRouteMiddleware[$alias])
            ) {
                $meta = $cachedRouteMiddleware[$alias];

                // Ensure class definition is loaded
                if (!class_exists($meta['class'])) {
                    require_once $meta['file'];
                }

                // Instantiate middleware (no constructor args assumed)
                $resolved[] = new $meta['class']();
                continue;
            }

            /**
             * 🔹 Case B: No cache OR alias not found in cache
             *     → fallback to filesystem resolution
             *
             * This preserves backward compatibility.
             */
            $resolved[] = $this->resolveRouteMiddlewareFromFilesystem($alias);
        }

        return $resolved;
    }


    /**
     * Resolve a single route middleware from the filesystem.
     * This will be called in a loop from runRouteMiddleware()
     *
     * @param string $alias
     * @return object
     * @throws DGZ_Exception
     */
    protected function resolveRouteMiddlewareFromFilesystem(string $alias)
    {
        // Convert alias → ClassNameMiddleware
        // e.g. auth → AuthMiddleware
        $class = 'Dorguzen\\Middleware\\RouteMiddleware\\'
            . ucfirst($alias)
            . 'Middleware';

        if (!class_exists($class)) {
            throw new DGZ_Exception(
                'Missing Route Middleware',
                DGZ_Exception::CLASS_NOT_FOUND,
                "Route middleware [$class] was not found."
            );
        }

        return container($class);
    }


    /**
     * Execute resolved route middleware in a PSR-15–compatible pipeline.
     *
     * Supports:
     *  - PSR-15 middleware (process)
     *  - Legacy DGZ middleware (handle)
     *
     * @param array $middlewares  Instantiated middleware objects
     * @param array $routeMatch   Route match data (controller, method, params, etc.)
     * @return mixed              true on success, or middleware response on abort
     */
    public function executeRouteMiddlewarePipeline(array $middlewares, array $routeMatch)
    {
        /**
         * Prepare request + final handler.
         * Returning true here means "middleware passed, continue routing".
         */
        $request = container(PsrRequestAdapter::class);

        $finalHandler = new SimpleRequestHandler(function ($request) {
            return true;
        });

        /**
         * Build pipeline from last → first
         * so each middleware wraps the next.
         */
        $next = $finalHandler;

        for ($i = count($middlewares) - 1; $i >= 0; $i--) {
            $mw = $middlewares[$i];

            /**
             * Case 1️⃣ — PSR-15 middleware
             */
            if (method_exists($mw, 'process')) 
            {
                $next = new SimpleRequestHandler(
                    function ($request) use ($mw, $next) {

                        // Preserve legacy boot() if present
                        if (method_exists($mw, 'boot')) {
                            $mw->boot();
                        }
                        return $mw->process($request, $next);
                    }
                );

                continue;
            }

            /**
             * Case 2️⃣ — Legacy DGZ middleware
             * Expected to return true to continue.
             */
            if (method_exists($mw, 'handle')) {
                $next = new SimpleRequestHandler(
                    function ($request) use ($mw, $next, $routeMatch) {

                        if (method_exists($mw, 'boot')) {
                            $mw->boot();
                        }

                        $result = $mw->handle(
                            $routeMatch['controller'] ?? null,
                            $routeMatch['controllerShortName'] ?? null,
                            $routeMatch['method'] ?? null
                        );

                        // true → continue pipeline
                        if ($result === true) {
                            return $next->handle($request);
                        }

                        // anything else → abort routing
                        return $result;
                    }
                );

                continue;
            }

            /**
             * Case 3️⃣ — Unknown middleware shape
             * Skip safely (non-fatal).
             */
            $next = new SimpleRequestHandler(
                fn ($request) => $next->handle($request)
            );
        }

        /**
         * Kick off the pipeline
         */
        $result = $next->handle($request);

        return ($result === null || $result === true) ? true : $result;
    }
}
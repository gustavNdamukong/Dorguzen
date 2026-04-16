<?php
namespace Dorguzen\Core\Kernel;


use Dorguzen\Config\EnvLoader;
use Dorguzen\Core\DGZ_Router;
use Dorguzen\Core\DGZ_Request;

class CliKernel extends BaseKernel
{

    public function __construct()
    {
        parent::__construct(base_path());
    }

    public function bootstrap(): void
    {
        parent::boot();
        $this->loadRoutes();

        // Load .env for CLI commands
        //$envLoader = $this->container->get(EnvLoader::class);
        //$envLoader->load();
    }

    /*protected function bootstrap(): void
    {
        // Future CLI-specific boot logic goes here
        // Example (later):
        // $this->registerCliServices();
        // $this->fireCliBootEvent();

        // Only thing CLI needs
        require $this->basePath . '/bootstrap/app.php';

        // ❌ DO NOT load routing
        // ❌ DO NOT load helpers_runtime
        // ❌ DO NOT start output buffering
    }*/

    protected function loadRoutes(): void
    {
        $request = container(DGZ_Request::class);

        // This creates THE router instance
        $router = new DGZ_Router($request);

        require DGZ_BASE_PATH . '/routes/web.php';
        require DGZ_BASE_PATH . '/routes/api.php';
    }
}
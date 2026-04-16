<?php 
namespace Dorguzen\Core\Kernel;


use Dorguzen\Core\DGZ_Exception;

/*
This is the key abstraction that will manage both HTTP & CLI kernel access.
Purpose is to encapsulate the bootstrapping of the application before routing.

Its boot() method does:
    Define DGZ_BASE_PATH
    Load Composer autoload
    Require bootstrap files
    Store container reference
*/

class BaseKernel
{
    protected string $basePath;
    protected $container;

    public function __construct(string $basePath)
    {
        $this->basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }

    public function boot(): void
    {
        $this->defineBasePath();
        $this->loadAutoloader();
        $this->runBootstrapFiles();
        $this->captureContainer();
    }

    public function getContainer()
    {
        return $this->container;
    }
    
    /* -------------------------------------------------
       Internal bootstrap steps
    ------------------------------------------------- */

    protected function defineBasePath(): void
    {
        if (!defined('DGZ_BASE_PATH')) {
            define('DGZ_BASE_PATH', $this->basePath);
        }
    }

    protected function loadAutoloader(): void
    {
        $autoload = $this->basePath . '/vendor/autoload.php';

        if (!file_exists($autoload)) {
            throw new \RuntimeException('Composer autoload.php not found.');
        }

        require_once $autoload;
    }

    protected function runBootstrapFiles(): void
    {
        $bootstrapPath = $this->basePath . '/bootstrap';

        $files = [
            'app.php',
            'config.php',
            'helpers.php',
            'custom_helpers.php',
            'helpers_runtime.php',
        ];

        foreach ($files as $file) {
            $fullPath = $bootstrapPath . '/' . $file;

            if (!file_exists($fullPath)) {
                throw new \RuntimeException("Bootstrap file missing: {$file}");
            }

            require_once $fullPath;
        }
    }

    protected function captureContainer(): void
    {
        // Bootstrap files define $container in global scope
        if (isset($GLOBALS['container'])) {
            $this->container = $GLOBALS['container'];
            return;
        }

        // Fallback: local scope capture
        if (isset($container)) {
            $this->container = $container;
            return;
        }

        throw new \RuntimeException(
            'Container not found after bootstrapping.'
        );
    }
}
<?php 

namespace Dorguzen\Core\Config;

use Dotenv\Dotenv;
use Dotenv\Exception\InvalidPathException;

class EnvLoader
{
    protected string $path;



    public function __construct(string $path)
    {
        if ($path === '' || !is_dir($path)) {
            throw new InvalidPathException("EnvLoader base path is invalid.");
        }
        $this->path = rtrim($path, DIRECTORY_SEPARATOR);
    }



    public function load(): void
    {
        // 1. Load base .env (if exists)
        Dotenv::createMutable($this->path, '.env')->safeLoad();

        // 2. Resolve environment-specific (.env) file (prefer getenv)
        $env = getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? null);
        if ($env) {
            Dotenv::createMutable($this->path, ".env.$env")->safeLoad();
        }

        // 3. Load local overrides last, and ONLY if not testing
        if ($env !== 'testing') {
            Dotenv::createMutable($this->path, '.env.local')->safeLoad();
        }
    }



    /**
     * Get a single env value.
     */
    public function get(string $key, $default = null)
    {
        return $_ENV[$key]
            ?? $_SERVER[$key]
            ?? $default;
    }
}


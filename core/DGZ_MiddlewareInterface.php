<?php 

namespace Dorguzen\Core;

/**
 * This guarantees consistent behavior for DGZ’s core and user middlewares.
 */
interface DGZ_MiddlewareInterface
{
    /**
     * Return an array defining the controllers/methods
     * this middleware wants to handle.
     * Example: ['admin' => 'authenticated', 'shop' => 'divert']
     */
    public function boot(): array;

    /**
     * Handle logic for the matched controller/method.
     * Must return true (proceed) or throw an exception to abort.
     */
    public function handle(string $controller, string $controllerShortName, string $method): bool;
}



    

            
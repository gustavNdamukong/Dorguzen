<?php

namespace Dorguzen\Middleware\RouteMiddleware;


use Dorguzen\Core\Psr\PsrRequestAdapter;
use Dorguzen\Core\Psr\SimpleRequestHandler;
use Dorguzen\Core\DGZ_Exception;
use Dorguzen\Config\Config;

/**
 * A PSR-style middleware following our SIMPLE internal contract:
 *
 *   public function process(PsrRequestAdapter $request, SimpleRequestHandler $handler)
 *
 * It returns:
 *   - true  → allow pipeline to continue
 *   - throws DGZ_Exception → abort pipeline
 *
 * No external PSR libraries required.
 */
class AuthMiddleware
{
    public string $name = 'AuthMiddleware';

    public function process(PsrRequestAdapter $request, SimpleRequestHandler $next)
    {
        /** @var Config $config */
        $config = container(Config::class);

        if (
            (isset($_SESSION['authenticated'])) && 
            ($_SESSION['authenticated'] == 'Let Go-'.$config->getConfig()['appName'])
        ) {
            // Continue middleware chain
            return $next->handle($request);
        }
        else
        { 
            throw new DGZ_Exception(
                'Auth middleware says you must be logged in to access this route',
                DGZ_Exception::PERMISSION_DENIED,
                "You must be authenticated to make this request!"
            );
        }
    }
}
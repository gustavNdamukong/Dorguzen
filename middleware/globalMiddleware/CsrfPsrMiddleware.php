<?php

namespace Dorguzen\Middleware\GlobalMiddleware;


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
class CsrfPsrMiddleware
{
    /** Optional priority so your router can sort middleware */
    public int $priority = 1;

    /** Optional name for debugging */
    public string $name = 'CsrfPsrMiddleware';

    public function process(PsrRequestAdapter $request, SimpleRequestHandler $next)
    {
        // When PHP's post_max_size is exceeded it silently empties $_POST,
        // which makes the CSRF token disappear and produces a misleading CSRF error.
        // Catch this case early and surface a clear message instead.
        if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
            $contentLength = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
            $postMaxBytes  = $this->parseIniSize((string) ini_get('post_max_size'));
            if ($contentLength > 0 && $postMaxBytes > 0 && $contentLength > $postMaxBytes) {
                $limit = ini_get('post_max_size');
                throw new DGZ_Exception(
                    'Uploaded file exceeds server max size limit',
                    DGZ_Exception::INVALID_INPUT,
                    "The data you submitted ({$this->formatBytes($contentLength)}) exceeds the server's maximum "
                    . "allowed POST size ({$limit}). Please upload a smaller file."
                );
            }
        }

        // Raw DGZ request object (accessible via adapter)
        $dgzRequest = $request->raw();

        if (!$this->validateCsrfToken($dgzRequest)) {
            throw new DGZ_Exception(
                'Invalid or missing CSRF token',
                DGZ_Exception::PERMISSION_DENIED,
                "Invalid or missing CSRF token. If you submitted a form, make sure the form has a hidden field of
                the name _csrf_token, and its value the result of calling the global getCsrfToken()
                function, or if it was an AJAX request, be sure to send the X-CSRF-TOKEN header with its value as the
                generated token from getCsrfToken()"
            );
        }

        // Continue middleware chain
        return $next->handle($request);
    }

    private function parseIniSize(string $val): int
    {
        $val  = trim($val);
        $last = strtolower(substr($val, -1));
        $num  = (int) $val;
        switch ($last) {
            case 'g': $num *= 1024;
            // fall through
            case 'm': $num *= 1024;
            // fall through
            case 'k': $num *= 1024;
        }
        return $num;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 1) . 'GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 1) . 'MB';
        if ($bytes >= 1024)       return round($bytes / 1024, 1) . 'KB';
        return $bytes . 'B';
    }


    public function validateCsrfToken($dgzRequest): bool
    {
        $request = $dgzRequest;

        /** @var Config $config */
        $config = container(Config::class);

        $method = $request->method();
        $uri = $request->uri(); 

        // Get CSRF exceptions from config
        $csrfExcepts = $config->getConfig()['csrf_except'] ?? [];

        // Only enforce CSRF on unsafe HTTP methods
        if (in_array($method, ['POST', 'PATCH', 'PUT', 'DELETE'])) 
        {
            // Skip paths that match exceptions
            foreach ($csrfExcepts as $exceptPath) {
                if (is_string($exceptPath) && stripos($uri, $exceptPath) !== false) {
                    return true; // CSRF check not required here
                }
            }

            // Retrieve token using DGZ_Request’s helper
            $token = $request->getCsrfTokenFromRequest();

            if (!$request->validateCsrfToken($token)) {
                error_log("CSRF check for {$method} {$uri}: FAILED");
                return false;
            }
        }
        error_log("CSRF check for {$method} {$uri}: PASSED");

        return true;
    }
}
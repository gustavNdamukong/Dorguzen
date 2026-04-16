<?php

namespace Dorguzen\Api\V1\Controllers;

use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_Response;
use Dorguzen\Core\DGZ_Router;
use OpenApi\Attributes as OA;
use OpenApi\Generator;

/**
 * Serves the interactive Swagger UI docs page and the underlying OpenAPI spec.
 *
 * Route:  GET /api/v1/docs        → Swagger UI HTML page
 *         GET /api/v1/docs/spec   → OpenAPI 3.0 JSON spec
 *
 * The spec is built in two steps:
 *   1. zircote/swagger-php scans src/api/v1/controllers/ for #[OA\*] attributes
 *      written by the developer — these produce rich, fully documented path entries.
 *   2. Every API route registered in routes/api.php that is NOT already documented
 *      by step 1 gets a minimal stub entry auto-added, so every endpoint is always
 *      visible in the UI even before annotations have been written.
 *
 * Set API_DOCS_ENABLED=true in .env to enable this feature.
 * Set API_DOCS_ENABLED=false (or leave unset) in production to hide the docs.
 */

// ——— Global spec metadata ——————————————————————————————————————————————————
// These attributes are picked up by Generator::scan() and written into the
// top-level "info" and "components" sections of the generated OpenAPI spec.
// They must exist exactly once in the scanned source tree.
// ——————————————————————————————————————————————————————————————————————————

#[OA\Info(
    version: '1.0.0',
    title: 'Dorguzen API',
    description: 'Interactive documentation for this Dorguzen-powered API. '
        . 'Endpoints marked **Auto-discovered** have been found via route inspection '
        . 'but not yet annotated — add `#[OA\\*]` attributes to their controller '
        . 'methods to promote them to fully documented entries.'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Enter your JWT access token. Obtain one via POST /api/v1/auth/login.'
)]
class DocsController extends DGZ_Controller
{
    // ——— UI page ——————————————————————————————————————————————————————————


    public function getDefaultAction()
    {
        return 'defaultAction';
    }

    public function defaultAction()
    {}

    /**
     * Serve the Swagger UI HTML page.
     * The page loads Swagger UI from CDN and points it at the /spec endpoint below.
     */
    public function index(): void
    {
        if (!env('API_DOCS_ENABLED', false)) {
            (new DGZ_Response())
                ->setStatus(404)
                ->setData(['code' => 404, 'status' => false, 'message' => 'Not found'])
                ->send();
            exit();
        }

        $specUrl = $this->currentBaseUrl() . '/api/v1/docs/spec';
        $appName = $this->config->getConfig()['appBusinessName']
                ?? $this->config->getConfig()['appName']
                ?? 'Dorguzen';
        $title = htmlspecialchars($appName) . ' — API Documentation';

        header('Content-Type: text/html; charset=utf-8');
        echo $this->buildSwaggerHtml($specUrl, $title);
        exit();
    }

    // ——— OpenAPI spec —————————————————————————————————————————————————————

    /**
     * Generate and return the OpenAPI 3.0 JSON spec.
     *
     * Step 1 — scan this directory for #[OA\*] attributes.
     * Step 2 — enumerate all registered API routes from the DGZ router and add
     *           minimal stubs for any route not already covered by step 1.
     */
    public function spec(): void
    {
        if (!env('API_DOCS_ENABLED', false)) {
            (new DGZ_Response())
                ->setStatus(404)
                ->setData(['code' => 404, 'status' => false, 'message' => 'Not found'])
                ->send();
            exit();
        }

        // Step 1: scan API controllers for #[OA\*] attributes
        $scanPath = __DIR__;
        $spec = $this->generateAnnotatedSpec($scanPath);

        // Step 2: add stubs for any API route not already in the spec
        $spec = $this->mergeAutoDiscoveredRoutes($spec);

        // Inject the correct server base URL so Swagger UI sends requests to the
        // right host+path (e.g. http://localhost/camerooncom, not http://localhost/)
        $spec['servers'] = [
            ['url' => $this->currentBaseUrl(), 'description' => 'Current server'],
        ];

        // Sort paths alphabetically for a clean UI display
        if (isset($spec['paths'])) {
            ksort($spec['paths']);
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($spec, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        exit();
    }

    // ——— Private helpers ——————————————————————————————————————————————————

    /**
     * Run zircote/swagger-php over the given directory and return the spec as
     * a plain PHP array. If scanning fails for any reason a safe skeleton is
     * returned so the rest of the pipeline can still add auto-discovered stubs.
     */
    private function generateAnnotatedSpec(string $scanPath): array
    {
        try {
            // Suppress any stray output from the scanner (e.g. deprecation notices)
            ob_start();
            $openapi = Generator::scan([$scanPath]);
            ob_end_clean();

            $json = $openapi->toJson();
            $spec = json_decode($json, true);

            if (!is_array($spec)) {
                return $this->emptySpec();
            }

            return $spec;
        } catch (\Throwable $e) {
            ob_end_clean();
            return $this->emptySpec();
        }
    }

    /**
     * Walk every registered route in the DGZ router. Any route that:
     *   - has an apiVersion (i.e. was registered with apiGet/apiPost/etc.)
     *   - is NOT one of the docs routes themselves
     *   - is NOT already present in the annotated spec's paths
     *
     * ... gets a minimal stub path entry so it still appears in the Swagger UI.
     */
    private function mergeAutoDiscoveredRoutes(array $spec): array
    {
        $spec['paths'] = $spec['paths'] ?? [];

        $allRoutes = DGZ_Router::getInstance()->getRoutes();

        foreach ($allRoutes as $route) {
            // Only API routes
            if (empty($route['apiVersion'])) {
                continue;
            }

            $path   = $route['uri'];
            $method = strtolower($route['method']);

            // Skip the docs routes themselves
            if (str_contains($path, '/docs')) {
                continue;
            }

            // Only add stub when the path+method isn't already annotated
            if (isset($spec['paths'][$path][$method])) {
                continue;
            }

            $spec['paths'][$path]         = $spec['paths'][$path] ?? [];
            $spec['paths'][$path][$method] = [
                'tags'        => ['Auto-discovered'],
                'summary'     => $route['action'] . ' — not yet documented',
                'description' => 'This endpoint was found automatically via DGZ route inspection. '
                    . 'Add `#[OA\\' . strtoupper($method) . '(...)]` to the controller method '
                    . 'to replace this stub with full documentation.',
                'operationId' => preg_replace('/[^a-zA-Z0-9]/', '_', trim($path, '/')) . '_' . $method,
                'responses'   => [
                    '200' => ['description' => 'Success'],
                ],
            ];
        }

        return $spec;
    }

    /**
     * Build the base URL from the current HTTP request so the spec URL is always
     * correct regardless of LIVE_ENV setting (local vs production).
     * e.g. http://localhost/yourAppName  or  https://yourAppName.com
     */
    private function currentBaseUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // SCRIPT_NAME is the front controller e.g. /yourAppName/index.php
        // dirname() gives the app root e.g. /yourAppName (or / on the root domain)
        $path   = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
        return $scheme . '://' . $host . $path;
    }

    /**
     * A safe minimal spec skeleton — used as fallback if the OA scanner fails.
     */
    private function emptySpec(): array
    {
        return [
            'openapi' => '3.0.0',
            'info'    => [
                'title'   => $this->config->getConfig()['appBusinessName'] ?? 'Dorguzen API',
                'version' => '1.0.0',
            ],
            'paths'      => [],
            'components' => [
                'securitySchemes' => [
                    'bearerAuth' => [
                        'type'         => 'http',
                        'scheme'       => 'bearer',
                        'bearerFormat' => 'JWT',
                    ],
                ],
            ],
        ];
    }

    /**
     * Return a self-contained HTML page that loads Swagger UI from CDN.
     *
     * persistAuthorization: true  — keeps the Bearer token between page reloads.
     * deepLinking: true           — makes individual endpoints bookmarkable.
     */
    private function buildSwaggerHtml(string $specUrl, string $title): string
    {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist/swagger-ui.css">
    <style>
        body  { margin: 0; }
        /* Hide the default Swagger topbar (shows the petstore URL input) */
        .swagger-ui .topbar { display: none !important; }
    </style>
</head>
<body>
    <div id="swagger-ui"></div>

    <script src="https://unpkg.com/swagger-ui-dist/swagger-ui-bundle.js"></script>
    <script src="https://unpkg.com/swagger-ui-dist/swagger-ui-standalone-preset.js"></script>
    <script>
        window.onload = function () {
            SwaggerUIBundle({
                url:                  "{$specUrl}",
                dom_id:               '#swagger-ui',
                deepLinking:          true,
                persistAuthorization: true,
                presets: [
                    SwaggerUIBundle.presets.apis,
                    SwaggerUIStandalonePreset
                ],
                layout: "StandaloneLayout"
            });
        };
    </script>
</body>
</html>
HTML;
    }
}

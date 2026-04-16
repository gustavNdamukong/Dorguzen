<?php

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeApiControllerCommand extends AbstractCommand
{
    protected static $defaultName = 'make:api-controller';
    protected static $defaultDescription = 'Create a new API controller';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('make:api-controller')
            ->setDescription('Create a new API controller with DGZ_APITrait and OpenAPI annotation skeleton')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the controller (e.g. Product or ProductController)'
            )
            ->addOption(
                'api-version',
                null,
                InputOption::VALUE_OPTIONAL,
                'The API version directory to place the controller in (e.g. v1, v2)',
                'v1'
            );
    }

    protected function handle(): int
    {
        $name    = $this->input->getArgument('name');
        $version = ltrim($this->input->getOption('api-version'), 'v');
        $version = 'v' . $version;   // normalise: 'v1', 'v2', etc.

        // Normalise class name
        $className = str_ends_with($name, 'Controller')
            ? ucfirst($name)
            : ucfirst($name) . 'Controller';

        // Build namespace — v1 → V1, v2 → V2
        $versionNs = strtoupper($version);   // 'V1'

        // Ensure target directory exists, create it if not
        $dir = DGZ_BASE_PATH . "/src/api/{$version}/controllers";

        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            $this->output->writeln("<info>Created directory:</info> src/api/{$version}/controllers/");
        }

        $path = "{$dir}/{$className}.php";

        if (file_exists($path)) {
            $this->output->writeln('<error>Controller already exists.</error>');
            return self::FAILURE;
        }

        file_put_contents($path, $this->stub($className, $version, $versionNs));

        $this->output->writeln("<info>API controller created:</info> src/api/{$version}/controllers/{$className}.php");
        $this->output->writeln("<comment>Next steps:</comment>");
        $this->output->writeln("  1. Register your routes in <info>routes/api.php</info> using \$router->apiGet() / apiPost() etc.");
        $this->output->writeln("  2. Replace the skeleton #[OA\\*] annotations with your actual request/response details.");
        $this->output->writeln("  3. Inject any service classes your controller needs via the constructor.");

        return self::SUCCESS;
    }

    protected function stub(string $className, string $version, string $versionNs): string
    {
        // Derive a sensible operationId and tag from the class name
        // e.g. ProductController → product, getProduct
        $base        = str_replace('Controller', '', $className);
        $tag         = $base;
        $operationId = 'get' . $base;

        return <<<PHP
<?php

namespace Dorguzen\Api\\{$versionNs}\Controllers;

use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_APITrait;
use Dorguzen\Core\DGZ_Response;
use OpenApi\Attributes as OA;

/**
 * {$className}
 *
 * API controller for the {$base} resource.
 * All endpoints that require authentication must call \$this->validateToken()
 * and check \$this->validatedToken before proceeding.
 *
 * Register your routes in routes/api.php, e.g.:
 *   \$router->apiGet('/api/{$version}/your-path',       '{$base}Api@index',  '{$version}');
 *   \$router->apiPost('/api/{$version}/your-path',      '{$base}Api@store',  '{$version}');
 *   \$router->apiPut('/api/{$version}/your-path/{id}',  '{$base}Api@update', '{$version}');
 *   \$router->apiDelete('/api/{$version}/your-path/{id}','{$base}Api@destroy','{$version}');
 */
class {$className} extends DGZ_Controller
{
    use DGZ_APITrait;

    public function __construct()
    {
        parent::__construct();
        // TODO: inject any service classes your controller needs, e.g.:
        // \$this->myService = container(MyService::class);
    }

    public function getDefaultAction(): string
    {
        return 'defaultAction';
    }

    public function defaultAction(): void {}


    // =========================================================================
    // EXAMPLE ENDPOINT — rename, replace, or duplicate this block as you see fit
    // =========================================================================
    //
    // The index() method below is just a starting point. You are free to:
    //   - Rename it to anything that suits your resource (show, store, update, destroy, etc.)
    //   - Delete it entirely and write your own methods from scratch
    //   - Add as many additional methods as your resource needs
    //
    // Each method that handles a route simply needs:
    //   1. A corresponding route registered in routes/api.php
    //   2. An #[OA\*] annotation above it if you want it fully documented in Swagger UI
    //      (if you skip the annotation it will still appear as an auto-discovered stub)
    //
    // HOW TO WRITE OpenAPI ANNOTATIONS
    // ---------------------------------
    // Each public method that handles a route gets one #[OA\Get/Post/Put/Patch/Delete]
    // attribute directly above it.  Fill in the fields below:
    //
    //   path         → the URI exactly as registered in routes/api.php
    //   operationId  → a unique camelCase name across the whole spec (no spaces/slashes)
    //   summary      → one short line shown as the endpoint title in the Swagger UI
    //   description  → a longer explanation (optional, markdown supported)
    //   tags         → groups endpoints into collapsible sections in the UI
    //   security     → add  security: [['bearerAuth' => []]]  for protected endpoints;
    //                  omit entirely for public endpoints (login, register, etc.)
    //
    // REQUEST BODY (POST / PUT / PATCH)
    //   requestBody: new OA\RequestBody(
    //       required: true,
    //       content: new OA\JsonContent(
    //           required: ['field1', 'field2'],
    //           properties: [
    //               new OA\Property(property: 'field1', type: 'string',  example: 'hello'),
    //               new OA\Property(property: 'field2', type: 'integer', example: 42),
    //           ]
    //       )
    //   )
    //
    // PATH PARAMETERS  e.g. /api/{$version}/resource/{id}
    //   parameters: [
    //       new OA\Parameter(name: 'id', in: 'path', required: true,
    //           schema: new OA\Schema(type: 'integer', example: 1))
    //   ]
    //
    // RESPONSES — always include at least one
    //   new OA\Response(response: 200, description: 'Success')
    //   new OA\Response(response: 401, description: 'Unauthorised — missing or expired token')
    //   new OA\Response(response: 422, description: 'Validation failed')
    //   new OA\Response(response: 500, description: 'Server error')
    //
    // TIP: if you skip annotations entirely, the route still appears automatically in the
    // Swagger UI as an "Auto-discovered" stub — annotations are purely for adding detail.
    // =========================================================================

    #[OA\Get(
        path: '/api/{$version}/TODO-replace-with-your-path',
        operationId: '{$operationId}',
        summary: 'TODO: one-line description of what this endpoint does',
        description: 'TODO: longer description (optional). Supports **markdown**.',
        tags: ['{$tag}'],
        // security: [['bearerAuth' => []]],  // uncomment for protected endpoints
        responses: [
            new OA\Response(
                response: 200,
                description: 'TODO: describe the success response',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'code',    type: 'integer', example: 200),
                        new OA\Property(property: 'status',  type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string',  example: 'Success'),
                        // TODO: add more OA\Property entries for each field in your response
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorised — missing or expired token'),
            new OA\Response(response: 500, description: 'Server error'),
        ]
    )]
    public function index(): void
    {
        \$this->setHeaders();

        // Uncomment the next three lines for protected endpoints:
        // \$this->validateToken();
        // if (!\$this->validatedToken) { exit(); }
        // \$userId = (int) (\$this->validatedUser['user_id'] ?? 0);

        // TODO: implement your logic here, e.g.:
        // \$data = \$this->myService->getAll();

        (new DGZ_Response())
            ->setStatus(200)
            ->setData([
                'code'    => 200,
                'status'  => true,
                'message' => 'TODO: replace with real response data',
                'data'    => [],
            ])
            ->send();
        exit();
    }
}
PHP;
    }
}

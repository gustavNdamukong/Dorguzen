<?php 

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeMiddlewareCommand extends AbstractCommand
{
    protected static $defaultName = 'make:middleware';
    protected static $defaultDescription = 'Create a new middleware';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('make:middleware')
            ->setDescription('Create a new middleware')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'Middleware name (e.g. Auth or CsrfMiddleware)'
            )
            ->addOption(
                'global',
                null,
                InputOption::VALUE_NONE,
                'Create a global middleware'
            )
            ->addOption(
                'route',
                null,
                InputOption::VALUE_NONE,
                'Create a route middleware'
            );
    }

    protected function handle(): int
    {
        $name = $this->input->getArgument('name');

        if (
            !$this->input->getOption('global') &&
            !$this->input->getOption('route')
        ) {
            $this->output->writeln(
                '<error>Please specify --global or --route</error>'
            );
            return self::FAILURE;
        }

        $className = str_ends_with($name, 'Middleware')
            ? $name
            : ucfirst($name) . 'Middleware';

        if ($this->input->getOption('global')) {
            $dir = DGZ_BASE_PATH . '/middleware/globalMiddleware';
            $namespace = 'Dorguzen\\Middleware\\GlobalMiddleware';
            $stub = $this->globalStub($namespace, $className);
        } else {
            $dir = DGZ_BASE_PATH . '/middleware/routeMiddleware';
            $namespace = 'Dorguzen\\Middleware\\RouteMiddleware';
            $stub = $this->routeStub($namespace, $className);
        }

        $path = "{$dir}/{$className}.php";

        if (file_exists($path)) {
            $this->output->writeln('<error>Middleware already exists.</error>');
            return self::FAILURE;
        }

        file_put_contents($path, $stub);

        $this->output->writeln("<info>Middleware created:</info> {$className}");

        return self::SUCCESS;
    }

    /* ---------------- STUBS ---------------- */

    protected function globalStub(string $namespace, string $class): string
    {
        return <<<PHP
<?php

namespace {$namespace};

use Dorguzen\Core\Psr\PsrRequestAdapter;
use Dorguzen\Core\Psr\SimpleRequestHandler;
use Dorguzen\Core\DGZ_Exception;

/**
 * Global Middleware
 *
 * Runs on EVERY request before routing.
 *
 * Minimum contract:
 *  - process(PsrRequestAdapter \$request, SimpleRequestHandler \$next)
 *
 * Flow:
 *  - Inspect / validate request
 *  - Call \$next->handle(\$request) to continue
 *  - OR throw DGZ_Exception to abort
 */
class {$class}
{
    /** Optional priority (lower runs first) */
    public int \$priority = 100;

    /** Optional name for debugging */
    public string \$name = '{$class}';

    public function process(
        PsrRequestAdapter \$request,
        SimpleRequestHandler \$next
    ) {
        // Example: read raw DGZ request
        // \$dgzRequest = \$request->raw();

        // TODO: Add your global logic here

        // Continue middleware chain
        return \$next->handle(\$request);
    }
}
PHP;
    }

    protected function routeStub(string $namespace, string $class): string
    {
        return <<<PHP
<?php

namespace {$namespace};

use Dorguzen\Core\Psr\PsrRequestAdapter;
use Dorguzen\Core\Psr\SimpleRequestHandler;
use Dorguzen\Core\DGZ_Exception;

/**
 * Route Middleware
 *
 * Runs ONLY on routes or route-groups it is attached to.
 *
 * Typical use cases:
 *  - Authentication
 *  - Authorization
 *  - Role checks
 *
 * Minimum contract:
 *  - process(PsrRequestAdapter \$request, SimpleRequestHandler \$next)
 *
 * To block a request:
 *  - throw DGZ_Exception
 */
class {$class}
{
    /** Optional name for debugging */
    public string \$name = '{$class}';

    public function process(
        PsrRequestAdapter \$request,
        SimpleRequestHandler \$next
    ) {
        // Example: inspect request
        // \$dgzRequest = \$request->raw();

        // TODO: Add your route-specific logic here

        // Continue to controller if allowed
        return \$next->handle(\$request);
    }
}
PHP;
    }
}
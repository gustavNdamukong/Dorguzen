<?php 

namespace Dorguzen\Core\CLI\Command;

use ReflectionClass;

class MiddlewareCacheCommand extends AbstractCommand
{
    protected static $defaultName = 'cache:middleware-cache';

    protected static $defaultDescription = 'Cache middleware metadata for faster loading';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('cache:middleware-cache')
            ->setDescription('Cache global and route middleware metadata');
    }

    protected function handle(): int
    {
        $cacheDir  = DGZ_BASE_PATH . '/storage/cache';
        $cacheFile = $cacheDir . '/middleware.php';

        if (!is_dir($cacheDir)) {
            mkdir($cacheDir, 0777, true);
        }

        $data = [
            'global' => $this->cacheGlobalMiddleware(),
            'route'  => $this->cacheRouteMiddleware(),
        ];

        file_put_contents(
            $cacheFile,
            "<?php\n\nreturn " . var_export($data, true) . ";\n"
        );

        $this->output->writeln('<info>Middleware cache generated.</info>');

        return self::SUCCESS;
    }


    protected function cacheGlobalMiddleware(): array
    {
        $dir = DGZ_BASE_PATH . '/middleware/globalMiddleware';
        $files = glob($dir . '/*Middleware.php');

        $items = [];

        foreach ($files as $file) {
            require_once $file;

            $class = $this->resolveClassFromFile($file);

            if (!$class) {
                continue;
            }

            $ref = new ReflectionClass($class);

            // Read priority safely (default 10)
            $priority = $ref->hasProperty('priority')
                ? $ref->getDefaultProperties()['priority'] ?? 10
                : 10;

            $items[] = [
                'class'    => $class,
                'file'     => $file,
                'priority' => $priority,
            ];
        }

        return $items;
    }


    protected function cacheRouteMiddleware(): array
    {
        $dir = DGZ_BASE_PATH . '/middleware/routeMiddleware';
        $files = glob($dir . '/*Middleware.php');

        $items = [];

        foreach ($files as $file) {
            // Load the file so the class becomes available for reflection
            require_once $file;

            // Resolve the FQCN from the file
            $class = $this->resolveClassFromFile($file);

            if (!$class) {
                continue;
            }

            // Convert AuthMiddleware → auth
            $short = basename($file, 'Middleware.php');
            $key = strtolower($short);

            $items[$key] = [
                'class' => $class,
                'file'  => $file,
            ];
        }

        return $items;
    }


    protected function resolveClassFromFile(string $file): ?string
    {
        $contents = file_get_contents($file);

        if (!preg_match('/namespace\s+([^;]+);/m', $contents, $m)) {
            return null;
        }

        $namespace = trim($m[1]);
        $class = basename($file, '.php');

        return $namespace . '\\' . $class;
    }

}
<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\DGZ_Router;

class RouteCacheCommand extends AbstractCommand
{
    protected static $defaultName = 'cache:route-cache';
    protected static $defaultDescription = 'Cache application routes';

    protected function configure(): void
    {
        $this
            ->setName('cache:route-cache')
            ->setDescription('Cache application routes');
    }

    protected function handle(): int
    {
        $router = DGZ_Router::getInstance();
        $routes = $router->getRoutes();

        $cachePath = DGZ_BASE_PATH . '/storage/cache/routes.php';

        if (!is_dir(dirname($cachePath))) {
            mkdir(dirname($cachePath), 0777, true);
        }

        file_put_contents(
            $cachePath,
            "<?php\n\nreturn " . var_export($routes, true) . ";\n"
        );

        $this->output->writeln('<info>✔ Routes cached successfully</info>');

        return self::SUCCESS;
    }
}
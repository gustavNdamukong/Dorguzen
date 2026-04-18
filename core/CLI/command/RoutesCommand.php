<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\DGZ_Router;

class RoutesCommand extends AbstractCommand
{
    protected static $defaultName = 'routes';

    protected static $defaultDescription = 'List all defined routes';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('routes')
            ->setDescription('List all defined routes');
    }

    protected function handle(): int
    {
        $output = $this->output;

        $router = DGZ_Router::getInstance();
        $routes = $router->getDefinedRoutes();

        $output->writeln('');
        $output->writeln('Route count: ' . count($routes));

        if (empty($routes)) {
            $output->writeln('<comment>No defined routes found.</comment>');
            return SELF::SUCCESS;
        }

        foreach ($routes as $route) {
            $line = sprintf(
                '%-6s %-30s %-30s %s %s',
                $route['method'],
                $route['uri'],
                $route['action'],
                $route['apiVersion'] ?: '',
                $route['module'] ?: ''
            );

            $output->writeln(trim($line));
        }
        $output->writeln('');

        return SELF::SUCCESS;
    }
}

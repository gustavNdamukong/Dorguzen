<?php

namespace Dorguzen\Core\CLI\Command;

class RouteClearCommand extends AbstractCommand
{
    protected static $defaultName = 'cache:route-clear';
    protected static $defaultDescription = 'Clear the route cache';

    protected function configure(): void
    {
        $this
            ->setName('cache:route-clear')
            ->setDescription('Clear the route cache');
    }

    protected function handle(): int
    {
        $routeCache = DGZ_BASE_PATH . '/storage/cache/routes.php';

        if (file_exists($routeCache)) {
            unlink($routeCache);
            $this->output->writeln('<info>✔ Route cache cleared</info>');
        } else {
            $this->output->writeln('<comment>No route cache found</comment>');
        }

        return self::SUCCESS;
    }
}
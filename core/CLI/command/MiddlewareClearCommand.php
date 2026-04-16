<?php 

namespace Dorguzen\Core\CLI\Command;

class MiddlewareClearCommand extends AbstractCommand
{
    protected static $defaultName = 'cache:middleware-clear';

    protected static $defaultDescription = 'Clear the middleware cache';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('cache:middleware-clear')
            ->setDescription('Clear the cached middleware metadata');
    }

    protected function handle(): int
    {
        $cacheFile = DGZ_BASE_PATH . '/storage/cache/middleware.php';

        // If cache does not exist, do nothing (safe)
        if (!file_exists($cacheFile)) {
            $this->output->writeln('<info>No middleware cache found.</info>');
            return self::SUCCESS;
        }

        // Attempt deletion
        unlink($cacheFile);

        $this->output->writeln('<info>Middleware cache cleared.</info>');

        return self::SUCCESS;
    }
}
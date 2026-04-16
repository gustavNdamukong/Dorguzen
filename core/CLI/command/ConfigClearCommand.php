<?php

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Attribute\AsCommand;

class ConfigClearCommand extends AbstractCommand
{
    protected static $defaultName = 'cache:config-clear';
    protected static $defaultDescription = 'Clear the configuration cache';

    protected function configure(): void
    {
        $this
            ->setName('cache:config-clear')
            ->setDescription('Clear the configuration cache');
    }

    protected function handle(): int
    {
        $cacheFile = DGZ_BASE_PATH . '/bootstrap/cache/config.php';
        $metaFile  = DGZ_BASE_PATH . '/bootstrap/cache/config.php.meta';

        $cleared = false;

        if (file_exists($cacheFile)) {
            unlink($cacheFile);
            $cleared = true;
        }

        if (file_exists($metaFile)) {
            unlink($metaFile);
            $cleared = true;
        }

        if ($cleared) {
            $this->output->writeln('<info>✔ Configuration cache cleared</info>');
        } else {
            $this->output->writeln('<comment>No configuration cache found</comment>');
        }

        return self::SUCCESS;
    }
}
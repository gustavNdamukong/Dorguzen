<?php 

namespace Dorguzen\Core\CLI\Command;

/**
 * Removes DGZ from global execution paths.
 */
class UninstallCommand extends AbstractCommand
{
    protected static $defaultName = 'uninstall';
    protected static $defaultDescription = 'Uninstall DGZ CLI';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this->setName('uninstall')
             ->setDescription('Uninstall DGZ CLI');
    }

    protected function handle(): int
    {
        // Windows
        if (PHP_OS_FAMILY === 'Windows') {
            $bat = DGZ_BASE_PATH . '/dgz.bat';
            if (file_exists($bat)) {
                @unlink($bat);
            }
            return 0;
        }

        // macOS / Linux
        $target = '/usr/local/bin/dgz';

        if (is_link($target) || file_exists($target)) {
            @unlink($target);
        }

        return 0;
    }
}
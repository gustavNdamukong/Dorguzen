<?php 

namespace Dorguzen\Core\CLI\Command;

/**
 * One-time installer for the DGZ CLI.
 *
 * Responsibilities:
 *  - Make `dgz` runnable without `php`
 *  - Be silent when run via Composer
 *  - Handle macOS/Linux and Windows safely
 *  - Never break if permissions are missing
 */
class InstallCommand extends AbstractCommand
{
    protected static $defaultName = 'install';
    protected static $defaultDescription = 'Install DGZ CLI globally';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this->setName('install')
             ->setDescription('Install DGZ CLI globally');
    }

    protected function handle(): int
    {
        $dgzPath = realpath(DGZ_BASE_PATH . '/dgz');

        if (! $dgzPath) {
            return 0; // Silent fail (Composer-safe)
        }

        // ----------------------------
        // Windows handling
        // ----------------------------
        if (PHP_OS_FAMILY === 'Windows') {
            // On Windows we cannot reliably write to PATH silently.
            // Best practice: create dgz.bat in project root.
            $bat = DGZ_BASE_PATH . '/dgz.bat';

            if (! file_exists($bat)) {
                file_put_contents($bat, "@php \"%~dp0dgz\" %*\r\n");
            }

            return 0;
        }

        // ----------------------------
        // macOS / Linux handling
        // ----------------------------
        $target = '/usr/local/bin/dgz';

        if (file_exists($target)) {
            return 0; // Already installed
        }

        // Attempt symlink (may fail without permissions)
        @symlink($dgzPath, $target);

        return 0;
    }
}
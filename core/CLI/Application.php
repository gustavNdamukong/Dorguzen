<?php

namespace Dorguzen\Core\CLI;

use Dorguzen\Core\CLI\Command\AbstractCommand;

use Dorguzen\Core\CLI\Command\InstallCommand;
use Dorguzen\Core\CLI\Command\UninstallCommand;
use Dorguzen\Core\CLI\Command\TestCommand;

use Dorguzen\Core\CLI\Command\ConfigClearCommand;
use Dorguzen\Core\CLI\Command\RouteCacheCommand;
use Dorguzen\Core\CLI\Command\RouteClearCommand;
use Dorguzen\Core\CLI\Command\MiddlewareCacheCommand;
use Dorguzen\Core\CLI\Command\MiddlewareClearCommand;
use Dorguzen\Core\CLI\Command\MakeCommandCommand;
use Dorguzen\Core\CLI\Command\MakeEventCommand;
use Dorguzen\Core\CLI\Command\MakeJobCommand;
use Dorguzen\Core\CLI\Command\ScheduleRunCommand;

use Dorguzen\Core\CLI\Command\MakeControllerCommand;
use Dorguzen\Core\CLI\Command\MakeApiControllerCommand;
use Dorguzen\Core\CLI\Command\MakeModelCommand;
use Dorguzen\Core\CLI\Command\MakeMiddlewareCommand;
use Dorguzen\Core\CLI\Command\MakeFactoryCommand;
use Dorguzen\Core\CLI\Command\MakeSeederCommand;
use Dorguzen\Core\CLI\Command\MakeJetFormCommand;
use Dorguzen\Core\CLI\Command\MakeTestCommand;

use Dorguzen\Core\CLI\Command\EnvCheckCommand;
use Dorguzen\Core\CLI\Command\RoutesCommand;
use Dorguzen\Core\CLI\Command\ConfigCommand;
use Dorguzen\Core\CLI\Command\LogsCommand;
use Dorguzen\Core\CLI\Command\MakeMigrationCommand;
use Dorguzen\Core\CLI\Command\MigrateCommand;
use Dorguzen\Core\CLI\Command\MigrateRollbackCommand;
use Dorguzen\Core\CLI\Command\MigrateStatusCommand;
use Dorguzen\Core\CLI\Command\MigrateRefreshCommand;
use Dorguzen\Core\CLI\Command\MigrateFreshCommand;
use Dorguzen\Core\CLI\Command\DbSeedCommand;
use Dorguzen\Core\CLI\Command\QueueWorkCommand;
use Dorguzen\Core\CLI\Command\QueueJobsCommand;
use Dorguzen\Core\CLI\Command\QueueRemoveJobCommand;
use Dorguzen\Core\CLI\Command\QueueFailedCommand;
use Dorguzen\Core\CLI\Command\QueueRetryCommand;
use Dorguzen\Core\CLI\Command\QueueForgetCommand;
use Dorguzen\Core\CLI\Command\QueueClearCommand;
use Dorguzen\Core\CLI\Command\QueueStatsCommand;
use Dorguzen\Core\CLI\Command\ServeCommand;
use Dorguzen\Core\CLI\Command\LogTailCommand;
use Dorguzen\Core\CLI\Command\LogPruneCommand;
use Symfony\Component\Console\Application as SymfonyApplication;


class Application
{
    protected SymfonyApplication $console;
    protected $container;


    /**
     * Track registered command names to prevent conflicts
     */
    protected array $registeredCommandNames = [];


    public function __construct($container)
    {
        $this->container = $container;

        // CLI banner — shown on `dgz` / `dgz list` only.
        // getLongVersion() is what Symfony's built-in list command renders at the top,
        // so overriding it in an anonymous subclass is the correct hook.
        $dgzBanner = implode("\n", [
            '',
            '  _____      _____   ______ ',
            ' |   _  \   /  ___| |___  / ',
            ' |  | |  | |  |  _    /  /  ',
            ' |  |_|  | |  |_| |  /  /__ ',
            ' |_____ /   \_____| /______|',
            '',
            '   Dorguzen CLI  —  v1.0.0',
            '',
        ]);

        $this->console = new class('Dorguzen', '1.0.0', $dgzBanner) extends SymfonyApplication {
            private string $dgzBanner;

            public function __construct(string $name, string $version, string $banner)
            {
                parent::__construct($name, $version);
                $this->dgzBanner = $banner;
            }

            public function getLongVersion(): string
            {
                return $this->dgzBanner;
            }
        };

        $this->console->setDefaultCommand('list');
        $this->console->setCatchExceptions(false);

        // Optional but recommended
        $this->console->setAutoExit(false);
    }


    /**
     * Register all CLI commands
     * Symfony's & Dorguzen's.
     */
    public function registerCommands(): void
    {
        // add DGZ commands here
        $this->registerCommandSafely(new InstallCommand($this->container));
        $this->registerCommandSafely(new UninstallCommand($this->container));
        $this->registerCommandSafely(new TestCommand($this->container));

        $this->registerCommandSafely(new ConfigClearCommand($this->container));
        $this->registerCommandSafely(new RouteCacheCommand($this->container));
        $this->registerCommandSafely(new RouteClearCommand($this->container));
        $this->registerCommandSafely(new MiddlewareCacheCommand($this->container));
        $this->registerCommandSafely(new MiddlewareClearCommand($this->container));
        $this->registerCommandSafely(new MakeCommandCommand($this->container));
        $this->registerCommandSafely(new MakeEventCommand($this->container));
        $this->registerCommandSafely(new MakeJobCommand($this->container));
        $this->registerCommandSafely(new ScheduleRunCommand($this->container));
        
        $this->registerCommandSafely(new MakeControllerCommand($this->container));
        $this->registerCommandSafely(new MakeApiControllerCommand($this->container));
        $this->registerCommandSafely(new MakeModelCommand($this->container));
        $this->registerCommandSafely(new MakeMiddlewareCommand($this->container));
        $this->registerCommandSafely(new MakeFactoryCommand($this->container));
        $this->registerCommandSafely(new MakeSeederCommand($this->container));
        $this->registerCommandSafely(new MakeJetFormCommand($this->container));
        $this->registerCommandSafely(new MakeTestCommand($this->container));

        $this->registerCommandSafely(new EnvCheckCommand($this->container));
        $this->registerCommandSafely(new RoutesCommand($this->container));
        $this->registerCommandSafely(new ConfigCommand($this->container));
        $this->registerCommandSafely(new LogsCommand($this->container));
        $this->registerCommandSafely(new MakeMigrationCommand($this->container));
        $this->registerCommandSafely(new MigrateCommand($this->container));
        $this->registerCommandSafely(new MigrateRollbackCommand($this->container));
        $this->registerCommandSafely(new MigrateStatusCommand($this->container));
        $this->registerCommandSafely(new MigrateRefreshCommand($this->container));
        $this->registerCommandSafely(new MigrateFreshCommand($this->container));
        $this->registerCommandSafely(new DbSeedCommand($this->container));
        $this->registerCommandSafely(new QueueWorkCommand($this->container));
        $this->registerCommandSafely(new QueueJobsCommand($this->container));
        $this->registerCommandSafely(new QueueRemoveJobCommand($this->container));
        $this->registerCommandSafely(new QueueFailedCommand($this->container));
        $this->registerCommandSafely(new QueueRetryCommand($this->container));
        $this->registerCommandSafely(new QueueForgetCommand($this->container));
        $this->registerCommandSafely(new QueueClearCommand($this->container));
        $this->registerCommandSafely(new QueueStatsCommand($this->container));
        $this->registerCommandSafely(new ServeCommand($this->container));
        $this->registerCommandSafely(new LogTailCommand($this->container));
        $this->registerCommandSafely(new LogPruneCommand($this->container));


        // -------------------------------------------------
        // Auto-register custom CLI commands
        // src/CLI/Command
        // -------------------------------------------------
        $appCommandPath = DGZ_BASE_PATH."/src/CLI/Commands";

        if (!is_dir($appCommandPath)) {
            return;
        }

        foreach (glob($appCommandPath . '/*.php') as $file) {
            $className = pathinfo($file, PATHINFO_FILENAME);
            $fqcn = 'Dorguzen\\CLI\\Commands\\' . $className;

            if (!class_exists($fqcn)) {
                continue;
            }

            if (!is_subclass_of($fqcn, AbstractCommand::class)) {
                continue;
            }

            // Let the container resolve it if possible
            $command = container($fqcn);
            $this->registerCommandSafely($command);
        }
    }


    /**
     * Run the console application.
     */
    public function run(): int
    {
        // always register commands before you do console.run() else it wont see any commands to run
        $this->registerCommands();
        return $this->console->run();
    }


    /**
     * getConsole exposes the console so code can run console commands 
     * on the fly without creating command classes.
     * @return SymfonyApplication $console
     */
    public function getConsole()
    {
        return $this->console;
    }


    protected function registerCommandSafely($command): void
    {
        $name = $command->getName();

        if (isset($this->registeredCommandNames[$name])) {
            throw new \RuntimeException(
                "CLI command name conflict detected: '{$name}' is already registered."
            );
        }

        $this->registeredCommandNames[$name] = true;
        $this->console->addCommand($command);
    }
}
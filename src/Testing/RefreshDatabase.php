<?php

namespace Dorguzen\Testing;


use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Dorguzen\Core\CLI\Command\MigrateFreshCommand;
use Dorguzen\Core\CLI\Application;

trait RefreshDatabase
{
    protected static bool $databaseRefreshed = false;

    protected function refreshDatabase(): void
    {
        if (static::$databaseRefreshed) {
            return;
        }

        $this->runDatabaseRefresh();

        static::$databaseRefreshed = true;
    }

    protected function runDatabaseRefresh(): void
    {
        // Safety: never allow this outside testing
        // Helps ensure no one drops prod DB by accident
        if (env('APP_ENV') !== 'testing') {
            throw new \RuntimeException(
                'RefreshDatabase can only run in the testing environment.'
            );
        }

        // Run migrate:fresh
        /*
          Execute a DGZ command via Symfony Console,
          This is basically how you can call a Symfony console command
          in code on the fly, outside of a command class in Dorguzen.
        */
        /** @var \Dorguzen\Core\CLI\Application $cliApp */
        $cliApp = container(Application::class);
        $cliApp->registerCommands();
        $console = $cliApp->getConsole();

        $input = new ArrayInput([
                'command' => 'migrate:fresh',
                '--force' => true,
            ]);

        // Silent output; scheduler should not spam console
        $output = new NullOutput();

        $console->run($input, $output);
    }
}

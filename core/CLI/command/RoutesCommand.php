<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\DGZ_Router;

use Dorguzen\Core\DGZ_DBAdapter;

///// The lines below are just for testing ///// DELETE
use Dorguzen\Core\Events\EventService;
use Dorguzen\Core\Events\EventDispatcher;
use Dorguzen\Events\TestEvent;
use Dorguzen\Core\Queues\QueueManager;
use Dorguzen\Jobs\TestSyncJob;
use Dorguzen\Jobs\FailingJob;
use Dorguzen\Config\Config;

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

        //-------------------START TESTING-------------------------------
        try {
            ////$events = container(EventService::class);
            /////$events = container(EventDispatcher::class);
            ////$events->dispatch(new TestEvent('fwffefefefefef'));

            ///event(new TestEvent('hello'));

            dispatch(new FailingJob());

            /////$queue = container(QueueManager::class);
            /////dump('QUEUE MANAGER CLASS:', get_class($queue));

            /////$queue = container(QueueManager::class);
            echo "Pushing job to queue...\n";
            //dd('ALL CONFIG DATA: ', $config->getConfig(), $config->all());
            //echo 'AppName is: '. $config->getConfig('app.appName'); //////
            ////$queue->push(new TestSyncJob());
            /////$queue->push(new FailingJob());
            echo "Returned from queue push\n";
            //event()
            //dispatch()
        }
        catch(\Throwable $e)
        {
            $output->writeln('<error>' . $e->getMessage(). '</error>');
        }
        //-----------------END TESTING--------------------------------

        $output->writeln('');

        return SELF::SUCCESS;
    }
}
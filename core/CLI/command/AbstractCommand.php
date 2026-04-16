<?php 

namespace Dorguzen\Core\CLI\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * All commands extend this class. They must all 
 *  -implement handle()
 *  -return an exit code
 *  
 * Purpose of this class
 *  -Standardise command structure
 *  -Inject container cleanly
 *  -Fire events later
 *  -Avoid Symfony coupling
 *  -Will enhance logging, events, timing, error wrapping
 */
abstract class AbstractCommand extends Command
{
    protected $container;
    protected InputInterface $input;
    protected OutputInterface $output;

    public function __construct($container)
    {
        $this->container = $container;
        parent::__construct();
    }


    final protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->input  = $input;
        $this->output = $output;

        return $this->handle();
    }


    abstract protected function handle(): int;
}
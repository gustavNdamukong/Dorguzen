<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\CLI\Command\AbstractCommand;

class MakeEventCommand extends AbstractCommand
{
    protected static $defaultName = 'make:event';
    protected static $defaultDescription = 'Generate a new Event class';

    protected function configure(): void
    {
        $this
            ->setName('make:event')
            ->setDescription('Generate a new Event class')
            ->addArgument('name', null, 'Name of the Event class');
    }

    protected function handle(): int
    {
        $name = $this->input->getArgument('name');
        $className = str_ends_with($name, 'Event') ? $name : $name . 'Event';
        $filePath = base_path("src/Events/{$className}.php");

        if (file_exists($filePath)) {
            $this->output->writeln("<error>{$className} already exists!</error>");
            return self::FAILURE;
        }

        $stub = <<<PHP
<?php

namespace Dorguzen\Events;

class {$className}
{
    // Add event properties and methods here
}
PHP;

        file_put_contents($filePath, $stub);
        $this->output->writeln("<info>{$className} created successfully at {$filePath}</info>");
        return self::SUCCESS;
    }
}
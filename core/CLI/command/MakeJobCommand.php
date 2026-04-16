<?php

namespace Dorguzen\Core\CLI\Command;


use Dorguzen\Core\CLI\Command\AbstractCommand;

class MakeJobCommand extends AbstractCommand
{
    protected static $defaultName = 'make:job';
    protected static $defaultDescription = 'Generate a new Job class';

    protected function configure(): void
    {
        $this
            ->setName('make:job')
            ->setDescription('Generate a new Job class')
            ->addArgument('name', null, 'Name of the Job class');
    }

    protected function handle(): int
    {
        $name = $this->input->getArgument('name');
        $className = str_ends_with($name, 'Job') ? $name : $name . 'Job';
        $filePath = base_path("src/Jobs/{$className}.php");

        if (file_exists($filePath)) {
            $this->output->writeln("<error>{$className} already exists!</error>");
            return self::FAILURE;
        }

        $stub = <<<PHP
<?php

namespace Dorguzen\Jobs;

class {$className}
{
    public function handle(): void
    {
        // TODO: Implement Job logic
        echo "🔥 {$className} ran successfully";
    }
}
PHP;

        file_put_contents($filePath, $stub);
        $this->output->writeln("<info>{$className} created successfully at {$filePath}</info>");
        return self::SUCCESS;
    }
}
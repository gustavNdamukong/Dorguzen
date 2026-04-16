<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\CLI\Command\AbstractCommand;


/**
 * Example usage:
 * 
 * php dgz make:test unit Orders
 * php dgz make:test feature Checkout
 */
class MakeTestCommand extends AbstractCommand
{
    protected static $defaultName = 'make:test';
    protected static $defaultDescription = 'Generate a new Test class';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('make:test')
            ->setDescription('Generate a new Test class')
            ->addArgument('type', null, 'Specify if its a Feature or Unit test')
            ->addArgument('name', null, 'Name of the Test class');
    }

    protected function handle(): int
    {
        $type = $this->input->getArgument('type');
        $name = $this->input->getArgument('name');

        // ---------------- VALIDATION ----------------
        if (!$type || !$name) {
            $this->output->writeln('<error>Both type and name arguments are required.</error>');
            $this->output->writeln('<comment>Usage: php dgz make:test unit Example</comment>');
            return self::FAILURE;
        }

        $type = strtolower($type);

        if (!in_array($type, ['unit', 'feature'], true)) {
            $this->output->writeln('<error>Type must be either "unit" or "feature".</error>');
            return self::FAILURE;
        }

        // Ensure class name ends with Test
        $className = str_ends_with($name, 'Event') ? $name : $name . 'Test';

        $directory = $type === 'unit'
            ? 'tests/unit'
            : 'tests/feature';

        $namespace = $type === 'unit'
            ? 'Dorguzen\Tests\Unit'
            : 'Dorguzen\Tests\Feature';

        $filePath = base_path("{$directory}/{$className}.php");

        if (file_exists($filePath)) {
            $this->output->writeln("<error>{$className} already exists!</error>");
            return self::FAILURE;
        }

        // Ensure directory exists
        if (!is_dir(base_path($directory))) {
            mkdir(base_path($directory), 0755, true);
        }

        // ---------------- STUB ----------------

        $stub = <<<PHP
<?php

namespace {$namespace};

use PHPUnit\\Framework\\TestCase;

class {$className} extends TestCase
{
    public function test_example(): void
    {
        \$this->assertTrue(true);
    }
}
PHP;

        file_put_contents($filePath, $stub);
        $this->output->writeln("<info>{$className} created successfully at {$filePath}</info>");
        return self::SUCCESS;
    }
}
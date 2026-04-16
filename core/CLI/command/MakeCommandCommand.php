<?php

namespace Dorguzen\Core\CLI\Command;

use Dorguzen\Core\CLI\Command\AbstractCommand;

class MakeCommandCommand extends AbstractCommand
{
    protected static $defaultName = 'make:command';
    protected static $defaultDescription = 'Create a new application CLI command';

    protected function configure(): void
    {
        $this
            ->setName('make:command')
            ->setDescription('Create a new application CLI command')
            ->addArgument('name', null, 'The command class name (e.g. Hello or HelloCommand)');
    }

    protected function handle(): int
    {
        $name = $this->input->getArgument('name');

        if (! $name) {
            $this->output->writeln('<error>Command name is required.</error>');
            return self::FAILURE;
        }

        // Normalize class name
        $classBase = str_ends_with(strtolower($name), 'command')
            ? substr($name, 0, -7)
            : $name;

        $className = ucfirst($classBase) . 'Command';
        $commandName = 'app:' . strtolower(preg_replace('/([a-z])([A-Z])/', '$1-$2', $classBase));

        $dir = DGZ_BASE_PATH . '/src/CLI/Commands';
        $path = $dir . '/' . $className . '.php';

        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        if (file_exists($path)) {
            $this->output->writeln("<error>Command already exists: {$className}</error>");
            return self::FAILURE;
        }

        $stub = <<<PHP
<?php

namespace Dorguzen\\CLI\\Commands;

use Dorguzen\\Core\\CLI\\Command\\AbstractCommand;

class {$className} extends AbstractCommand
{
    protected static \$defaultName = '{$commandName}';
    protected static \$defaultDescription = 'Describe what this command does';

    public function __construct(\$container)
    {
        parent::__construct(\$container);
    }

    protected function configure(): void
    {
        \$this
            ->setName('{$commandName}')
            ->setDescription(self::\$defaultDescription);
    }

    protected function handle(): int
    {
        \$this->output->writeln('{$className} executed');
        return self::SUCCESS;
    }
}
PHP;

        file_put_contents($path, $stub);

        $this->output->writeln("<info>Command created:</info> {$path}");
        $this->output->writeln("<comment>Run it with:</comment> php bin/dgz {$commandName}");

        return self::SUCCESS;
    }
}
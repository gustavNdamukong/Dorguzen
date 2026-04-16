<?php 

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputArgument;

class MakeControllerCommand extends AbstractCommand
{
    protected static $defaultName = 'make:controller';
    protected static $defaultDescription = 'Create a new controller';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('make:controller')
            ->setDescription('Create a new controller')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the controller (e.g. Home or HomeController)'
            );
    }

    protected function handle(): int
    {
        $name = $this->input->getArgument('name');

        // Normalize name
        $className = str_ends_with($name, 'Controller')
            ? ucfirst($name)
            : ucfirst($name) . 'Controller';

        $path = DGZ_BASE_PATH . "/src/controllers/{$className}.php";

        if (file_exists($path)) {
            $this->output->writeln('<error>Controller already exists.</error>');
            return self::FAILURE;
        }

        file_put_contents($path, $this->stub($className));

        $this->output->writeln("<info>Controller created:</info> {$className}");

        return self::SUCCESS;
    }

    protected function stub(string $className): string
    {
        return <<<PHP
<?php

namespace Dorguzen\Controllers;

use Dorguzen\Core\DGZ_Controller;
use Dorguzen\Core\DGZ_View;

class {$className} extends DGZ_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getDefaultAction()
    {
        return 'defaultAction';
    }

    public function defaultAction()
    {
        // Call your main action here
        \$this->index();
    }

    public function index()
    {
        // Example of how views are rendered in Dorguzen

        \$data = [
            'name' => 'John',
            'email' => 'john@doe.com',
        ];

        \$view = DGZ_View::getView('index', \$this, 'html');
        \$this->setPageTitle('{$className}');
        \$view->show(\$data);
    }
}
PHP;
    }
}
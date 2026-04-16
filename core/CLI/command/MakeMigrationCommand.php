<?php 

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputArgument;

class MakeMigrationCommand extends AbstractCommand
{
    protected static $defaultName = 'make:migration';
    protected static $defaultDescription = 'Create a new migration file e.g. create_employees_table';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('make:migration')
            ->setDescription('Create a new migration file')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the migration'
            );
    }

    protected function handle(): int
    {
        $name = $this->input->getArgument('name');
        $tableName = preg_replace('/create_/', '', $name, 1);

        $timestamp = date('Y_m_d_His');
        $fileName = "{$timestamp}_{$name}.php";
        $path = DGZ_BASE_PATH . "/database/migrations/{$fileName}";

        if (file_exists($path)) {
            $this->output->writeln('<error>Migration already exists.</error>');
            return self::FAILURE;
        }

        file_put_contents($path, $this->stub($tableName));

        $this->output->writeln("<info>Migration created:</info> {$fileName}");

        return self::SUCCESS;
    }

    protected function stub($tableName): string
    {
        return <<<PHP
<?php

use Dorguzen\Core\Database\Migrations\Migration;
use Dorguzen\Core\Database\Migrations\Blueprint;

return new class extends Migration {

    public function up(): void
    {
        // schema changes
        \$sql = \$this->schema->create('{$tableName}', function (Blueprint \$table) {

            // define your table here e.g.
            \$table->id();
            \$table->string('name')->nullable();
            \$table->timestamps();

        });

        \$this->addStatement(\$sql);
    }

    public function down(): void
    {
        // rollback
        \$sql = \$this->schema->dropIfExists('{$tableName}');
        \$this->addStatement(\$sql);
    }
};
PHP;
    }
}
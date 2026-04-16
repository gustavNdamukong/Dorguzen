<?php

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeModelCommand extends AbstractCommand
{
    protected static $defaultName = 'make:model';
    protected static $defaultDescription = 'Create a new model';

    public function __construct($container)
    {
        parent::__construct($container);
    }

    protected function configure(): void
    {
        $this
            ->setName('make:model')
            ->setDescription('Create a new model')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the model (e.g. Users, Products)'
            )
            ->addOption(
                'migration',
                'm',
                InputOption::VALUE_OPTIONAL,
                'Also create a migration. Optionally pass a name (e.g. -m create_products_table). Defaults to create_{model}_table.',
                false
            );
    }

    protected function handle(): int
    {
        $name = $this->input->getArgument('name');
        $name = ucfirst($name);
        $path = DGZ_BASE_PATH . "/src/models/{$name}.php";

        if (file_exists($path)) {
            $this->output->writeln('<error>Model already exists.</error>');
            return self::FAILURE;
        }

        file_put_contents($path, $this->stub($name));

        $this->output->writeln("<info>Model created:</info> {$name}");

        // Create a paired migration if -m was passed
        $migrationOption = $this->input->getOption('migration');
        if ($migrationOption !== false) {
            // Use the provided name, or derive one from the model name
            $migrationName = ($migrationOption !== null && $migrationOption !== '')
                ? $migrationOption
                : 'create_' . strtolower($name) . '_table';

            $this->createMigration($migrationName);
        }

        return self::SUCCESS;
    }

    private function createMigration(string $migrationName): void
    {
        // Derive the table name the same way MakeMigrationCommand does
        $tableName = preg_replace('/^create_/', '', $migrationName, 1);
        $tableName = preg_replace('/_table$/', '', $tableName, 1);

        $timestamp = date('Y_m_d_His');
        $fileName  = "{$timestamp}_{$migrationName}.php";
        $path      = DGZ_BASE_PATH . "/database/migrations/{$fileName}";

        if (file_exists($path)) {
            $this->output->writeln("<error>Migration already exists:</error> {$fileName}");
            return;
        }

        file_put_contents($path, $this->migrationStub($tableName));

        $this->output->writeln("<info>Migration created:</info> {$fileName}");
    }

    protected function migrationStub(string $tableName): string
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

    protected function stub(string $className): string
    {
        return <<<PHP
<?php

namespace Dorguzen\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

/**
 * Class {$className}
 *
 * Dorguzen model template. Remember to create a DB table for this model.
 * The table name should be this exact model name, beginning in lowercase.
 * For example if this model's name is Users, the DB table name should be 'users'.
 * If the DB table name is different, add a \$table property to this model and 
 * assign it a string with the table name. 
 */
class {$className} extends DGZ_Model
{
    /**
     * ############### Properties and Methods ###############
     * All model classes must extend the parent model DGZ_Model 
     * to get the full ORM power of Dorguzen.
     * 
     * Table columns
     */
    protected \$_columns = [];

    /**
     * Model data store. This will be used to hydrate the model 
     * on demand using a record id. This makes DGZ models double as 
     * repositories.
     */
    protected \$data = [];

    /**
     * Parent relations
     * Example: ['Users' => 'user_id']
     * 
     * Where 'user_id' is the foreign key field representing the parent model (Users) used on this model.
     * If 'user_id' is not provided, Dorguzen will assume the foreign key on this table is 
     * 'users_id' (the parent class's name beginning in lowercase with a '_id' suffix)
     */
    protected \$_hasParent = [];

    /**
     * Child relations
     * Example: ['Orders' => 'order_user_id']
     * 
     * Where 'order_user_id' is the foreign key field representing this model used on the child model.
     * If 'order_user_id' is not provided, Dorguzen will assume the foreign key on the child
     * table is the name of this class (parent) beginning in lowercase with a '_id' suffix)
     */
    protected \$_hasChild = [];



    /* Optional fields
     * ----------------
     * protected string \$id      (if the table's primary key field is something other than 'id' or 
     *                              the model name followed by a '_id' suffix).
     * 
     * protected string \$table     (if the table's name not the model name beginning in lowercase
    */

    public function __construct(Config \$config)
    {
        parent::__construct(\$config);
    }
}
PHP;
    }
}
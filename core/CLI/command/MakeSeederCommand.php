<?php

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MakeSeederCommand extends AbstractCommand
{
    protected static $defaultName = 'make:seeder';

    protected static $defaultDescription = 'Create a new database seeder class';


    public function __construct($container)
    {
        parent::__construct($container);
    }


    protected function configure(): void
    {
        $this
            ->setName('make:seeder')
            ->setDescription('Create a new database seeder class')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the seeder (e.g. UserSeeder)'
            )
            ->addOption(
                'table',
                null,
                InputOption::VALUE_OPTIONAL,
                'The database table this seeder targets'
            );
    }


    protected function handle(): int
    {
        $name  = $this->input->getArgument('name');
        $table = $this->input->getOption('table');

        // Ensure proper class naming
        if (!str_ends_with($name, 'Seeder')) {
            $name .= 'Seeder';
        }

        $className = preg_replace('/[^a-zA-Z0-9_]/', '', $name);
        $filePath  = DGZ_BASE_PATH . '/database/seeders/' . $className . '.php';

        if (file_exists($filePath)) {
            $this->output->writeln('<error>Seeder already exists.</error>');
            return self::FAILURE;
        }

        // Guess table name if not provided
        $table ??= strtolower(
            str_replace('Seeder', '', $className)
        );

        $stub = $this->getSeederStub($className, $table);

        file_put_contents($filePath, $stub);

        $this->output->writeln(
            "<info>Seeder created:</info> database/seeders/{$className}.php"
        );

        return self::SUCCESS;
    }


    protected function getSeederStub(string $className, string $table): string
    {
        return <<<PHP
<?php

namespace Dorguzen\Database\Seeders;

use Dorguzen\Core\Database\Seeders\Seeder;

// TODO: edit <YourFactory> to the factory you are using to seed 
use Dorguzen\Database\Factories\YourFactory;

/**
 * Seeder classes are responsible for inserting
 * fake or initial data into the database.
 *
 * Every seeder works hand-in-hand with a Factory.
 */
class {$className} extends Seeder
{
    /**
     * The table this seeder populates
     */
    protected string \$table = '{$table}';


    /**
     * Run the database seeding logic
     */
    public function run(): void
    {
        /*
         |--------------------------------------------------
         | How seeders work in DGZ
         |--------------------------------------------------
         | 1. Call \$this->factory(FactoryClass::class)
         | 2. Optionally set record count using count()
         | 3. Optionally create related records using has()
         | 4. Persist records to DB using create()
         |
         | Factories generate the data.
         | Seeders decide *what* and *how much* to insert.
         */

        // Example:
        // \$this->factory(YourFactory::class)
        //     ->pretend(\$this->pretend)
        //     ->count(5)
        //     ->create(\$this->table);
    }


    /**
     * Return the table name (used internally)
     */
    public function getTable(): string
    {
        return \$this->table;
    }
}
PHP;
    }
}
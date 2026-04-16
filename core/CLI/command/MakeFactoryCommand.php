<?php

namespace Dorguzen\Core\CLI\Command;

use Symfony\Component\Console\Input\InputArgument;

class MakeFactoryCommand extends AbstractCommand
{
    protected static $defaultName = 'make:factory';
    protected static $defaultDescription = 'Create a new database factory';

    protected function configure(): void
    {
        $this
            ->setName('make:factory')
            ->setDescription('Create a new database factory class')
            ->addArgument(
                'name',
                InputArgument::REQUIRED,
                'The name of the factory (e.g. UserFactory)'
            );
    }

    protected function handle(): int
    {
        $name = $this->input->getArgument('name');

        // Ensure Factory suffix
        if (!str_ends_with($name, 'Factory')) {
            $name .= 'Factory';
        }

        $filePath = DGZ_BASE_PATH . "/database/factories/{$name}.php";

        if (file_exists($filePath)) {
            $this->output->writeln('<error>Factory already exists.</error>');
            return self::FAILURE;
        }

        file_put_contents($filePath, $this->stub($name));

        $this->output->writeln("<info>Factory created:</info> {$name}");

        return self::SUCCESS;
    }

    /**
     * Factory boilerplate with teachable comments
     */
    protected function stub(string $className): string
    {
        return <<<PHP
<?php

namespace Dorguzen\Database\Factories;

use Dorguzen\Core\Database\Seeders\Factory;
use Dorguzen\Core\Database\Seeders\Pools\NamePool;
use Dorguzen\Core\Database\Seeders\Pools\EmailPool;
use Dorguzen\Core\Database\Seeders\Pools\DatePool;

/**
 * {$className}
 *
 * Factories are responsible for generating fake data
 * for database seeding.
 *
 * A factory:
 *  - Defines a single row of data via definition()
 *  - Can generate many rows using count()
 *  - Can apply states (admin, inactive, etc.)
 *  - Can create related records using has()
 */
class {$className} extends Factory
{
    /**
     * Optional: default table name.
     * You may override this per create() call in your seeder.
     */
    protected string \$table = '';

    /**
     * definition()
     *
     * Must return ONE row of fake data.
     * This method is called repeatedly when count(n) is used.
     * 
     * Note: the fields you define here in definition() must be fields that exist 
     * in the corresponding migration file and, or the existing corresponding DB table.
     */
    protected function definition(): array
    {
        // TODO: below are examples of usage-delete or use as you see fit
        // \$name = NamePool::full();
        // \$email = \$this->unique('email', function () use (\$name) {
        //     return EmailPool::random(\$name);
        // });
        
        return [
            // 'name'       => \$name,
            // 'email'      => \$email, 
            // 'password'   => 'secret123',
            // 'role'       => 'user',
            // 'is_active'  => 1,
            // 'created_at' => DatePool::now(),
            // 'updated_at' => DatePool::now(),
        ];
    }

    /* ---------- OPTIONAL HOOKS ---------- */

    /**
     * Runs BEFORE inserting a record into the database.
     * Useful for hashing passwords, normalizing data, etc.
     */
    protected function beforeCreate(array &\$attributes): void
    {
        // Modify attributes before insert
        // For example; Automatically hash the value of the defined 
        // password field above, before insertion.
        // TODO: delete this example code if not needed.
        \$attributes['password'] = password_hash(
            \$attributes['password'],
            PASSWORD_BCRYPT
        );
    }

    /**
     * Runs AFTER a record has been inserted.
     * \$insertId is the primary key of the inserted row.
     */
    protected function afterCreate(array \$attributes, int \$insertId): void
    {
        // Perform post-insert actions
    }

    /* ---------- OPTIONAL STATES ---------- */

    /**
     * Example usage:
     * \$this->factory(UserFactory::class)->admin()->create('users');
     */
    public function exampleState(): static
    {
        return \$this->state([
            // 'role' => 'admin',
        ]);
    }
}
PHP;
    }
}
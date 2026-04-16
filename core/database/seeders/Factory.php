<?php 

namespace Dorguzen\Core\Database\Seeders;

use Dorguzen\Core\DGZ_DBAdapter;

/**
 * Factory is the base factory class
 */
abstract class Factory
{
    protected DGZ_DBAdapter $db;

    protected static array $used = [];

    protected array $states = [];

    protected int $count = 1;

    protected bool $pretend = false;

    /**
     * $relations data structure will be like this:
     * 
     *  ['factory' => $factoryClass, 'count'   => $count]
     * 
     * Where 'factory' is the child record to be inserted, 
     * and 'count' is the number of records to created for 
     * each parent record.
     *  
     * @var array
     */
    protected array $relations = [];

    

    public function __construct(DGZ_DBAdapter $db)
    {
        $this->db = $db;
    }

    /**
     * Number of records to generate
     */
    public function count(int $count): static
    {
        $this->count = $count;
        return $this;
    }

    /**
     * Must return a single row of data
     */
    abstract protected function definition(): array;



    /**
     * make() ONLY generates the seed data (no DB writes here)
     */
    public function make(): array
    {
        $rows = [];

        for ($i = 0; $i < $this->count; $i++) {
            $rows[] = $this->resolveAttributes();
        }

        return $rows;
    }

    /**
     * Creat() inserts the seed data into DB - real seeding
     * @param string $table
     * @return array
     */
    public function create(string $table): array
    {
        $rows = $this->make();
        $inserted = [];
        
        foreach ($rows as $row) {
            if (!$this->isPretending()) {
                // BEFORE
                $this->beforeCreate($row);
            }

            $columns = array_keys($row);
            $values  = array_values($row);

            $placeholders = implode(', ', array_fill(0, count($values), '?'));
            $colsSql      = implode(', ', array_map(fn ($c) => "`{$c}`", $columns));

            $sql = "INSERT INTO `{$table}` ({$colsSql}) VALUES ({$placeholders})";

            $insertId = 0;

            if (!$this->isPretending()) {
                $this->db->execute($sql, $values);
                $insertId = (int) $this->db->insert_id();

                // AFTER
                $this->afterCreate($row, $insertId);

                // 🔥 process relationships
                foreach ($this->relations as $relation) {
                    $childFactory = new $relation['factory']($this->db);
                    $foreignKeyField = $relation['foreignKey'] != '' ? $relation['foreignKey'] : "{$table}_id";

                    $childFactory
                        ->count($relation['count'])
                        ->state([
                            $foreignKeyField => $insertId,
                        ])
                        ->create($relation['table']);
                }
            } 
            $inserted[] = $row;
        }
        return $inserted;
    }


    /**
     * state() is used in defining states eg roles for (the randomly) generated user data
     * It will be used by factories
     * @param callable|array $state
     * @return Factory
     */
    public function state(callable|array $state): static
    {
        if (is_callable($state)) {
            $this->states[] = $state;
        } else {
            $this->states[] = fn () => $state;
        }

        return $this;
    }


    /**
     * pretend() makes it possible for you to run save commands passing a --pretend flag.
     * This will not run the command but rather display to you what will happen including 
     * any SQL queries that will be ran, so you know how it works.
     * @param bool $state
     * @return Factory
     */
    public function pretend(bool $state = true): static
    {
        $this->pretend = $state;
        return $this;
    }

    public function isPretending(): bool
    {
        return $this->pretend;
    }


    protected function factory(string $factoryClass): Factory
    {
        return new $factoryClass($this->db);
    }



    /**
     * has() will be used by dev to make a factory insert child records into another 
     * (child) table after inserting records in a (parent) table.
     * 
     * For example, placing this in the UserFactory will insert 2 posts for each user record inserted:
     * 
     *      $this->has(PostFactory::class, 2, 'ChildTableName', 'optionalForeignKey');
     * 
     * @param string $factory the child table factory
     * @param string $table the table name of the child table
     * @param int $count optional. The number of inserts to create for each parent insert. Default is 1.
     * @param string $foreignKey optional. If not given, DGZ will make one by adding an '_id' suffix 
     *      to parent table name. Therefore, if in your migration file, you named the fk field of your 
     *      target child table with the parent table name plus a '_id' suffix, you can leave out this 
     *      parameter.  
     * @return Factory
     */
    public function has(string $factory, string $table, int $count= 1, string $foreignKey = ''): static
    {
        $this->relations[] = [
            'factory' => $factory,
            'table'   => $table,
            'count'   => $count,
            'foreignKey' => $foreignKey
        ];

        return $this;
    }


    /**
     * unique() is used to make sure all the generated fake emails are unique,
     * as to prevent the unique constrain on the users DB table 'email' field.
     * @param string $key
     * @param callable $generator
     */
    protected function unique(string $key, callable $generator)
    {
        do {
            $value = $generator();
        } while (isset(self::$used[$key][$value]));

        self::$used[$key][$value] = true;

        return $value;
    }

    
    // 🔹 Hooks (optional overrides)
    protected function beforeCreate(array &$attributes): void {}
    

    protected function afterCreate(array $attributes, int $insertId): void {}


    protected function resolveAttributes(): array
    {
        $attributes = $this->definition();

        foreach ($this->states as $state) {
            $stateAttributes = is_callable($state)
                ? $state($attributes)
                : $state;

            // IMPORTANT: state must override definition
            $attributes = array_merge(
                $attributes,
                $stateAttributes
            );
        }
        return $attributes;
    }
}
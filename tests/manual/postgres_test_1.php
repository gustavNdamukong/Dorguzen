<?php 

namespace Dorguzen\Tests\Manual;

use PDO;
use Dorguzen\Config\Config;

// TEST THAT DGZ postgres DB CONNECTION WORKS 
/*
    Your config setup should look like this:

        env.local:

            DB_CONNECTION=pgsql # options: [mysqli, pdo, sqlite, pgsql]
            DB_HOST=127.0.0.1
            DB_USERNAME=user # your mac user
            DB_PASSWORD=
            DB_DATABASE=dorguzen_test
            DB_PORT=5432 # 3306 for mysql or 5432 for pgsql (postgres)

        -Two easy ways to have a PostgreSQL engine locally is to either 
            -install the Postgres app on your machine and connect to it using 
                the client Dbeaver 
            -Or use Docker
            
        -Dont forget to clear your config cache.
        -Create a database 'dorguzen_test' in your Postgres system (Docker or desktop app).
        -You may have to create a logs table in your new 'dorguzen_test' database. It should 
            have these fields:
                logs_id
                logs_title
                logs_message
                context_json
                logs_created

    Run this test file in the CLI like this: 

        php tests/manual/postgres_test_1.php

    This should prove to you that the following operations work:

        Connection
        MATCH
        Parameters
        CREATE
        SET:

    You should see something like this:

        users table ready.
        Insert successful.
        currentUser with id 2 is:
        Array
        (
            [0] => Array
                (
                    [id] => 2
                    [name] => Gustav
                    [email] => gustav@example.com
                )

        )

    What this pass means:
        👉 DB connection with PostgreSQL works
           Insertion works 
           Your ORM works as seen via the SelectWhere() query 

        
*/

require_once __DIR__ . '/cliTestHeader.php';

$config = container(Config::class);
$DBcredentials = $config->getConfig('database.DBcredentials');

// build a DSN string like this: "pgsql:host=127.0.0.1;port=5432;dbname=dorguzen_test"
$dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $DBcredentials['host'],
            $DBcredentials['port'] ?? 5432,
            $DBcredentials['db']
        );



echo "==== DGZ Postgres Test #1 ====\n\n";

$pdo = new PDO(
    $dsn,
    $DBcredentials['username'],
    $DBcredentials['pwd']
);

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

/**
 * Create table
 */
$pdo->exec("
    CREATE TABLE IF NOT EXISTS users (
        id SERIAL PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(150)
    )
");

echo "users table ready.\n";

/**
 * Insert data
 */
$stmt = $pdo->prepare("
    INSERT INTO users (name, email)
    VALUES (:name, :email) RETURNING id
");

$stmt->execute([
    'name' => 'Gustav',
    'email' => 'gustav@example.com'
]);

$lastInsertedId = $stmt->fetchColumn();

echo "Insert successful.\n";

//----------------------------------------------------
// test ORM with PostgreSQL
//----------------------------------------------------
$db = \Dorguzen\Core\DGZ_DB_Singleton::getInstance();

$users = new UsersPostgresTestModel($config);

$modelIdField = $users->getIdFieldName();

// select the user
$selectWhereClause = [$modelIdField => $lastInsertedId];
$testUser = $users->selectWhere([], $selectWhereClause);

echo "currentUser with id {$lastInsertedId} is:\n";
print_r($testUser);
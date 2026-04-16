<?php 

namespace Dorguzen\Tests\Manual;

use Dorguzen\Config\Config;
use Dorguzen\Core\Database\Graph\DGZ_Neo4jClient;

// TEST THAT DGZ neo4j DB CONNECTION WORKS 
/*

    For config setup:

        -Your .env file should look like this:

            # Neo4j DB Connection 
            NEO4J=true # whether or not to use a neo4j DB
            NEO4J_URI='bolt://localhost:7687'
            NEO4J_USERNAME=neo4j
            NEO4J_PASSWORD=neo4j_admin1

        -Your configs/database.php file should pick it up like this:

            'Neo4jCredentials' => [
                'uri' => env('NEO4J_URI'),
                'username' => env('NEO4J_USERNAME'),
                'password' => env('NEO4J_PASSWORD'),
            ],

        -You could download Neo4j desktop, but you can just as well use Docker. 
        -If we assume we use Neo4j Desktop, 
            -Install Neo4j Desktop
            -Create an instance, and eg give it the name: gus_neo4jInstance
            -Create a database, and name it eg neo4j
                -Create a Username-probably accept the default one: neo4j
                -Create a password eg neo4j_admin1

            -Note carefully here that this username and password ofor the DB you just 
                created in your neo4j Desktop MUST match the same ones in yothe neo4j connection 
                credentials in your .env file. 

                Don't worry if the CONNECTION URI value in your neo4j desktop is:
                        neo4j://127.0.0.1:7687

                while that in your .env file says:
                         'bolt://localhost:7687'

                That will cause not problem at all. The key is for that port number at the end (:7687)
                to match.


    Run this test file in the CLI like this: 

        php tests/manual/neo4j_test_1.php

    This should prove to you that the following operations work:

        Connection
        MATCH
        Parameters
        CREATE
        SET:

    What you will see if it works well, is:

        ==== DGZ Neo4j Test #1 ====

        1. Testing Connection...
        Array
        (
            [0] => Array
                (
                    [test] => 1
                )

        )

        2. Testing Simple MATCH...
        Array
        (
        )

        3. Testing Parameter Binding...
        Array
        (
        )

        4. Testing CREATE...
        User created.

        5. Testing UPDATE (SET)...
        User updated.

        ==== All Neo4j tests executed. ====
        

    What this pass means:
        👉 All these now work:
            -PostgreSQL is working
            -Neo4j is working
            -PDO is working
            -Laudis driver is correctly configured
            -Auth system is correct
            -Transaction wrapper is implemented
            -Result normalization is working

        
*/

require_once __DIR__ . '/cliTestHeader.php';

echo "==== DGZ Neo4j Test #1 ====\n\n";


$config = container(Config::class);
$neo4jConn = $config->getConfig('database.Neo4jCredentials');

$neo = new DGZ_Neo4jClient($neo4jConn);

/**
 * 1. Connection Test
 */
echo "1. Testing Connection...\n";
$result = $neo->run('RETURN 1 AS test');
print_r($result);

/**
 * 2. Simple MATCH
 */
echo "\n2. Testing Simple MATCH...\n";
$result = $neo->run('MATCH (n) RETURN n LIMIT 3');
print_r($result);


/**
 * 3. Parameter Binding
 */
echo "\n3. Testing Parameter Binding...\n";
$result = $neo->run(
    'MATCH (u:User {name: $name}) RETURN u',
    ['name' => 'Alice']
);
print_r($result);


/**
 * 4. Write Query (CREATE)
 */
echo "\n4. Testing CREATE...\n";
$neo->run(
    'CREATE (u:User {name: $name}) RETURN u',
    ['name' => 'DGZ_Test_User']
);
echo "User created.\n";


/**
 * 5. Update Query (SET)
 */
echo "\n5. Testing UPDATE (SET)...\n";
$neo->run(
    'MATCH (u:User {name: $name})
     SET u.updated = true
     RETURN u',
    ['name' => 'DGZ_Test_User']
);

echo "User updated.\n";

echo "\n==== All Neo4j tests executed. ====\n";
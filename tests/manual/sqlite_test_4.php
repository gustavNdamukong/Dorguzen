<?php 

namespace Dorguzen\Tests\Manual;

use Dorguzen\Config\Config;

// TEST THAT DGZ ORM WORKS FOR SELECT & UPDATE QUERIES IN SQLite  
/*
    Run this test file in the CLI like this: 

        php tests/manual/sqlite_test_4.php

    You should see:
        
       currentUser with named 'Alice' is:
        Array
        (
            [0] => Array
                (
                    [user_id] => 1
                    [name] => Alice
                    [email] => alice@example.com
                )

        )
        User successfully updated:
        Array
        (
            [0] => Array
                (
                    [user_id] => 1
                    [name] => Alice
                    [email] => alice_a@example.com
                )

        )

    This assumes you had the record in the 'users' table, with the name of Alice, and email value 'alice@example.com'
    which was inserted by the script in tests/manual/sqlite_test_3.php. 
    Check that it's there before you run the script and adjust the data as necessary.

    What this pass means:
        👉 The ORM engine works via the SQLite driver:

        ✔ The ORM auto detects model table name via the model $table property, from UsersSqliteTestModel to 'users'
        ✔ The ORM auto detects model's primary key field (via the getIdFieldName())
        ✔ The ORM does SELECTs
        ✔ The ORM does UPDATEs
*/

require_once __DIR__ . '/cliTestHeader.php';

$db = \Dorguzen\Core\DGZ_DB_Singleton::getInstance();

$config = container(Config::class);
$users = new UsersSqliteTestModel($config);

$modelIdField = $users->getIdFieldName();

// select the user
$selectWhereClause = ['name' => 'Alice'];
$testUser = $users->selectWhere([], $selectWhereClause);

echo "currentUser with named 'Alice' is:\n";
print_r($testUser);


// update the user
// change user email from 'alice@example.com' to 'alice_a@example.com'
$users->email = 'alice_a@example.com';
$done = $users->update($selectWhereClause);

if ($done)
{
    // select updated user
    $updatedTestUser = $users->selectWhere([], $selectWhereClause);

    echo "User successfully updated:\n";
    print_r($updatedTestUser);
}
else 
{
    echo "User could not be updated:\n";
}
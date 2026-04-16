<?php 

namespace Dorguzen\Tests\Manual;

use Dorguzen\Config\Config;

// TEST THAT DGZ ORM WORKS FOR SELECT & UPDATE QUERIES IN PDO  
/*
    Run this test file in the CLI like this: 

        php tests/manual/pdo_test_4.php

    You should see:
        
       currentUser with id 2 is:
        Array
        (
            [0] => Array
                (
                    [id] => 2
                    [name] => Bob
                    [email] => bob@example.com
                )

        )

        Array
        (
            [0] => Array
                (
                    [id] => 2
                    [name] => Bob
                    [email] => bobby@example.com
                )

        )

    This assumes you had the record in the users_test table, with the id of 2, and email value 'bob@example.com'
    which was inserted by the script in tests/manual/pdo_test_3.php. 
    Check that it's there before you run the script and adjust the data as necessary.

    What this pass means:
        👉 The ORM engine works via the PDO driver:

        ✔ The ORM auto detects model table name via the model $table property, from UsersTestModel to 'users_test'
        ✔ The ORM auto detects model's primary key field (via the getIdFieldName())
        ✔ The ORM does SELECTs
        ✔ The ORM does UPDATEs
*/

require_once __DIR__ . '/cliTestHeader.php';

$db = \Dorguzen\Core\DGZ_DB_Singleton::getInstance();

$config = container(Config::class);
$users = new UsersPdoTestModel($config);

$modelIdField = $users->getIdFieldName();

// select the user
$selectWhereClause = [$modelIdField => 2];
$testUser = $users->selectWhere([], $selectWhereClause);

echo "currentUser with id 2 is:\n";
print_r($testUser);


// update the user
$updateWhereClause = [$modelIdField => 2];

$users->email = 'bobby@example.com';
$done = $users->update($updateWhereClause);

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
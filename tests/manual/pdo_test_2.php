<?php 

// TEST THAT PDO DB SCHEMA INTROSPECTION (WHICH WILL BE USED BY DGZ ORM) WORKS
/*
    Here we want to see that the 'users' table we created in test 1 (in tests/pdo_test_1.php)
    worked and that table exists with its fields as defined.

    Run this test file in the CLI like this: 

        php tests/manual/pdo_test_2.php

    You should see something like this:
        
        Array with Field, Type, etc.

        Array
        (
            [0] => Array
                (
                    [Field] => id
                    [Type] => int(11)
                    [Null] => NO
                    [Key] => PRI
                    [Default] => 
                    [Extra] => auto_increment
                )

            [1] => Array
                (
                    [Field] => name
                    [Type] => varchar(100)
                    [Null] => YES
                    [Key] => 
                    [Default] => 
                    [Extra] => 
                )

            [2] => Array
                (
                    [Field] => email
                    [Type] => varchar(150)
                    [Null] => YES
                    [Key] => 
                    [Default] => 
                    [Extra] => 
                )

        )

    What this pass means:

        -PDO connection works
        -Table exists
        -Schema is readable
*/

require_once __DIR__ . '/cliTestHeader.php';
//-------------


$db = Dorguzen\Core\DGZ_DB_Singleton::getInstance();

$schema = $db->query("DESCRIBE users_test");

print_r($schema);
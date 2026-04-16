<?php 

// TEST THAT SQLite DB SCHEMA INTROSPECTION (WHICH WILL BE USED BY DGZ ORM) WORKS
/*
    Here we want to see that the 'users' table we created in test 1 (in tests/sqlite_test_1.php)
    worked and that table exists with its fields as defined.

    Run this test file in the CLI like this: 

        php tests/manual/sqlite_test_2.php

    You should see something like this:
        
        Array
        (
            [0] => Array
                (
                    [cid] => 0
                    [name] => user_id
                    [type] => INTEGER
                    [notnull] => 0
                    [dflt_value] => 
                    [pk] => 1
                )

            [1] => Array
                (
                    [cid] => 1
                    [name] => name
                    [type] => TEXT
                    [notnull] => 1
                    [dflt_value] => 
                    [pk] => 0
                )

            [2] => Array
                (
                    [cid] => 2
                    [name] => email
                    [type] => TEXT
                    [notnull] => 1
                    [dflt_value] => 
                    [pk] => 0
                )

        )

    What this pass means:

        -SQLite connection works
        -Table exists
        -Schema is readable
*/

require_once __DIR__ . '/cliTestHeader.php';
//-------------


$db = Dorguzen\Core\DGZ_DB_Singleton::getInstance();

$schema = $db->query("PRAGMA table_info(users)");

print_r($schema);




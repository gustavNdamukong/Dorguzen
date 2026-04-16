<?php 

// TEST THAT PDO INSERT + lastInsertId + SELECT QUERIES WORK
/*
    Run this test file in the CLI like this: 

        php tests/manual/pdo_test_3.php

    You should see:
        
        Inserted ID: 1
        Array
        (
            [0] => Array
                (
                    [id] => 1
                    [name] => Bob
                    [email] => bob@example.com
                )

        )

    What this pass means:
        👉 The PDO driver works:

        ✔ Does INSERT
        ✔ fetches lastInsertId
        ✔ Does SELECT
*/

require_once __DIR__ . '/cliTestHeader.php';

$db = Dorguzen\Core\DGZ_DB_Singleton::getInstance();

$db->execute(
    "INSERT INTO users_test (name, email) VALUES (?, ?)",
    ['Bob', 'bob@example.com']
);

$id = $db->insert_id();

$row = $db->query(
    "SELECT * FROM users_test WHERE id = ?",
    [$id]
);

echo "Inserted ID: {$id}\n";
print_r($row);
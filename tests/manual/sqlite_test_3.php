<?php 

// TEST THAT SQLite INSERT + lastInsertId + SELECT QUERIES WORK
/*
    Run this test file in the CLI like this: 

        php tests/manual/sqlite_test_3.php
        
    You should see:
        
        Inserted ID: 1
        Array
        (
            [0] => Array
                (
                    [user_id] => 1
                    [name] => Alice
                    [email] => alice@example.com
                )
        )

    What this pass means:
        👉 The SQLite driver works:

        ✔ Does INSERT
        ✔ fetches lastInsertId
        ✔ Does SELECT
*/

require_once __DIR__ . '/cliTestHeader.php';

$db = Dorguzen\Core\DGZ_DB_Singleton::getInstance();

$db->execute(
    "INSERT INTO users (name, email) VALUES (?, ?)",
    ['Alice', 'alice@example.com']
);

$id = $db->insert_id();

$row = $db->query(
    "SELECT * FROM users WHERE user_id = ?",
    [$id]
);

echo "Inserted ID: {$id}\n";
print_r($row);
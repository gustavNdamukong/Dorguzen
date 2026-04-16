<?php 

// TEST PDO DB CONNECTION
/*
    Create a test table 'users_test' in your MySQL DB. Use the SQL tab in your DB client app 

        CREATE TABLE users_test (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100),
            email VARCHAR(150)
        );

    Configure your PDO driver (in .env file):

        DB_CONNECTION=pdo
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=DBName
        DB_USERNAME=yourUserName
        DB_PASSWORD=yourPassword

    Run this test file in the CLI like this: 

        php tests/manual/pdo_test_1.php

    You should see:

        Dorguzen\Core\DGZ_DBAdapter

    What this pass means:
        means:

            ✅ The DB Adapter works
            ✅ CLI kernel booted correctly
            ✅ Container resolved DB driver
            ✅ DGZ_PDODriver was instantiated
            ✅ Adapter wrapped it
            ✅ Singleton returned properly
*/

require_once __DIR__ . '/cliTestHeader.php';

$db = Dorguzen\Core\DGZ_DB_Singleton::getInstance();
echo get_class($db), PHP_EOL;
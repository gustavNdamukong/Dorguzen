<?php 

// TEST SQLite DB CONNECTION 
/*
    Create a test table 'users' in the Sqlite DB via the CLI by running these commands:

        // connect to Sqlite DB-comes with most systems:
        sqlite3 storage/database.sqlite

        // then at the sqlite prompt, run the CREATE TABLE command:

        CREATE TABLE users (
            user_id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL
        );
        .quit

    Run this test file in the CLI like this: 

        php tests/manual/sqlite_test_1.php

    You should see:

        Dorguzen\Core\DGZ_DBAdapter
        
    What this pass means:
        means:

            ✅ The DB Adapter works
            ✅ CLI kernel booted correctly
            ✅ Container resolved DB driver
            ✅ SQLite driver instantiated
            ✅ Adapter wrapped it
            ✅ Singleton returned properly
*/

require_once __DIR__ . '/cliTestHeader.php';

$db = Dorguzen\Core\DGZ_DB_Singleton::getInstance();
echo get_class($db), PHP_EOL;
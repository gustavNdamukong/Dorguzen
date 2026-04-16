<?php

namespace Dorguzen\Configs;

return [
    'DBcredentials' => [
        'username'       => env('DB_USERNAME', 'root'),
        'pwd'            => env('DB_PASSWORD', 'root'),
        'db'             => env('DB_DATABASE', 'dorguzapp'),
        'host'           => env('DB_HOST', 'localhost'),
        'connectionType' => env('DB_CONNECTION', 'mysqli'),
        'port'           => env('DB_PORT', 3306),
        'key'            => env('DB_KEY', 'takeThisWith@PinchOfSalt'),

        // Needed only for SQLite; MySQL fields above are ignored when using SQLite
        'sqlite_path'    => env('DB_SQLITE_PATH', ''),
    ],

    'Neo4jCredentials' => [
        'uri'      => env('NEO4J_URI', 'bolt://localhost:7687'),
        'username' => env('NEO4J_USERNAME', 'neo4j'),
        'password' => env('NEO4J_PASSWORD', ''),
    ],
];

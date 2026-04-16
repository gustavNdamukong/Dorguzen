<?php

/*
|--------------------------------------------------------------------------
| DGZ Log Channels
|--------------------------------------------------------------------------
|
| Each channel is an independently-configured logging destination.
| Every channel supports the following keys:
|
|   driver    — where to write: 'file' | 'db' | 'both'
|   format    — line format:    'text' | 'json'
|   path      — directory for log files, relative to the project root
|   min_level — minimum severity to record: 'debug' | 'info' | 'notice'
|               | 'warning' | 'error' | 'critical'
|
| Usage anywhere in your application:
|
|   DGZ_Logger::channel('payments')->warning("Charge failed", ['amount' => 100]);
|   DGZ_Logger::channel('security')->critical("Brute force attempt", ['ip' => $ip]);
|
| The 'default' channel is used internally by all existing DGZ_Logger::error()
| etc. calls and mirrors the APP_LOG_DRIVER / APP_LOG_FORMAT settings from
| your .env file, so existing behaviour is completely unchanged.
|
*/

return [

    'channels' => [

        'default' => [
            'driver'          => env('APP_LOG_DRIVER', 'db'),
            'format'          => env('APP_LOG_FORMAT', 'text'),
            'path'            => 'storage/logs',
            'min_level'       => 'debug',
            'filename_prefix' => 'dgz',   // keeps existing dgz-YYYY-MM-DD.log naming
        ],

        'payments' => [
            'driver'    => 'file',
            'format'    => 'json',
            'path'      => 'storage/logs',
            'min_level' => 'warning',
        ],

        'security' => [
            'driver'    => 'both',
            'format'    => 'json',
            'path'      => 'storage/logs',
            'min_level' => 'error',
        ],

    ],

];

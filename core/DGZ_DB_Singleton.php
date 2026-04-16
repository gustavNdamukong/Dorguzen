<?php

namespace Dorguzen\Core;

use Exception;
use Dorguzen\Core\Database\Drivers\DGZ_PDODriver;
use Dorguzen\Core\Database\Drivers\DGZ_MySQLiDriver;
use Dorguzen\Core\Database\Drivers\DGZ_SQLiteDriver;
use Dorguzen\Core\Database\Drivers\DGZ_PostgresDriver;


class DGZ_DB_Singleton
{

        /**
         * Our single database client instance.
         */
        private static ?DGZ_DBAdapter $instance = null;

        /**
         * Disable instantiation.
         */
        private function __construct()
        {
                // Private to disable instantiation.
        }


        final public function __clone() { throw new Exception('Cloning disabled'); }
        final public function __wakeup() { throw new Exception('Wakeup disabled'); }


        public static function getInstance(): DGZ_DBAdapter
        {
                if (!is_null(static::$instance)) {
                        return static::$instance;
                }
                
                $config = config('database');

                $credentials = $config['DBcredentials'];

                switch ($credentials['connectionType']) {
                case 'mysqli':
                        $driver = new DGZ_MySQLiDriver($credentials);
                        break;
                case 'pdo':
                        $driver = new DGZ_PDODriver($credentials);
                        break;
                case 'sqlite':
                        $driver = new DGZ_SQLiteDriver($credentials);
                        break;
                case 'pgsql':
                        $driver = new DGZ_PostgresDriver($credentials);
                        break;
                default:
                        throw new Exception('Unsupported DB connection type: ' . $credentials['connectionType']);
                }

                static::$instance = new DGZ_DBAdapter($driver);
                return static::$instance;
        }
}
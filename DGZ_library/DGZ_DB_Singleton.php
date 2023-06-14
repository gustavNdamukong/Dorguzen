<?php

namespace DGZ_library;

use configs\Config;
use mysqli;
use Exception;
use PDO;
use PDOException;


class DGZ_DB_Singleton
{

        /**
         * Our single database client instance.
         *
         * @var Database
         */
        private static $instance;

        /**
         * Disable instantiation.
         */
        private function __construct()
        {
                // Private to disable instantiation.
        }

        /**
         * Create or retrieve the instance of our database client.
         *
         * @return Database
         */
        public static function getInstance()
        {
                $configClass = new Config();

                //get DB connection credentials
                if ($credentials = $configClass->getConfig()['live'] == false) {
                        $credentials = $configClass->getConfig()['localDBcredentials'];
                }
                elseif ($configClass->getConfig()['live'] == true)
                {
                        $credentials = $configClass->getConfig()['liveDBcredentials'];
                }

                //get the connection driver (connection type)
                if ($credentials['connectionType']  == 'mysqli') {

                        if (is_null(static::$instance)) {
                                static::$instance = new mysqli($credentials['host'], $credentials['username'], $credentials['pwd'], $credentials['db']);
                        }

                        return static::$instance;
                }
                elseif ($credentials['connectionType'] == 'pdo')
                {
                        try {
                                if (is_null(static::$instance)) {
                                        static::$instance = new PDO("mysql:host=$credentials[host];dbname=$credentials[db]", $credentials['username'], $credentials['pwd']);
                                }
                        }
                        catch (PDOException $e) {
                                echo 'Cannot connect to database';
                                exit;
                        }

                        return static::$instance;
                }
        }

        /**
         * Disable the cloning of this class.
         *
         * @return void
         * @throws Exception
         */
        final public function __clone()
        {
                throw new Exception('Feature disabled.');
        }




        /**
         * Disable the wakeup of this class.
         *
         * @return void
         * @throws Exception
         */
        final public function __wakeup()
        {
                throw new Exception('Feature disabled.');
        }
}
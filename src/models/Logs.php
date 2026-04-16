<?php

namespace Dorguzen\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

    /** ############## Properties and Methods all model classes must have to get the full power of the Dorguzen ###############
     * Must extend the parent model DGZ_Model

    ##### PROPERTIES ######################
     * protected array $_columns = array();
     * protected array $data = array();
     * protected array $_hasParent = array();
     * protected array $_hasChild = array();
     
     ## OPTIONAL FIELDS ##
     * protected string $id      (if its table's pk field is something other than 'id' or 'tableName_id')
     * protected string $table (if not lcfirst(tableName))

     * ##### CONSTRUCTOR ######################
     * Must call the parent constructor

     * ##### METHODS ######################
     * It has access to all its patent's methods, and you can add yours
     *
     */



    /**
     * Logs
     */
    class Logs extends DGZ_Model
    {
        //make the visibility of this field protected, not private; else the parent class would not be able to write to it.
        protected $_columns = array();

        protected $data = [];


        /**
         * This property takes an array of strings which are names of models whose foreign keys are being used in this table (model)
         *
         * This is important as it is used to handle what is known as 'foreign key constraints'. You should come back and add any tables (models) whose primary keys are used by
         * any record in this model. It's default value is an empty array.
         *
         * @var array
         */
        protected $_hasParent = [];


        /**
         * This property takes an assoc array of strings which are names of models that have foreign keys of this table (model)
         *
         * This is important as it is used to handle what is known as 'foreign key constraints'. You should come back and add any tables (models) that use (have the foreign key)
         * of any record in this model. The keys are the names of the child models, while he values are the names of the foreign key fields in the child table.
         * The default value of this array is an empty array.
         *
         * @var array
         */
        protected $_hasChild = [];




        public function __construct(Config $config)
        {
            parent::__construct($config);
        }


        /**
         * @param string $title
         * @param string $message
         * @param array $context
         * @return boolean
         */
        public function log(string $title, string $message, array $context = [])
        {
            $contextJson = !empty($context) ? json_encode($context) : null;

            $this->logs_title = $title;
            $this->logs_message = $message;
            $this->context_json = $contextJson;
            $this->logs_created = date("Y-m-d H:i:s");
            $saved = $this->save();

            if ($saved)
            {
                return true;
            }
            else
            {
                return false;
            }
        }


        public function getAll($orderBy = '')
        {
            $orderby = $orderBy != ''?' ORDER BY '.$orderBy:'';
            $query = "SELECT * FROM ".$this->getTable().$orderby;

            $result = $this->query($query);

            return $result;
        }


        /**
         * @param string $orderBy
         * @return array|bool
         */
        public function getRunTimeErrors($orderBy = '')
        {
            $orderby = $orderBy != ''?' ORDER BY '.$orderBy:'';
            $query = "SELECT * FROM ".$this->getTable()." WHERE `logs_title` LIKE '%Runtime error%'".$orderby;
            $result = $this->query($query);

            return $result;
        }


        /**
         * @param string $orderBy
         * @return array|bool
         */
        public function getAdminLoginData($orderBy = '')
        {
            $orderby = $orderBy != ''?' ORDER BY '.$orderBy:'';
            $query = "SELECT * FROM ".$this->getTable()." WHERE `logs_title` LIKE '%Admin login%'".$orderby;

            $result = $this->query($query);
            return $result;
        }
    }
    
    

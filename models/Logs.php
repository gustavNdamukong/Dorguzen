<?php


use DGZ_library\DGZ_Model;

    /** ############## Properties and Methods all model classes must have to get the full power of the Dorguzen ###############
     * Must extend the parent model DGZ_Model

     * ##### PROPERTIES ######################
     * protected $_columns = array();
     * protected $data = array(); 
     * private $_hasParent = array();
     * private $_hasChild = array();

     * ##### CONSTRUCTOR ######################
     * Must call the parent constructor
     * Must call loadORM(), which queries its table, then loops through the results and populates its _columns member array

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
        private $_hasParent = [];


        /**
         * This property takes an assoc array of strings which are names of models that have foreign keys of this table (model)
         *
         * This is important as it is used to handle what is known as 'foreign key constraints'. You should come back and add any tables (models) that use (have the foreign key)
         * of any record in this model. The keys are the names of the child models, while he values are the names of the foreign key fields in the child table.
         * The default value of this array is an empty array.
         *
         * @var array
         */
        private $_hasChild = [];




        public function __construct()
        {
            //Parent constructors wont run auto, u have to explicitly call it, in this case to load our DB connection settings
            parent::__construct();

            //build the map of the table columns and datatypes. Note we have created before hand a private member called '_columns' wh will hold column names n datatypes
            //only your model class will write to n read from this member
            $columns = $this->loadORM($this);

        }




        /**
         * Update the view counter for the view visited
         *
         * @param $title
         * @param $message
         * @return boolean
         */
        public function log($title, $message)
        {
            $this->logs_title = $title;
            $this->logs_message = $message;
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
    
    

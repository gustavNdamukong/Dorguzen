<?php


    /** ############## Properties and Methods all model classes must have to get the full power of the Dorguzen ###############
     * Must extend the parent model DGZ_DB_ADAPTER

    ##### PROPERTIES ######################
     * protected $_columns = array();
     * private $_hasParent = array();
     * private $_hasChild = array();

    ##### CONSTRUCTOR ######################
     * Must call the parent constructor
     * Must call loadORM(), which queries its table, then loops through the results and populates its _columns member array

    ##### METHODS ######################
     * It has access to all its patent's methods, and you can add yours
     *
     */



    class ContactFormMessage extends DGZ_DB_Adapter
    {

        //make the visibility of this field protected, not private; else the parent class would not be able to write to it.
        protected $_columns = array();


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
         * THIS IS NOT YET IN USE
         *
         * @param $id
         * @return string
         */
        public function deleteContacFormMessage($id)
        {  
            $connect = $this->_dbadapter->getConnected();
            $sql = "DELETE FROM contactFormMessage WHERE id = $id";
            $deleted = $connect->query($sql);
            return $deleted;
        }
          
        
    }
    
    

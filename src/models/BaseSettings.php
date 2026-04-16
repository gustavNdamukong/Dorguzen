<?php

namespace Dorguzen\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;


    /** ### Properties and Methods all model classes must have to get the full power of the Dorguzen ####
     * Must extend the parent model DGZ_DB_ADAPTER
     *
     * ##### PROPERTIES ######################
     * protected array $_columns = array();
     * protected array $data = array();
     * protected array $_hasParent = array();
     * protected array $_hasChild = array();
     *
     * ## OPTIONAL FIELDS ##
     * protected string $id      (if its table's pk field is something other than 'id' or 'tableName_id')
     * protected string $table   (if not lcfirst(tableName))
     *
     * ##### CONSTRUCTOR ######################
     * Must call the parent constructor
     *
     *##### METHODS ######################
     * It has access to all its patent's methods, and you can add yours
     *
     *
     */
    class BaseSettings extends DGZ_Model
    {

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
         * of any record in this model. The keys are the names of the child models, while the values are the names of the foreign key fields in the child table.
         * The default value of this array is an empty array.
         *
         * @var array
         */
        protected $_hasChild = [];





        public function __construct(Config $config)
        {
            parent::__construct($config);
        }

        
    }
    
    

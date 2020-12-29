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



    /**
     * Class Users
     */
    class Users extends DGZ_DB_Adapter
    {
        protected $_columns = array();


        public function __construct()
        {
            parent::__construct();

            //build the map of the table columns and datatypes. Note we have created before hand a private member called '_columns' wh will hold column names n datatypes
            //only your model class will write to n read from this member
            $columns = $this->loadORM($this);
        }





        public function authenticateUser($email, $password)
        {
            $connect = $this->connect();

            $salt = $this->getSalt();

            $dataTypes = '';
            $getdataTypes = $this->getColumnDataTypes();
            foreach ($getdataTypes as $dataTypeKey => $getDataType)
            {
                if ($dataTypeKey == 'users_email')
                {
                    $dataTypes .= $getDataType;
                }
                if ($dataTypeKey == 'users_pass')
                {
                    $dataTypes .= $getDataType;
                }
            }

            //add one for the password 'key' value which must be represented but does not exist as a column in the users table
            $dataTypes .= 's';
            //die($dataTypes);/////

            $sql = "SELECT * FROM ".$this->getTable()." WHERE users_email = ? AND users_pass = AES_ENCRYPT(?, ?)";
            //die($sql);/////

            $stmt = $connect->stmt_init();
            $stmt->prepare($sql);

            // bind parameters and insert the details into the database
            $stmt->bind_param($dataTypes, $email, $password, $salt); //apart from the datatype strings, the number of variables MUST match the number of xters in the prepared statement placeholders
            $stmt->bind_result($custo_id, $type, $email, $pass, $first_name, $last_name, $updated, $created); //These MUST match the exact number of items returned from the table by your SQL
            $stmt->execute();
            $stmt->store_result();

            $stmt->fetch(); //without fetching the result like this, forget it, the values wont come thru; it like getting your wallet, but then 			      				
                                            //trying to get the money without putting ur hand inside it. 

            if ($stmt->num_rows ) 
            {
                if (!session_id()) { session_start(); } // You should start the session only just bf u start assigning the session variables.
                $_SESSION['authenticated'] = 'Let Go'; //this is the secret keyword (token) u'll check to confirm that a user is logged in.
                //get the time the session started
                $_SESSION['start'] = time();
                session_regenerate_id();


                // This is when you grant them access and let them go by redirecting them to the right quarters of the site
                //store the session variables to be used further on your site if the session variable has been set, then redirect
                if (isset($_SESSION['authenticated']))
                {
                    $_SESSION['custo_id'] = $custo_id;
                    $_SESSION['user_type'] = $type;
                    $_SESSION['email'] = $email;
                    $_SESSION['pass'] = $pass;
                    $_SESSION['first_name'] = $first_name;
                    $_SESSION['last_name'] = $last_name;
                    $_SESSION['created'] = $created;

                    session_write_close();
                }

               return true;
            }
            else
            {
                return false;
            }
        }
        











        
        public function recoverLostPw($email)
        {

            $connect = $this->connect();
            
            $salt = $this->getSalt();

            $dataTypes = '';
            $getdataTypes = $this->getColumnDataTypes();

            foreach ($getdataTypes as $dataTypeKey => $getDataType)
            {
                //check here for as many fields as you intend to select from the users table in order to build the data types
                // for the prepared statements of the SQL query. Note, u only need to worry about the ones your SQL uses
                // '?' placeholders for
                if ($dataTypeKey == 'users_email')
                {
                    $dataTypes .= $getDataType;
                }
            }

            //add one 's' (string) to represent the password 'key' (salt) which must be represented in the SQL prepared statement
            //but does not exist as a column in the users table
            /////$dataTypes .= 's';


            $sql = "SELECT users_id, users_email, AES_DECRYPT(users_pass, '$salt') AS pass, users_first_name FROM users 
            WHERE users_email = ?";

            $stmt = $connect->stmt_init();
            $stmt->prepare($sql);
            // bind parameters to the placeholder xters (?) and query the database
            //the number of variables bound to the query MUST ONLY exactly match the number of prepared statement placeholder xters (?)
            $stmt->bind_param($dataTypes, $email);

            //when binding results, specifically make sure that the value bound on the placeholder (using bind_result()) is given a diff variable name,
            // from the name of the DB table field, else it will override the val coming from the DB n mess up the results-e.g when selecting an email
            // field (table column) put e.g. $emailo in bind_result(), and not $email
            //the number of variables u bind to the result here MUST match the number of fields you are selecting from the DB or u will get errors
            $stmt->bind_result($users_id, $emailo, $pass, $firstname);
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();

            if ($stmt->num_rows ) 
            { 
                //die($emailo.$pass.$firstname);
                $result = array();
                $result['userId'] = $users_id;
                $result['email'] = $emailo;
                $result['pass'] = $pass;
                $result['firstname'] = $firstname;
                
               return $result;
            }
            else
            {
               return false;
            }
             
           
        }









        public function getUserById($userId)
        {
            $key = $this->getSalt();

            $table = $this->getTable();


            $sql = "SELECT users_id, users_type, users_email, AES_DECRYPT(users_pass, '$key') AS pass, users_first_name, users_last_name, users_created FROM ".$table." WHERE users_id = ".$userId;
            $users = $this->query($sql);

            if ($users)
            {
                return $users;
            }

        }



        
        
        
        public function createUser($firstname, $lastname, $email, $password)
        {
            $data = [
                'users_type' => 'admin',
                'users_first_name' => $firstname,
                'users_last_name' => $lastname,
                'users_email' => $email,
                'users_pass' => $password,
                'users_created' => ''
            ];

            $saved = $this->insert($data);

            if ($saved == 1062)
            {
                return 1062;
            }
            elseif ($saved == true)
            {
                return true;
            }
            else
            {
                return false;
            }
        }






        /**
         * when a user requests to reset their password, they are are sent a link to a form that eventually gets submitted here
         * their user ID and email address will have to match before the reset can happen.
         *
         * @param $reset_user_id
         * @param $reset_email
         * @param $reset_pwd
         */
        public function resetUserPassword($reset_user_id, $reset_email, $new_pwd)
        {
            $this->users_pass = $new_pwd;

            $where = ['users_id' => $reset_user_id, 'users_email' => $reset_email];
            $updated = $this->updateObject($where);

            if ($updated) {
                return true;
            }
            else {
                return false;
            }
        }




        
    }
    
    

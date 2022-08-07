<?php


use DGZ_library\DGZ_Model;

    /** ############## Properties and Methods all model classes must have to get the full power of the Dorguzen ###############
     * Must extend the parent model DGZ_Model

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
    class Users extends DGZ_Model
    {
        protected $_columns = array();


        public function __construct()
        {
            parent::__construct();

            $columns = $this->loadORM($this);
        }




        
        public function recoverLostPw($email)
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
            }

            $sql = "SELECT users_id, users_email, AES_DECRYPT(users_pass, '$salt') AS pass, users_first_name FROM users 
            WHERE users_email = ?";

            $stmt = $connect->stmt_init();
            $stmt->prepare($sql);
            $stmt->bind_param($dataTypes, $email);
            $stmt->bind_result($users_id, $emailo, $pass, $firstname);
            $stmt->execute();
            $stmt->store_result();
            $stmt->fetch();

            if ($stmt->num_rows ) 
            {
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
         * When a user requests to reset their password, they are are sent a link to a form that eventually gets submitted here
         * their user ID and email address will have to match before the reset can happen.
         *
         * @param $reset_user_id
         * @param $reset_email
         * @param $new_pwd
         * @return Bool
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
    
    

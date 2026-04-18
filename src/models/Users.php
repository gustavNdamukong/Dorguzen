<?php

namespace Dorguzen\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

    /** 
     * ### Properties and Methods all model classes must have to get the full power of the Dorguzen ####
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
     * Class Users
     */
    class Users extends DGZ_Model
    {
        protected $_columns = array();

        protected $data = [];

        protected $id = 'users_id';


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


        public function authenticateUser(array $data): array|false
        {
            $result = parent::authenticateUser($data);
            if (!$result || ($result['users_emailverified'] ?? '') !== 'yes') {
                return false;
            }
            return $result;
        }


        public function recoverLostPw($email)
        {
            $salt = $this->getSalt();

            $sql = "SELECT users_id, users_email, AES_DECRYPT(users_pass, '$salt') AS users_pass, users_first_name AS users_firstname
                    FROM users
                    WHERE users_email = ?";

            $rows = $this->connect()->query($sql, [$email]);

            return $rows[0] ?? false;
        }


        public function getUserById($userId)
        {
            $key = $this->getSalt();

            $table = $this->getTable();


            $sql = "SELECT users_id, users_type,
                users_email, users_phone_number,
                AES_DECRYPT(users_pass, '$key') AS pass,
                users_first_name, users_last_name,
                users_created FROM ".$table."
                WHERE users_id = ".$userId;
            $users = $this->query($sql);

            if ($users)
            {
                return $users;
            }

        }


        public function getUsernameFromId($userId)
        {
            $query = "SELECT users_email FROM users
                        WHERE users_id = ?";
            $result = $this->query($query, [$userId]);
            if ($result) {
                return $result[0]['users_email'];
            }
            else
            {
                return false;
            }
        }


        public function getUserEmailById($userId)
        {
            $query = "SELECT users_email FROM users
                        WHERE users_id = $userId";
            $result = $this->query($query);
            return $result[0]['users_email'];
        }


        public function createUser($user_type, $firstname, $lastname, $email, $phone_number, $password, $created)
        {
            $data = [
                'users_type' => $user_type,
                'users_first_name' => $firstname,
                'users_last_name' => $lastname,
                'users_email' => $email,
                'users_phone_number' => $phone_number,
                'users_pass' => $password,
                'users_created' => $created
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
         * We use this function when users send messages to a seller where the seller is from Camerooncom.
         * We actually send the message to the seller's inbox who is obviously an admin person, but then we also
         * send a copy of the same message to our (Camerooncom) admin_gen user, or the super_admin user if there's
         * admin_gen, for follow-up purposes.
         */
        public function getAdminGenId()
        {
            $query = "SELECT users_id FROM users
                        WHERE users_type = ?";
            $result = $this->query($query, ['admin_gen']);
            if (empty($result[0]))
            {
                $query = "SELECT users_id FROM users
                        WHERE users_type = ?";
                $result = $this->query($query, ['super_admin']);
            }

            return $result[0]['users_id'];
        }


        public function getSuperAdminId()
        {
            $query = "SELECT users_id FROM users
                    WHERE users_type = 'super_admin'";
            $result = $this->query($query);

            return $result[0]['users_id'];
        }



        /**
         * Get the IDs of all admin users
         * @return mixed
         */
        public function getAdminIds()
        {
            $ids = [];
            $query = "SELECT users_id FROM users
                        WHERE users_type IN ('admin', 'admin_gen', 'super_admin')";
            $result = $this->query($query);
            if ($result)
            {
                foreach ($result as $res)
                {
                    $ids[] = $res['users_id'];
                }
            }
            return $ids;
        }






        
        /**
         * Check if the given user is an admin user or not
         *
         * @param $userId
         * @return boolean
         */
        public function isAdmin($userId)
        {
            // first ensure $userId is not empty, because they 
            // may not even be logged in
            if ($userId != null && $userId != "")
            {
                $query = "SELECT users_type 
                        FROM users
                        WHERE users_id = $userId";

                $result = $this->query($query);

                if ($result) {

                    if (
                        ($result[0]['users_type'] == 'admin') || 
                        ($result[0]['users_type'] == 'admin_gen') || 
                        ($result[0]['users_type'] == 'super_admin')
                    ) 
                    {
                        return true;
                    }
                    else {
                        return false;
                    }
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return false;
            }
        }



        /**
         * Check if the given user is at least an admin_gen or super_admin user
         *
         * @param $userId
         * @return boolean
         */
        public function isAdminGenOrHigher($userId)
        {
            $query = "SELECT users_type 
                    FROM users
                    WHERE users_id = $userId";

            $result = $this->query($query);

            if ($result) {
                if (($result[0]['users_type'] == 'admin_gen') ||
                    ($result[0]['users_type'] == 'super_admin')) {
                    return true;
                }
                else {
                    return false;
                }
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
         * @return bool
         */
        public function resetUserPassword($reset_user_id, $reset_email, $new_pwd)
        {
            $this->users_pass          = $new_pwd;
            $this->users_emailverified = 'yes';

            $where = ['users_id' => $reset_user_id, 'users_email' => $reset_email];
            $updated = $this->update($where);

            if ($updated) {
                return true;
            }
            else {
                return false;
            }
        }
    }
    
    

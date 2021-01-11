<?php

namespace DGZ_library;


/**
 *
 * @author Gustav Ndamukong
 */

use settings\Settings;
use mysqli;


/**
 * This class is the parent model extended by all models. It is responsible for
 * -1) establishing a connection to the DB, and
 * -2) orchestrating DB connections that don't directly relate to models
 * -3) implementing an ORM that enables models to write to and update their fields dynamically
 *  ...and much more
 */
 class DGZ_DB_Adapter 
 {
     protected $settings;


     protected $host = '';


     protected $username = '';


     protected $pwd = '';

     
     protected $db = '';
     
     
     protected $salt = '';
     
     
     protected $connectionType = '';


     protected $whoCalledMe = '';


     protected $model = '';



    public function __construct()
    {
        $classThatCalled = get_class($this);
        $this->whoCalledMe = $classThatCalled;

        $settingsClass = new Settings();

        $this->settings = $settingsClass;

        //get DB connection credentials
        if ($credentials = $this->settings->getSettings()['live'] == false) {
            $credentials = $this->settings->getSettings()['localDBcredentials'];
        }
        elseif ($this->settings->getSettings()['live'] == true)
        {
            $credentials = $this->settings->getSettings()['liveDBcredentials'];
        }

        $this->username = $credentials['username'];
        $this->pwd = $credentials['pwd'];
        $this->db = $credentials['db'];
        $this->host = $credentials['host'];
        $this->connectionType = $credentials['connectionType'];
        $this->salt = $credentials['key'];

    }





    protected function connect()
    {

        if ($this->connectionType  == 'mysqli')
        {
            $conn = new mysqli($this->host, $this->username, $this->pwd, $this->db);

            if ($conn->connect_error)
            {
                die('cannot open database');
            }


            return $conn;
        }
        elseif ($this->connectionType  == 'pdo')
        {
            try
            {
                return new PDO("mysql:host=$this->host;dbname=$this->db", $this->username, $this->pwd);
            }
            catch (PDOException $e)
            {
                echo 'Cannot connect to database';
                exit;
            }
        }
    }




     public function getSalt()
     {
         $salt = (string) $this->salt;

         return $salt;
     }



    /**
     * This method is called ONLY by models at load time to map to their tables n initialize
     * vital settings
     *
     * This weirdly only seems to work when you store the $db-> values in a var (e.g. $result below)
     */
     public function loadORM($model)
     {
         $table = $this->getTable();
         $db = $this->connect();
         $query = 'DESCRIBE '.strtolower($table);

         $result = $db->query($query);

         if ((isset($result->num_rows)) && ($result->num_rows > 0))
         {
             $results = array();
             while ($row = $result->fetch_assoc())
             {
                 $results[] = $row;
             }



             $columns = $results;


             if (is_array($columns)) {
                 foreach ($columns as $column) {
                     if (preg_match('/int/', $column['Type'])) {
                         $val = 'i';
                     }
                     if (preg_match('/varchar/', $column['Type'])) {
                         $val = 's';
                     }
                     if (preg_match('/text/', $column['Type'])) {
                         $val = 's';
                     }
                     if (preg_match('/timestamp/', $column['Type'])) {
                         $val = 's';
                     }
                     if (preg_match('/enum/', $column['Type'])) {
                         $val = 's';
                     }
                     if (preg_match('/blob/', $column['Type'])) {
                         $val = 's';
                     }
                     if (preg_match('/decimal/', $column['Type'])) {
                         $val = 'd';
                     }
                     if (preg_match('/date/', $column['Type'])) {
                         $val = 's';
                     }
                     if (preg_match('/float/', $column['Type'])) {
                         $val = 'd';
                     }

                     $model->_columns[$column['Field']] = $val;
                 }
             }
         }
         else {
             if ((isset($result->affected_rows)) && ($result->affected_rows > 0)) {
                 return true;
             }
         }
     }








        public function __set($member, $value)
        {
            if (array_key_exists($member, $this->_columns)) {
                $this->$member = $value;
            }
        }








        /**
         * This member being retrieved must have been created already using __set() above
         */
     public function __get($member)
     {
         if (array_key_exists($member, $this->_columns)) {
             return $this->$member;
         }
     }








     public function getColumnDataTypes()
     {
         return $this->_columns;
     }






     /**
      * Returns the name of this model beginning in lowercase (first letter).
      * According to Dorguzen convention, you should name your models after the DB tables they represent and the DB tables should
      * be in lowercase-at least the first letter, while the model being a class should begin with an uppercase.
      * This is to the effect that when you see a model, you should assume its DB table is of the same name beginning in lowercase.
      *
      * @return string
      */
     public function getTable()
     {
         return lcfirst(get_class($this));
     }

     



     public function save()
     {
         $model = new $this->whoCalledMe;
         $table = $model->getTable();

         $data = array();
         $datatypes = array();

         foreach (get_object_vars($this) as $property => $value) {
             if (array_key_exists($property, $model->_columns)) {
                 $data[$property] = $value;

                 array_push($datatypes, $model->_columns[$property]);
             }

         }

         $datatypes = implode($datatypes);

         // Connect to the database
         $db = $this->connect();
         $key = $this->getSalt();

         list( $fields, $placeholders, $values ) = $this->insert_update_prep_query($data);

         array_unshift($values, $datatypes);

         $stmt = $db->stmt_init();

         $stmt->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})");

         call_user_func_array( array( $stmt, 'bind_param'), $this->ref_values($values));

         $stmt->execute();

         if ( $stmt->affected_rows == 1)
         {
             return $stmt->insert_id;
         }
         elseif ( (isset($stmt->errno)) && ($stmt->errno == 1062))
         {
             return 'duplicate';
         }
         else
         {
             return false;
         }
     }






     /**
      * Instead of preparing all the data needed to passed to the update()
      * method ($table, $data, $datatypes, $whereClause), updateObject just takes
      * a 'where' clause array of 'fieldName' => 'value' pairs and does all the rest for you.
      *
      * When it comes to the 'users_pass' (spelled exactly like that), we need 2 entries each for the data and datatypes arrays
      *     -data)  password value, and key value (salt string of the pw)
      *     -datatypes) 'i' for the password, and 's' for the salt string
      *
      * @param $where
      * @return bool|string
      */
     public function updateObject($where)
     {
         $model = new $this->whoCalledMe;
         $table = $model->getTable();

         $data = array();
         $dataTypes = array();


         foreach (get_object_vars($this) as $property => $value) {
             if (array_key_exists($property, $model->_columns)) {
                 if ($property == 'users_pass')
                 {
                     $key = $this->getSalt();
                     $data[$property] = $value;
                     $data['key'] = $key;

                     array_push($dataTypes, $model->_columns[$property]);
                     array_push($dataTypes, 's');
                 }
                 else {
                     $data[$property] = $value;
                     array_push($dataTypes, $model->_columns[$property]);
                 }
             }

         }

         foreach ($where as $field => $val)
         {
             if (array_key_exists($field, $model->_columns)) {
                 array_push($dataTypes, $model->_columns[$field]);
             }
         }

         $datatypes = implode($dataTypes);

         $db = $this->connect();

         list( $fields, $placeholders, $values ) = $this->insert_update_prep_query($data, 'update');
         $where_clause = '';
         $where_values = [];
         $count = 0;

         foreach ( $where as $field => $value )
         {
             if ( $count > 0 ) {
                 $where_clause .= ' AND ';
             }

             $where_clause .= $field . '=?';
             $where_values[] = $value;

             $count++;
         }

         array_unshift($values, $datatypes);
         $values = array_merge($values, $where_values);

         $stmt = $db->prepare("UPDATE {$table} SET {$placeholders} WHERE {$where_clause}");

         call_user_func_array( array( $stmt, 'bind_param'), $this->ref_values($values));

         $stmt->execute();

         if ( $stmt->affected_rows ) {
             return true;
         }

         return false;
     }








     /**
      * delete based on any criteria desired
      *
      * this method prepares the args ($table, $where criteria, and $dataTypes) before passing these args to delete()
      *
      * @param array $criteria the criteria to delete records based on. For example, if we are deleting an album, $criteria will contain
      *   something like ['albums_name' => 'Birthday']
      *
      * @return string
      */
     public function deleteWhere($criteria = array())
     {
         if ((isset($this->_hasChild)) && (!empty($this->_hasChild))) {

             foreach ($criteria as $key => $crits) {
                 if (!array_key_exists($key, $this->_columns)) {
                     return 'The field ' . $key . ' does not exist in the ' . strtolower($this->getTable() . ' table');
                 }
                 else
                 {
                     $recId = $crits;

                     foreach ($this->_hasChild as $childname => $childFkField)
                     {
                         $datatypes = '';
                         $where = array();

                         $childModel = new $childname;

                         $childDataTypes = $childModel->getColumnDataTypes();

                         if (!array_key_exists($childFkField, $childDataTypes)) {
                             return 'The field ' . $childFkField . ' does not exist in the ' . strtolower($childModel->getTable() . ' table');
                         }

                         $where[$childFkField] = $recId;
                         $datatypes .= $childDataTypes[$childFkField];

                         $babyTable = $childModel->getTable();

                         $result = $this->delete($babyTable, $where, $datatypes);

                     }
                 }
             }
         }


         foreach ($criteria as $key => $crits)
         {
             $datatypes = '';
             $where = array();
             if (!array_key_exists($key, $this->_columns)) {
                 return 'The field ' . $key . ' does not exist in the ' . strtolower($this->getTable() . ' table');
             }
             else {
                 $where[$key] = $crits;
                 $datatypes .= $this->_columns[$key];
             }
         }

         $table = $this->getTable();

         $result = $this->delete($table, $where, $datatypes);

         return true;
     }







     /**
      *query DB without a prepared stmt
      *
      * @param $query just pass it your SQL query string
      * @return it returns true if you are updating of deleting, it returns the last inserted ID if you are inserting, it returns the result set
      *              if you are selecting, it returns false if the operation fails.
     */
        public function query($query)
        {
            $db = $this->connect();

            $res = $db->query($query);

            if ((isset($res->num_rows)) && ($res->num_rows > 0))
            {
                $results = array();
                while ($row = $res->fetch_assoc())
                {
                    $results[] = $row;
                }

                return $results;
            }

            if ((isset($db->affected_rows)) && ($db->affected_rows > 0))
            {

                if ((isset($db->insert_id)) && ($db->insert_id != 0)) {
                    return $db->insert_id;
                }
                else
                {
                    return true;
                }
            }

            return false;
        }








     /**
      *
      * This meth does select all (SELECT *) or only SELECTS a given set of columns
      * If no $columns are specified, none of the other params will have anything, so its does a SELECT all
      * If $columns are specified, then $where must also be specified
      * $columns is given the names of columns u wanna grab, the model will check that they exist in the table
      * $where is given the column names you're matching on as keys, & the column values required as the values.The model also checks if these keys
      * exist in the table.
      *
      *
      * @param string $columns of fields to grab
      * @param array $criteria is an assoc array of 'where key (column name) => value' sort o thing
      *
      * @return array of results
      */
     public function selectWhere($columns = array(), $criteria = array())
     {
         $model = new $this->whoCalledMe;
         $fields_to_select = array();
         $datatypes = '';
         $criterion = array();
         if (!empty($columns)) {
             foreach ($columns as $column) {
                 if (!array_key_exists($column, $model->getColumnDatatypes())) {
                     return 'The field ' . $column . ' does not exist in the ' . strtolower($model->getTable() . ' table');
                 }
                 else {
                     $fields_to_select[] = $column;
                 }
             }

             foreach ($criteria as $key => $crits)
             {
                 if (!array_key_exists($key, $model->getColumnDatatypes())) {
                     return 'The field ' . $key . ' does not exist in the ' . strtolower($model->getTable() . ' table');
                 }
                 else {
                     $criterion[$key] = $crits;
                     $datatypes .= $model->getColumnDatatypes()[$key];
                 }
             }
         }

         $table = strtolower($model->getTable());
         $columns_needed = $fields_to_select;
         $where = $criterion;
         $db = $this->connect();

         if (empty($columns)) {
             $sql = "SELECT * FROM $table";


             $result = $this->query($sql);
             if (is_array($result)) {
                 return $result;
             }
             else {
                 return false;
             }

         }
         elseif (!empty($columns)) {
             $columns = (array)$columns;
             $where = (array)$where;

             $where_placeholders = '';
             $where_values = [];
             $count = 0;

             foreach ($where as $field => $value) {
                 if ($count > 0) {
                     $where_placeholders .= ' AND ';
                 }

                 $where_placeholders .= $field . '=?';
                 $where_values[] = $value;

                 $count++;
             }

             array_unshift($where_values, $datatypes);

             $columns_as_string = implode(',', $columns);
             $stmt = $db->prepare("SELECT {$columns_as_string} FROM {$table} WHERE {$where_placeholders}");

             call_user_func_array(array($stmt, 'bind_param'), $this->ref_values($where_values));
             $stmt->execute();

             $stmt->store_result();

             if ($stmt->num_rows) {
                 $results_basket = [];

                 while ($row = $this->fetchAssocStatement($stmt)) {
                     $results_basket[] = $row;
                 }

                 $stmt->close();

                 return $results_basket;
             }
             else {
                 return false;
             }
         }
     }







     /**
      * Call this function like so:
      *
      *      $blog2cat = new Article2cat();
      *
      *      $blogPost = [
      *          'blog_id' => $_POST['blog_id'],
      *          'blog_cats_id' => $cat_id,
      *      ];
      *      $blog2cat->insert($blogPost);
      *
      * @param $data
      * @return bool|int|string
      */
     public function insert($data)
     {
         $model = new $this->whoCalledMe;
         $table = $model->getTable();

         $db = $this->connect();
         $key = $this->getSalt();

         $data = (array) $data;

         $dataTypes = '';
         $usersDataClues = $model->getColumnDataTypes();

         foreach ($data as $dataKey => $dat) {
             foreach ($usersDataClues as $dataClueKey => $columnDatClue) {
                 if ($dataClueKey == $dataKey) {
                     $dataTypes .= $columnDatClue;
                     if ($dataKey == 'users_pass')
                     {
                         $data['key'] = $key;
                     }
                 }
             }
         }

         list( $fields, $placeholders, $values ) = $this->insert_update_prep_query($data);

         array_unshift($values, $dataTypes);


         $stmt = $db->stmt_init();

         $stmt->prepare("INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})");

         call_user_func_array( array( $stmt, 'bind_param'), $this->ref_values($values));

         $stmt->execute();

         if ( $stmt->affected_rows == 1)
         {
             return $stmt->insert_id;
         }
         elseif ( (isset($stmt->errno)) && ($stmt->errno == 1062))
         {
             return '1062';
         }
         else
         {
             return false;
         }
     }









     /**
      * Update a record in the DB
      *
      * Prepare to call it like so:
      * $data = ['blog_title' => $_POST['title'],
      *     'blog_article' => $_POST['article'],
      *  ];
      *
      * $where = ['blog_id' => $blog_id];
        $updated = $blog->update($data, $where);
      *
      * @param string $table the table to update in
      * @param array $data a ready-made array of 'fieldName' => 'value' elements
      * @param string $dataTypes a string of datatype characters to match the prepared statement placeholders this query needs
      * @param array $where. An also ready-made array of 'fieldName' => 'value' which will be used for the 'WHERE' 'fieldName' = 'value' clause
      *     Note very well that you should add one more character type to the $dataTypes string for each element in the 'where' clause, as this method will use prepared statements for each one,
      *     otherwise the DB query will not work. Also, make sure the data type character you pass in matches the data type of the field the 'WHERE' clause is referring to.
      *
      * @return bool
      */
    public function update($data, $where)
    {
        $model = new $this->whoCalledMe;
        $table = $model->getTable();

        $data = (array) $data;
        $newData = [];

        $dataTypes = '';
        $tableDataClues = $model->getColumnDataTypes();
        
        foreach ($data as $dataKey => $dat) {
            foreach ($tableDataClues as $dataClueKey => $columnDatClue) {
                if ($dataClueKey == $dataKey) {
                    $dataTypes .= $columnDatClue;

                    $newData[$dataKey] = $dat;
                    if ($dataClueKey == 'users_pass')
                    {
                        $key = $this->getSalt();
                        $newData['key'] = $key;
                        $dataTypes .= 's';
                    }
                }
            }
        }

        foreach ($where as $criteriaKey => $criteria)
        {
            foreach ($tableDataClues as $dataClueKey => $columnDatClue) {
                if ($dataClueKey == $criteriaKey) {
                    $dataTypes .= $columnDatClue;
                }
            }
        }

        $db = $this->connect();

        list( $fields, $placeholders, $values ) = $this->insert_update_prep_query($newData, 'update');

        $where_clause = '';
        $where_values = [];
        $count = 0;

        foreach ( $where as $field => $value )
        {
            if ( $count > 0 ) {
                $where_clause .= ' AND ';
            }

            $where_clause .= $field . '=?';
            $where_values[] = $value;

            $count++;
        }

        array_unshift($values, $dataTypes);
        $values = array_merge($values, $where_values);

        $stmt = $db->prepare("UPDATE {$table} SET {$placeholders} WHERE {$where_clause}");
        call_user_func_array( array( $stmt, 'bind_param'), $this->ref_values($values));

        $stmt->execute();

        if ( $stmt->affected_rows ) {
            return true;
        }

        return false;
    }
        


     


        
        
        
     /**
      * a method in your model prepares the args for this method n calls it
      *
      * @return Bool true or false for whether the deletion was successful or not
      */
    public function delete($table, $where = array(), $dataTypes = '')
    {
        $db = $this->connect();

        if (empty($where)) {
            $sql = $db->prepare("DELETE FROM {$table}");

            $result = $this->query($sql);

            if ($result) {
                return true;
            }
            else {
                return false;
            }
        }
        elseif (!empty($where)) {

            // Cast all data to arrays
            $where = (array) $where;
            $dataTypes = (string) $dataTypes;

            $where_placeholders = '';
            $where_values = [];
            $count = 0;

            foreach ($where as $field => $value) {
                if ($count > 0) {
                    $where_placeholders .= ' AND ';
                }

                $where_placeholders .= $field . '=?';
                $where_values[] = $value;

                $count++;
            }

            array_unshift($where_values, $dataTypes);

            $stmt = $db->prepare("DELETE FROM {$table} WHERE {$where_placeholders}");

            call_user_func_array(array($stmt, 'bind_param'), $this->ref_values($where_values));

            $stmt->execute();

            if ($stmt->affected_rows) {
                return true;
            }

            return true;
        }

    }




        


    /**
     * Builds the query strings from the data (e.g. arrays) given
     *
     * Works fine
     */
    private function insert_update_prep_query($data, $type = 'insert')
    {
        $fields = '';
        $placeholders = '';
        $values = array();

        foreach ( $data as $field => $value )
        {
            if ($field == 'key')
            {
                $values[] = $value;
                continue;
            }

            $fields .= "{$field},";
            $values[] = $value;

            if ( $type == 'update')
            {
                if ($field == 'users_pass')
                {
                    $placeholders .= $field ." = AES_ENCRYPT(?, ?),";
                }
                else
                {
                    $placeholders .= $field . '=?,';
                }
            }
            elseif ($field == 'users_pass')
            {
                $placeholders .= "AES_ENCRYPT(?, ?),";
            }
            elseif ($field == 'users_created')
            {
                $placeholders .= "NOW(),";
            }
            else
            {
               $placeholders .= '?,';
            }
        }

        $values = array_filter($values);

        $fields = substr($fields, 0, -1);
        $placeholders = substr($placeholders, 0, -1);


        return array( $fields, $placeholders, $values );
    }




     /**
      * Alternative to fetch_assoc() method of the myslqli object which requires the mysqlnd driver to be installed on your webspace.
      * If it is not, you will have to work with BIND_RESULT() & FETCH() methods. It uses fetch() in the background and returns you
      * the array that you are used to having with $stmt->fetch_assoc(). Call this method passing it your $stmt.  Since it uses
      * $stmt->fetch() internally, you can call it just as you would call mysqli_result::fetch_assoc
      * (just be sure that before you call this method the $stmt object is still open (not closed yet) and the result of your DB query
      * is already stored using $stmt->store_result())
      *
      * @param $stmt
      * @return array|null
      */
     private function fetchAssocStatement($stmt)
     {
         if($stmt->num_rows>0)
         {
             $result = array();
             $md = $stmt->result_metadata();
             $params = array();
             while($field = $md->fetch_field()) {
                 $params[] = &$result[$field->name];
             }
             call_user_func_array(array($stmt, 'bind_result'), $params);
             if($stmt->fetch())
                 return $result;
         }

         return null;
     }







    /**
     * Creates an optimized array to be used by bind_param() to bind
     * values to the query placeholders
     *
     */
    private function ref_values($array)
    {
        $refs = array();
        foreach ($array as $key => $value) {
                $refs[$key] = &$array[$key];
        }
        return $refs;
    }







     /**
      * This method is specifically for deleting folders recursively. This bypasses the limitation of PHP's rmdir not
      * being able to delete folders with files in them
      *
      * It removes all files and subfolders within the given directory, and finalizes by deleting the now empty folder itself
      *
      * @param string $dir the folder to delete
      *
      * @return true
      */
     public function deleteFolderTree($dir)
     {
         $files = array_diff(scandir($dir), array('.','..'));
         foreach ($files as $file) {
             (is_dir("$dir/$file")) ? $this->deleteFolderTree("$dir/$file") : unlink("$dir/$file");
         }
         return rmdir($dir);
     }






     /**
      * return all records from any model. Optionally specify if records should be ordered by a specific column by passing
      * the name of the column as a string. e.g.
      *     getAll('columnName') will order the results by 'columnName' in an ascending order by default
      *     getAll('columnName DESC') will order the results by 'columnName' in an descending order
      *
      * @param string $orderBy If you have a column named TABLENAME_'name' then the results will be auto ordered by this field.
      * However, if you called this column something else and want to order it by it, you will have to pass the column name to it
      * @return array|bool|mixed
      */
    public function getAll($orderBy = '')
    {
        $model = new $this->whoCalledMe;
        $table = $model->getTable();

        if ($orderBy == '')
        {
            $order = array_key_exists($table.'_name',$model->getColumnDataTypes()) ?  ' ORDER BY '.$table.'_name' : '';
        }
        else
        {
            $order = ' ORDER BY '.$orderBy;
        }

        $query = "SELECT * FROM ".$table.$order;

        $everything = $this->query($query);

        if ($everything)
        {
            return $everything;
        }
    }










    public function getNameFromId($id)
    {
        $model = new $this->whoCalledMe;
        $table = $model->getTable();

        $query = "SELECT ".$table."_name FROM $table WHERE ".$table."_id = $id";

        $result = $this->query($query);

        if ($result)
        {
            return $result[0][$table.'_name'];
        }
    }







     /**
      * This method is to allow models to be able to grab everything about a record using its ID
      *
      * @param $id
      * @return array
      */
     public function getById($id)
     {
         $model = new $this->whoCalledMe;
         $table = $model->getTable();

         $query = "SELECT * FROM $table WHERE ".$table."_id = $id";

         $result = $this->query($query);

         if ($result)
         {
             return $result;
         }
     }








    /**
    * Ideally, we should have made the $table.'_id' below be $this->id instead if that property is set in the model class, and then else use $table.'_id'.
    *
    * This is to allow developers give their model table id field any name they want while the default one will be the one currently used: $table.'_id' (e.g. albums_id)
    * On second thought however, we dont need this. It will be too much work to have to check for a separate id name for model PK fields. IT'S SIMPLE; IF THE DEV DOES NOT FOLLOW THE
    * column-naming convention of $tablename_id, and $tablename_name then they should not use these ready-made methods in the model, but write their own - simples :)
    *
    * @params $name the name of the record whose ID you want to know
    *
    * @return array $result[0][$table.'_id'] The ID of the record having the given name
    */
    public function getIdFromName($name)
    {
        $album_name = strtolower($name);
        $model = new $this->whoCalledMe;
        $table = $model->getTable();
        $query = "SELECT ".$table."_id FROM $table WHERE ".$table."_name = '$name'";

        $result = $this->query($query);

        if ($result)
        {
            return $result[0][$table.'_id'];
        }
    }







     /**
      * This method is to allow models to be able to grab everything about a record using its name
      * It assumes of course that you have followed the Dorguzen table column naming convention which is to have one column named: 'tableName_name'
      *
      * @param $id
      * @return array
      */
     public function getByName($recordName)
     {
         $model = new $this->whoCalledMe;
         $table = $model->getTable();

         $query = "SELECT * FROM $table WHERE ".$table."_name = '$recordName'";

         $result = $this->query($query);

         if ($result)
         {
             return $result;
         }
     }







    /**
     * If you give your model table a column called $TABLENAME_default_record you can grab that one record easily with this method
     *
     * This will come in handy with modules like blog images, blog posts, gallery albums etc, any module at all where you
     * wish to initialise your view page with one record. A great tip is to allow the setting of this default record from an
     * admin dashboard where you have a CRUD operations for an admin user CMS
     *
     * Note, to avoid the case where you have nothing to show if the admin user has not specified a default record, this
     * code will grab another random record it can find if none is set as default.
     *
    * @return array of one record
    */
     public function getDefaultRecord()
     {
         $model = new $this->whoCalledMe;
         $table = $model->getTable();
         $query = "SELECT * FROM ".$table." WHERE ".$table."_default_record = 1";

         $defaultRec = $this->query($query);

         if ($defaultRec)
         {
             return $defaultRec;
         }
         else
         {
             $query2 = "SELECT * FROM ".$table;
             $allRecs = $this->query($query2);

             if ($allRecs)
             {
                 return $allRecs;
             }
             else
             {
                 return false;
             }
         }
     }








     /**
      *
      * This meth does select all (SELECT *) or only SELECTS a given set of columns
      * If no $columns are specified, none of the other params will have anything, so its does a SELECT all
      * If $columns are specified, then $where must also be specified
      * $columns is given the names of columns u wanna grab, the model will check that they exist in the table
      * $where is given the column names you're matching on as keys, & the column values required as the values.The model also checks if these keys
      * exist in the table.
      *
      * @param string $columns of fields to grab
      * @param array $criteria is an assoc array of 'where key (column name) => value' sort o thing
      *
      * @return array of results
      */
    public function grabWhere($columns = array(), $criteria = array())
    {
        $model = new $this->whoCalledMe;

        $fields_to_select = array();
        $datatypes = '';
        $criterion = array();
        if (!empty($columns)) {
            foreach ($columns as $column) {
                if (!array_key_exists($column, $model->getColumnDatatypes())) {
                    return 'The field ' . $column . ' does not exist in the ' . strtolower($model->getTable() . ' table');
                }
                else {
                    $fields_to_select[] = $column;
                }
            }

            foreach ($criteria as $key => $crits)
            {
                if (!array_key_exists($key, $model->getColumnDatatypes())) {
                    return 'The field ' . $key . ' does not exist in the ' . strtolower($model->getTable() . ' table');
                }
                else {
                    $criterion[$key] = $crits;
                    $datatypes .= $model->getColumnDatatypes()[$key];
                }
            }
        }

        $table = strtolower($model->getTable());
        $columns_needed = $fields_to_select;
        $where = $criterion;

        $db = $this->connect();

        if (empty($columns)) {
            $sql = "SELECT * FROM $table";

            $result = $this->query($sql);

            if (is_array($result)) {
                return $result;
            }
            else {
                return false;
            }

        }
        elseif (!empty($columns)) {
            $columns = (array) $columns;
            $where = (array) $where;

            $where_placeholders = '';
            $where_values = [];
            $count = 0;

            foreach ( $where as $field => $value ) {
                if ( $count > 0 ) {
                    $where_placeholders .= ' AND ';
                }

                $where_placeholders .= $field . '=?';
                $where_values[] = $value;

                $count++;
            }

            array_unshift($where_values, $datatypes);

            $columns_as_string = implode(',', $columns);
            $stmt = $db->prepare("SELECT {$columns_as_string} FROM {$table} WHERE {$where_placeholders}");

            call_user_func_array( array( $stmt, 'bind_param'), $this->ref_values($where_values));

            $stmt->execute();

            $stmt->store_result();

            if ($stmt->num_rows )
            {
                $results_basket = [];

                while($row = $this->fetchAssocStatement($stmt))
                {
                    $results_basket[] = $row;
                }

                $stmt->close();

                return $results_basket;
            }
            else
            {
                return false;
            }
        }
    }







     /**
      * This method takes only 1 mandatory argument; the columns of the fields to grab
      * The other three ($where, $order and $sort) are optional
      *
      * This is for situations where you want to grab only selected fields from the DB and nothing else
      *
      * @param array $columns of fields to grab
      *
      * @return array
      */
     public function selectOnly($columns, $where = null, $order = '', $sort = '')
     {
         $model = new $this->whoCalledMe;

         $datatypes = '';
         if (!empty($columns)) {
             foreach ($columns as $column) {
                 if (!array_key_exists($column, $model->getColumnDatatypes())) {
                     return 'The field ' . $column . ' does not exist in the ' . strtolower($model->getTable() . ' table');
                 }
             }
         }

         $columns_needed = implode(',', $columns);
         $order = $order != ''?$order:$columns[0];
         $sort = $sort != ''?$sort:'ASC';
         $table = strtolower($model->getTable());

         $whereClause = '';
         if ($where != null)
         {
             $whereCount = 0;
             foreach ($where as $whereKey => $whereVal)
             {
                 if ($whereCount < 1) {
                     $whereClause .= ' WHERE ' . $whereKey . " = '$whereVal'";
                 }
                 else
                 {
                     $whereClause .= ' AND ' . $whereKey . ' = ' . $whereVal;
                 }
                 $whereCount++;
             }

         }

         $query = "SELECT $columns_needed FROM $table $whereClause ORDER BY $order $sort";

         $result = $this->query($query);

         return $result;
     }

     
     
     
     


     /**
      * Get the total number of records in this table
      *
      * @return mixed
      */
     public function getCount()
     {
         $model = new $this->whoCalledMe;
         $table = $model->getTable();
         $query = "SELECT COUNT(*) AS number FROM $table";

         $total = $this->query($query);
         if ($total != 'failed')
         {
             $totalRecs = $total[0]['number'];

             return $totalRecs;
         }
     }







     public function getPaginated($start, $numPerPage)
     {
         $model = new $this->whoCalledMe;
         $table = $model->getTable();
         $query = "SELECT * FROM $table LIMIT $start, $numPerPage";

         $chunk = $this->query($query);

         if ($chunk)
         {
             return $chunk;
         }

     }



  }


    
 
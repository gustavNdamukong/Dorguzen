<?php
namespace DGZ_library;



class DGZ_Model
{
    protected $config;

    protected $salt = '';

    protected $whoCalledMe = '';

    private $passwordField = [
        'password',
        'pwd',
        'user_pwd',
        'users_pwd',
        'user_password',
        'users_password',
        'user_pass',
        'users_pass'
    ];



    public function __construct()
    {
        $classThatCalled = get_class($this);
        $this->whoCalledMe = $classThatCalled;
        $application = new DGZ_Application();
        $this->config = $application->getAppConfig();

        //get DB connection credentials
        if ($credentials = $this->config->getConfig()['live'] == false) {
            $credentials = $this->config->getConfig()['localDBcredentials'];
        }
        elseif ($this->config->getConfig()['live'] == true)
        {
            $credentials = $this->config->getConfig()['liveDBcredentials'];
        }

        $this->salt = $credentials['key'];
    }



    protected function connect()
    {
        return DGZ_DB_Singleton::getInstance();
    }



    public function getSalt()
    {
        $salt = (string) $this->salt;

        return $salt;
    }



    /**
     * This method is called ONLY by models at load time to map to their tables n initialize
     * vital settings
     */
    public function loadORM($model)
    {
        //This does two things;
        //    registers all the model's field types into a '_columns' array member, 
        //    loads all model's data into a $data array member
        $table = $this->getTable();
        $db = $this->connect();

        $schemaQuery = 'DESCRIBE '.strtolower($table);
        $schemaResult = $db->query($schemaQuery);

        //Eager-load model data
        $dataQuery = 'SELECT * FROM '.strtolower($table);
        $dataResult = $db->query($dataQuery);

        //check result if SELECTING the field types
        if ((isset($schemaResult->num_rows)) && ($schemaResult->num_rows > 0))
        {
            $results = array();
            while ($row = $schemaResult->fetch_assoc())
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
        //WE ARE ONLY SELECTING HERE, SO WE DONT NEED THIS PART
        /*else {
            //check result if Updating/inserting/deleting
            if ((isset($result->affected_rows)) && ($result->affected_rows > 0)) {
                return true;
            }
        }*/

         //load the model fields data
         if ((isset($dataResult->num_rows)) && ($dataResult->num_rows > 0))
         { 
            $dataResults = array();
            while ($dataRow = $dataResult->fetch_assoc())
            {
                $dataResults[] = $dataRow;
            }

            foreach ($dataResults as $field => $fieldValue) {
                $model->data[$field] = $fieldValue;
            }
         }
    }



    public function __set($member, $value)
    {
        if (array_key_exists($member, $this->_columns)) {
            $this->data[$member] = $value;
        }
    }



    /**
     * This member being retrieved must have been created already using __set() above
     */
    public function __get($member)
    {
        if (array_key_exists($member, $this->_columns)) {
            return $this->data[$member];
        }
    }


    /**
     * Grab & return the data of a model
     */
    public function getData()
    {
        return $this->data;
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



    public function escapeString4DB($string)
    {
        $db = $this->connect();
        return $db->real_escape_string($string);
    }



    /**
     * It it recommended to assign values to all fields on a model class after having initialized them with NULLs
     * to avoid errors of number of parameters provided not matching the number of fields on the table.
     * Obviously, make sure those NULL fields can actually accept a NULL in the DB
     *
     * @return bool|string
     */
    public function save()
    {
        $model = new $this->whoCalledMe;
        $db = $this->connect();
        $table = $model->getTable();

        $data = array();
        $datatypes = '';

        /*foreach (get_object_vars($this) as $property => $value) {
            if (array_key_exists($property, $model->_columns)) {
                $data[$property] = $value;

                if (in_array($property, $this->passwordField)) {
                    $data['key'] = $this->getSalt();
                    $datatypes .= 'ss';
                }
                else {
                    $datatypes .= $model->_columns[$property];
                }
            }
        }*/
        //---------------Above dynamic setting of properties on objects is deprecated since PHP 8.0
        //---------------Our chosen workaround is to have a data array member on all models into 
        //---------------which to store their field data
        foreach ($this->data as $property => $value) {
            if (array_key_exists($property, $model->_columns)) {
                $data[$property] = $value;

                if (in_array($property, $this->passwordField)) {
                    $data['key'] = $this->getSalt();
                    $datatypes .= 'ss';
                }
                else {
                    $datatypes .= $model->_columns[$property];
                }
            }
        }
        //-------------------------------------

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
            return '1062';
        }
        else
        {
            return false;
        }
    }



    /**
     * This method takes a 'where' clause array of 'fieldName' => 'value' pairs.
     *
     * @example:
     *         $products = new Products();
     *         $products->products_authorized = 'yes';
     *         $products->products_authorized_date = date("Y-m-d H:i:s");
     *         $products->products_authorized_by = $authorizerId;
     *
     *         $where = ['products_id' => $adId];
     *
     *         $updated = $products->updateObject($where);
     *
     * @param $where
     * @return bool|string
     */
    public function updateObject($where)
    {
        $model = new $this->whoCalledMe;
        $table = $model->getTable();

        $data = array();
        $newData = [];
        $dataTypes = '';

        foreach (get_object_vars($this) as $property => $value) {
            if (array_key_exists($property, $model->_columns)) {
                $newData[$property] = $value;
                if (in_array($property, $this->passwordField)) {
                    $newData['key'] = $this->getSalt();
                    $dataTypes .= 'ss';
                }
                else {
                    $dataTypes .= $model->_columns[$property];
                }
            }

        }

        foreach ($where as $field => $val)
        {
            if (array_key_exists($field, $model->_columns)) {
                $dataTypes .= $model->_columns[$field];
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
     * Delete a record by its field ID when you are not worried about managing fk constraints.
     * If you are worried about fk constraints; use the deleteWhere() method instead.
     * @param int $id the value to match on the table's given ID field
     * @param bool $tablePrefixed if the id field is named with a prefix of the table name, default true
     *     This will be the field name used as the ID field if $fieldName is a blank string
     * @param string $fieldName if the id field is named something else-not 'id' or table name-prefixed,
     *     default '' (blank string). If this is not blank, this is the field that will be used as the ID field. Use it to
     *     override all other choices and specify the exact table field name to match.
     * @return bool
     */
    public function deleteById($id, $tablePrefixed = true, $fieldName = '')
    {
        $table = $this->getTable();
        $db = $this->connect();

        if ($fieldName != '') {
            $stmt = $db->prepare("DELETE FROM {$table} WHERE {$fieldName} = ?");
        }
        else if ($tablePrefixed)
        {
            $stmt = $db->prepare("DELETE FROM {$table} WHERE {$table}_id = ?");
        }
        else
        {
            $stmt = $db->prepare("DELETE FROM {$table} WHERE id = ?");
        }
        $stmt->bind_param( 'i', $id );
        $stmt->execute();

        return true;
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

        //check result if SELECTING
        if ((isset($res->num_rows)) && ($res->num_rows > 0))
        {
            $results = array();
            while ($row = $res->fetch_assoc())
            {
                $results[] = $row;
            }

            return $results;
        }

        //check result if INSERTING/UPDATING/DELETING
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
     * This meth does select all (SELECT *) as well as only on given columns (SELECT bla FROM bla WHERE)
     * $columns is given the names of columns u wanna grab, the model will make sure they exist in its table columns
     * $criteria is given and it is an array of the columns you're matching on as keys, n the values to match in the columns as the values.
     *     This is basically what will be used to build the where clause of the SQL query. The model also ensures these keys
     *     exist in the target column.
     * $orderBy is for the developer to pass in a string to be used to order the results of the SQL query. An example value it takes will be
     *     'ORDER BY name DESC'
     *     Internally; the model uses the table field data types which it stores as a string in $dataTypes for the column placeholders;
     *     which it easily gets from the values of its columns array.
     *     The following is a summary of its logic flow:
     *         If $columns has somethings, and $criteria has something, it grabs the field(s) in $columns that meet the criteria in $criteria
     *         If $columns is empty, and $criteria has something, it grabs all field(s) on the table that meet the criteria in $criteria
     *         If $columns has something, and $criteria is empty, it grabs the field(s) in $columns only
     *         If $columns is empty, and $criteria is empty, it grabs all field(s) on the table
     *         $orderBy is used in any case, as long as it is not blank
     *
     * @returns array with the results
     *
     */
    public function selectWhere($columns = array(), $criteria = array(), $orderBy = '')
    {
        $model = new $this->whoCalledMe;

        $fields_to_select = array();
        $datatypes = '';
        $criterion = array();
        $table = strtolower($model->getTable());


        if ((!empty($columns)) && (!empty($criteria))) {
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

        if ((empty($columns)) && (!empty($criteria))) {

            foreach ($model->getColumnDatatypes() as $fieldName => $datatype)
            {
                $fields_to_select[] = $fieldName;
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

        if ((!empty($columns)) && (empty($criteria))) {
            foreach ($columns as $column) {
                if (!array_key_exists($column, $model->getColumnDatatypes())) {
                    return 'The field ' . $column . ' does not exist in the ' . strtolower($model->getTable() . ' table');
                }
                else {
                    $fields_to_select[] = $column;
                }
            }
        }

        if ((empty($columns)) && (empty($criteria))) {
            foreach ($model->getColumnDatatypes() as $fieldName => $datatype)
            {
                $fields_to_select[] = $fieldName;
            }
        }

        $db = $this->connect();

        $columns = (array)$fields_to_select;
        $where = (array)$criterion;

        $where_placeholders = '';
        $where_values = [];
        $count = 0;

        $columns_as_string = implode(',', $columns);

        if (!empty($where)) {
            foreach ($where as $field => $value) {
                if ($count > 0) {
                    $where_placeholders .= ' AND ';
                }

                $where_placeholders .= $field . '=?';
                $where_values[] = $value;

                $count++;
            }

            array_unshift($where_values, $datatypes);

            if ($orderBy != '') {
                $stmt = $db->prepare("SELECT {$columns_as_string} FROM {$table} WHERE {$where_placeholders} {$orderBy}");
            }
            else {
                $stmt = $db->prepare("SELECT {$columns_as_string} FROM {$table} WHERE {$where_placeholders}");
            }

            call_user_func_array(array($stmt, 'bind_param'), $this->ref_values($where_values));
        }
        else {
            if ($orderBy != '') {
                $stmt = $db->prepare("SELECT {$columns_as_string} FROM {$table} {$orderBy}");
            }
            else {
                $stmt = $db->prepare("SELECT {$columns_as_string} FROM {$table}");
            }
        }

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
        $db = $this->connect();
        $table = $model->getTable();

        $datatypes = '';
        $dataClean = [];

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $model->_columns)) {
                $dataClean[$key] = $value;
                if (in_array($key, $this->passwordField)) {
                    $dataClean['key'] = $this->getSalt();
                    $datatypes .= 'ss';
                }
                else {
                    $datatypes .= $model->_columns[$key];
                }
            }
        }

        list( $fields, $placeholders, $values ) = $this->insert_update_prep_query($dataClean);

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
     * $updated = $blog->update($data, $where);
     *
     * @param array $data an array of 'fieldName' => 'value' pairs for the DB table fields to be updated
     * @param array $where. An array of 'key' - 'value' pairs which will be used to build the 'WHERE' clause
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

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $model->_columns)) {
                $newData[$key] = $value;
                if (in_array($key, $this->passwordField)) {
                    $newData['key'] = $this->getSalt();
                    $dataTypes .= 'ss';
                }
                else {
                    $dataTypes .= $model->_columns[$key];
                }
            }
        }

        //prepare the datatype string for the where clause too
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
     * You wouldn't call this method directly in code, but rather a method in your model prepares the args for this method & calls it
     * @return Bool true or false for whether the deletion was successful or not
     */
    public function delete($table, $where = array(), $dataTypes = '')
    {
        $db = $this->connect();

        if (empty($where)) {
            //Truncate table if no criteria is given
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
            $where = (array) $where;
            $dataTypes = (string) $dataTypes;

            //prepare the where clause
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
                if (in_array($field, $this->passwordField))
                {
                    $placeholders .= $field ." = AES_ENCRYPT(?, ?),";
                }
                else {
                    $placeholders .= $field . '=?,';
                }
            }
            else if (in_array($field, $this->passwordField))
            {
                $placeholders .= " AES_ENCRYPT(?, ?),";
            }
            else
            {
                $placeholders .= '?,';
            }
        }

        // Normalize $fields and $placeholders for inserting
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
     * @param $data array containing the username & password to authenticate the user with
     * @return array|bool It returns false if the login fails, or an array of all fields in your users table
     */
    public function authenticateUser($data)
    {
        $model = new $this->whoCalledMe;
        $tableColumns = $model->_columns;

        $connect = $this->connect();
        $dataTypes = '';
        $emailField = '';
        $emailValue = '';
        $passwordField = '';
        $passwordValue = '';
        $salt = '';

        foreach ($data as $key => $value) {
            if (array_key_exists($key, $tableColumns)) {
                if (in_array($key, $this->passwordField)) {
                    $passwordField = $key;
                    $passwordValue = $value;
                    $salt = $this->getSalt();
                    $dataTypes .= 'ss';
                }
                else {
                    $emailField = $key;
                    $emailValue = $value;
                    $dataTypes .= $tableColumns[$key];
                }
            }
        }

        $sql = "SELECT * FROM ".$this->getTable()." 
            WHERE ".$emailField." = ? 
            AND ".$passwordField." = AES_ENCRYPT(?, ?)";

        $stmt = $connect->stmt_init();
        $stmt->prepare($sql);

        $stmt->bind_param($dataTypes, $emailValue, $passwordValue, $salt);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows )
        {
            $row = $this->fetchAssocStatement($stmt);
            $stmt->close();
            return $row;
        }
        else
        {
            return false;
        }
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

        return $everything;

    }



    public function getNameFromId($id, $lang)
    {
        $model = new $this->whoCalledMe;
        $table = $model->getTable();

        $query = "SELECT ".$table."_name_$lang FROM $table WHERE ".$table."_id = $id";

        $result = $this->query($query);

        if ($result)
        {
            return $result[0][$table."_name_".$lang];
        }
    }



    /**
     * Grab everything from a record using its ID. If you did not follow the DGZ convention of prefixing
     * your table field names with the 'tablename_', you can pass it the actual id field name in $fieldName
     * If you did, just pass true to the second argument.
     * If however you pass only one argument-the actual id field value, this method will check if you
     * specified the id field of your model in a 'idField' property & use it, otherwise, it assumes that
     * your model table's id field is just 'id'.
     *
     * @param int $id the value to match on the table's given ID field
     * @param bool $tablePrefixed if the id field is named with a prefix of the table name, default true
     *     This will be the field name used as the ID field if $fieldName is a blank string
     * @param string $fieldName if the id field is named something else-not 'id' or table name-prefixed,
     *     default '' (blank string). If this is not blank, this is the field that will be used as the ID field. Use it to
     *     override all other choices and specify the exact table field name to match.
     * @return array
     */
    public function getById($id, $tablePrefixed = true, $fieldName = '')
    {
        $table = $this->getTable();
        $db = $this->connect();
        $model = new $this->whoCalledMe;

        if ($fieldName != '') {
            $stmt = $db->prepare("SELECT * FROM {$table} WHERE {$fieldName} = ?");
        }
        else if ($tablePrefixed)
        {
            $stmt = $db->prepare("SELECT * FROM {$table} WHERE {$table}_id = ?");
        }
        else
        {
            if (property_exists($model, 'idField'))
            {
                $stmt = $db->prepare("SELECT * FROM {$table} WHERE {$model->idField} = ?");
            }
            else
            {
                $stmt = $db->prepare("SELECT * FROM {$table} WHERE id = ?");
            }
        }
        $stmt->bind_param( 'i', $id );
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows) {
            $results = [];
            while ($row = $this->fetchAssocStatement($stmt)) {
                $results[] = $row;
            }

            $stmt->close();

            return $results[0];
        }
        else {
            return false;
        }
    }


    /**
     * Only use this method if you followed the DGZ table-naming convention of prefixing your table fields with 'tablename_'
     * This method could be useful for records like users, or locations etc which have names you might need to fetch.
     * It returns $result[0][$table.'_id'] The ID of the record having the given name, or false if no match is found.
     *
     * @params $name the name of the record whose ID you want to know
     *
     * @return mixed
     */
    public function getIdFromName($name)
    {
        $db = $this->connect();
        $table = $this->getTable();

        $stmt = $db->prepare("SELECT {$table}_id FROM {$table} WHERE {$table}_name = ?");
        $stmt->bind_param( 's', $name );
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows) {
            $results = [];
            while ($row = $this->fetchAssocStatement($stmt)) {
                $results[] = $row;
            }

            $stmt->close();

            return $results[0][$table.'_id'];
        }
        else {
            return false;
        }
    }



    /**
     * Only use this method if you followed the DGZ table-naming convention of prefixing your table fields with 'tablename_'
     * This method could be useful for records like users, or locations etc which have names you might need to fetch.
     *
     * @param $recordName
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
     * This method takes only 1 mandatory argument; the columns of the fields to grab
     * The other three ($where, $order and $sort) are optional
     *
     * This is for situations where you want to grab only selected fields from the DB and nothing else
     * DISCLAIMER: The where clause assumes all values of the provided
     *
     * @param array $columns of fields to grab
     * @param array $where optional, where the keys are column names, & the values are the values the columns have to match to be selected
     * @param string $order - string which should be the column name you wish the results to be ordered by e.g. 'username'
     * @param string $sort - a string of the the type of sorting you want done on the result e.g. 'ASC' or 'DESC'
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
                    if (gettype($whereVal) === 'string')
                    {
                        $whereClause .= ' WHERE ' . $whereKey . " = '$whereVal'";
                    }
                    else
                    {
                        $whereClause .= ' WHERE ' . $whereKey . ' = '.$whereVal;
                    }

                }
                else
                {
                    if (gettype($whereVal) === 'string') {
                        $whereClause .= ' AND ' . $whereKey . " = '$whereVal'";
                    }
                    else
                    {
                        $whereClause .= ' AND ' . $whereKey . ' = '.$whereVal;
                    }
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



    public function timeNow()
    {
        return date("Y-m-d:H:i:s");
    }
}




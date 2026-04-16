<?php
namespace Dorguzen\Core;

use Dorguzen\Config\Config;
use Exception;
use BadMethodCallException;
use RuntimeException;
use Dorguzen\Core\DGZ_Exception;
use Dorguzen\Controllers\ExceptionController;


class DGZ_Model
{
    protected $config;

    protected $salt = '';

    protected $whoCalledMe = ''; 

    protected static array $schemaCache = [];

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

    /**
     * The primary key field name for this model's table.
     * Override in your model if the PK is not 'id' or the DGZ convention of 'tablename_id'.
     * Used by __call() to resolve the current record's PK value for lazy loading.
     */
    protected $id = '';

    /**
     * Declares child models and their foreign keys pointing back to this model.
     * Format: [ChildClass::class => 'child_foreign_key']
     * If the FK is omitted (''), DGZ defaults to strtolower(ThisClass)_id.
     * Override this in your model with protected visibility.
     */
    protected $_hasChild = [];

    /**
     * Declares parent models and the foreign key on this model that points to the parent.
     * Format: [ParentClass::class => 'this_foreign_key']
     * If the FK is omitted (''), DGZ defaults to strtolower(ParentClass)_id.
     * Override this in your model with protected visibility.
     */
    protected $_hasParent = [];



    public function __construct(?Config $config)
    { 
        $this->config = $config;

        // This produces the string name of the curerent class
        // eg 'Users' - only to be used for metadata logging
        $this->whoCalledMe = get_class($this); 
        $credentials = $this->config->getConfig('database.DBcredentials');

        $this->salt = $credentials['key'];
    }

    
    
    /**
     * connect() is a crucial method for the DGZ ORM system. 
     * It identifies and couples the model to its DB table. 
     * Without it the ORM will fail. For that reason, connect() 
     * must be called early inside all ORM methods e.g 
     * save(), update(), selectWhere(), delete() etc.
     * @return DGZ_DBAdapter
     */
    protected function connect()
    {
        $db = DGZ_DB_Singleton::getInstance();
        $this->hydrateSchemaIfNeeded();
        return $db;
    }



    public function getSalt()
    {
        $salt = (string) $this->salt;

        return $salt;
    }



    /**
     * This method is called ONLY by models at load time to map to their tables & initialize
     * vital settings.
     * This does two things;
     *   registers all the model's field types into a '_columns' array member, 
     *   loads all model's data into a $data array member of the model
     */
    /*public function loadORM($model)
    {
        $table = $this->getTable();
        $db = $this->connect();

        // 1. Load schema
        $schemaQuery = 'DESCRIBE ' . lcfirst($table);
        $columns = $db->query($schemaQuery);

        if (!empty($columns)) {
            foreach ($columns as $column) {
                $val = 's'; // default
                if (preg_match('/int/', $column['Type'])) $val = 'i';
                if (preg_match('/decimal|float/', $column['Type'])) $val = 'd';
                $model->_columns[$column['Field']] = $val;
            }
        }
    }*/


    /**
     * This method is called ONLY by models when they connect to DB to map 
     * to their tables & initialize vital settings.
     * It registers all the model's field types into a '_columns' array member
     */
    /*protected function hydrateSchemaIfNeeded(): bool
    {
        $class = static::class;

        if (isset(self::$schemaCache[$class])) {
            $this->_columns = self::$schemaCache[$class];
            return true;
        }

        $db = DGZ_DB_Singleton::getInstance();

        $table = $this->getTable();

        // Load schema
        $schemaQuery = 'DESCRIBE ' . lcfirst($table);
        $columns = $db->query($schemaQuery);

        $schema = [];

        if (!empty($columns)) {
            foreach ($columns as $column) {
                $val = 's';
                if (preg_match('/int/', $column['Type'])) $val = 'i';
                if (preg_match('/decimal|float/', $column['Type'])) $val = 'd';

                $schema[$column['Field']] = $val;
            }
        }

        self::$schemaCache[$class] = $schema;
        $this->_columns = $schema;

        //--------------TESTING--------------------
        // validate ORM
        if (!isset($this->data))
        {
            return false;
        }
        foreach ($this->data as $setDataKey => $setDataValue)
        {
            if (!array_key_exists($setDataKey, $this->_columns))
            {
                // fields have been set which should not exist on the model  
                return false;
            }
        }

        // set null defaults on model field values that were not set by developer
        foreach ($this->_columns as $column => $_type) {
            if (!array_key_exists($column, $this->data)) {
                $this->data[$column] = null;
            }
        }
        return true;
        //--------------END TEST--------------------
    }*/


    /**
     * This method is called ONLY by models when they connect to DB to map 
     * to their tables & initialize vital settings.
     * It does two things;
     *   registers all the model's field types into a '_columns' array member, 
     *   sets all model's table field names into a $data array member of the model
     */
    protected function hydrateSchemaIfNeeded(): void
    {
        $class = static::class;

        if (!isset(self::$schemaCache[$class])) {
            $schema = $this->loadSchemaFromDatabase();
            self::$schemaCache[$class] = $schema;
        }

        $this->_columns = self::$schemaCache[$class];

        $this->validateModelData();
        // $this->applyNullDefaults();
    }


    /*public function loadSchemaFromDatabase()
    {
        $db = DGZ_DB_Singleton::getInstance();

        $table = $this->getTable();

        // Load schema
        $columns = $db->getTableSchema($table);

        if (empty($columns)) {
            dgzie("ORM schema load failed for table '{$table}' (" . static::class . ")");
        }

        $schema = [];

        foreach ($columns as $column) {
            $val = 's';
            if (preg_match('/int/', $column['Type'])) $val = 'i';
            if (preg_match('/decimal|float|real/', $column['Type'])) $val = 'd';

            $schema[$column['Field']] = $val;
        }

        return $schema;
    }*/


    /**
     * oadSchemaFromDatabase() calls getTableSchema() on the active driver to 
     * know the structure of the DB for use by the ORM.
     * Every driver’s getTableSchema() must return the SAME structure:
     *
     *  [
     *      'id'    => 'i',
     *      'name'  => 's',
     *      'email' => 's',
     *      'price' => 'd'
     *  ]
     * 
     *  ie [fieldName => bindType]
     * 
     * @return array
     */
    public function loadSchemaFromDatabase()
    {
        $db = DGZ_DB_Singleton::getInstance();
        $table = $this->getTable();

        $schema = $db->getTableSchema(lcfirst($table));

        if (empty($schema)) {
            dgzie("ORM schema load failed for table '{$table}' (" . static::class . ")");
            
        }

        return $schema;
    }


    // This is not DB logic — it’s just shape normalization.
    protected function normalizeSchemaColumn(array $column): array
    {
        // MySQL / MySQLi / PDO
        if (isset($column['Field'], $column['Type'])) {
            return [
                'Field' => $column['Field'],
                'Type'  => $column['Type'],
            ];
        }

        // SQLite
        if (isset($column['name'], $column['type'])) {
            return [
                'Field' => $column['name'],
                'Type'  => $column['type'],
            ];
        }

        dgzie("Unknown schema column format encountered");
        return [];
    }


    protected function validateModelData(): void
    {
        foreach ($this->data as $setDataKey => $setDataValue)
        {
            if (!array_key_exists($setDataKey, $this->_columns))
            {
                throw new RuntimeException(
                    "Invalid ORM field '{$setDataKey}' on model " . static::class
                );
            }
        }
    }

    protected function applyNullDefaults(): void
    {
        foreach ($this->_columns as $column => $_type) {
            if (!array_key_exists($column, $this->data)) {
                $this->data[$column] = null;
            }
        }
    }


    /**
     * Load the DB data of the current model using the given record ID
     * Call this on demand to load data onto a model properties from its table
     * @param int $id
     * @return object
     */
    public function loadData($id): object  
    {
        $model = $this;
        $table = $this->getTable();
        $db = $this->connect();

        $field = $this->getIdFieldName($model);

        $dataQuery = "SELECT * FROM {$table} WHERE {$field} = ?";
        $dataRows = $db->query($dataQuery, [$id]);

        foreach ($dataRows as $row) 
        {
            foreach ($row as $field => $value) 
            {
                $model->data[$field] = $value;
            }
        }
        return $this;
    }



    public function __set($member, $value)
    {
        $this->data[$member] = $value;
    }



    /**
     * This member being retrieved must have been created already using __set() above
     */
    public function __get($member)
    {
        return $this->data[$member];
    }



    /**
     * Enables lazy-loading of related models via dynamic method calls.
     *
     * DGZ checks $_hasChild first (returns a collection), then $_hasParent (returns a single record).
     * The method name is matched case-insensitively against the short class name of each entry.
     *
     * Examples:
     *   $user->orders()        // loads all Orders where orders FK = $user->users_id
     *   $post->users()         // loads the parent User record for this post
     *
     * @throws \RuntimeException       if the current record has no PK loaded (hasChild)
     *                                 or the FK field is missing from loaded data (hasParent)
     * @throws \BadMethodCallException if the method name matches no relationship
     */
    public function __call(string $name, array $arguments): mixed
    {
        // --- hasChild: returns a collection of child records ---
        foreach ($this->_hasChild as $classRef => $fkField) {
            if (strtolower($this->shortName($classRef)) === strtolower($name)) {
                if (empty($fkField)) {
                    $fkField = strtolower($this->shortName(static::class)) . '_id';
                }

                $pkField = !empty($this->id) ? $this->id : $this->getIdFieldName($this);
                $pkValue = $this->data[$pkField] ?? null;
                if ($pkValue === null) {
                    throw new RuntimeException(
                        "Cannot lazy-load '{$name}': no primary key value is loaded on " . static::class . ". " .
                        "Call loadData(\$id) first to load a record into the model."
                    );
                }

                $results = $this->resolveClass($classRef)->selectWhere([], [$fkField => $pkValue]);
                return $results ?: [];
            }
        }

        // --- hasParent: returns a single parent record ---
        foreach ($this->_hasParent as $classRef => $fkField) {
            if (strtolower($this->shortName($classRef)) === strtolower($name)) {
                if (empty($fkField)) {
                    $fkField = strtolower($this->shortName($classRef)) . '_id';
                }

                $fkValue = $this->data[$fkField] ?? null;
                if ($fkValue === null) {
                    throw new RuntimeException(
                        "Cannot lazy-load '{$name}': FK field '{$fkField}' is not present in the loaded " .
                        "data on " . static::class . ". Ensure a record was loaded first."
                    );
                }

                return $this->resolveClass($classRef)->getById($fkValue);
            }
        }

        throw new BadMethodCallException(
            "Call to undefined method " . static::class . "::{$name}()"
        );
    }



    /**
     * Extracts the short (unqualified) class name from a FQCN or a plain string.
     * e.g. 'App\Models\Posts' => 'Posts',  'Posts' => 'Posts'
     */
    private function shortName(string $classRef): string
    {
        $parts = explode('\\', $classRef);
        return end($parts);
    }



    /**
     * Resolves a model instance from the DI container by either FQCN or short class name.
     * Tries a direct container lookup first; falls back to getByShortName() for plain strings.
     *
     * @param  string $classRef  FQCN (e.g. Dorguzen\Models\Orders) or short name (e.g. 'Orders')
     * @return object
     * @throws \RuntimeException if the class cannot be resolved
     */
    private function resolveClass(string $classRef): object
    {
        $c = container();

        if ($c->has($classRef)) {
            return container($classRef);
        }

        $resolved = $c->getByShortName($classRef);
        if ($resolved !== null) {
            return $resolved;
        }

        throw new \RuntimeException(
            "DGZ relationship: cannot resolve '{$classRef}' from the container. " .
            "Ensure it is registered in bootstrap/app.php or provide a FQCN."
        );
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
     * Returns the name of this model beginning in lowercase.
     * According to Dorguzen convention, you should name your models after the DB tables they represent 
     * with the first letter in lowercase, while the name of the model class should begin with an uppercase.
     * This is to the effect that when you see a model, you should assume its DB table is of the same name, 
     * but beginning in lowercase.
     *
     * For models that don’t follow the naming convention, simply set a string property in the model named 
     * 'table', and give it a value of your table if it is anything other than lcfirst(modelName).
     * For examle, if you don't want to name the table for the Users model as 'users', you can name the 
     * table whatever you want e.g 'app_users'. Just remember to create a $table property on the Users model 
     * to anounce this to Dorguzen like so: 
     * 
     *      protected string $table = 'app_users';
     * 
     * @return string
     */
    public function getTable()
    {
        $modelClass = get_class($this);

        // Check if the developer explicitly defined a $table property
        if (property_exists($this, 'table')) {
            return lcfirst($this->table);
        }

        // Fallback: derive table name from class name
        // Extract just the class name, e.g. "\Seo" & remove the leading slash
        $shortName = substr(strrchr($modelClass, '\\'), 1);
        return lcfirst($shortName);
    }



    /**
     * It it recommended to assign values to all fields on a model class after having initialized them 
     * with NULLs to avoid errors of number of parameters provided not matching the number of fields on 
     * the table. Obviously, make sure those NULL fields can actually accept a NULL in the DB
     *
     * @return bool|string
     */
    public function save()
    {
        $model = $this;
        $dbAdapter = $this->connect();
        $table = $model->getTable();

        // filter data
        $data = [];
        foreach ($this->data as $property => $value) {
            if (array_key_exists($property, $model->_columns)) {
                $data[$property] = $value;

                if (in_array($property, $this->passwordField)) {
                    $data['key'] = $this->getSalt();
                }
            }
        }

        // Delegate query prep to driver
        list($fields, $placeholders, $values) = $dbAdapter->prepareInsertOrUpdate($data, $this->passwordField);

        // Build SQL
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";

        // Execute using adapter
        $dbAdapter->execute($sql, $values);

        return $dbAdapter->insert_id();
    }



    /**
     * This method takes an optional 'where' array of 'fieldName' => 'value' pairs.
     * The data in this array will be added to the generated SQL's WHERE clause.
     * If the where array is empty, it will check the active object for the existence of
     * an ID field. If it finds the ID field, & its value is not null, it will use that
     * ID field as the record to update in the SQL where clause.
     * 
     * If the where clause is empty & there is no ID field on the object, return false
     *
     * @example:
     *         $products = container(Products::class);
     * 
     *         $products->products_authorized = 'yes';
     *         $products->products_authorized_date = date("Y-m-d H:i:s");
     *         $products->products_authorized_by = $authorizerId;
     *
     *         $where = ['products_id' => $adId];
     *         $updated = $products->update($where);
     * 
     *      OR
     * 
     *         $products = container(Products::class);
     * 
     *         $products->id = '$adId; 
     *         $products->products_authorized = 'yes';
     *         $products->products_authorized_date = date("Y-m-d H:i:s");
     *         $products->products_authorized_by = $authorizerId;
     * 
     *         $updated = $products->update(); 
     *
     * @param $where
     * @return bool|string
     */
    public function update($where = []) 
    { 
        $model = $this;
        $table = $model->getTable();

        // get the DBAdapter
        $db = $this->connect();
        $newData = [];
        $dataTypes = '';
        $where_clause = '';
        $where_values = [];

        foreach ($this->data as $property => $value) {    
            if (array_key_exists($property, $model->_columns)) {

                // do not allow developers to insert into/update primary key field 
                if ($property != $this->getIdFieldName())
                {
                    $newData[$property] = $value;  
                }

                if (in_array($property, $this->passwordField)) {
                    $newData['key'] = $this->getSalt();
                    $dataTypes .= 'ss';
                }
                else {
                    $dataTypes .= $model->_columns[$property];
                }
            }
        }

        list( $fields, $placeholders, $values ) = $db->prepareInsertOrUpdate($newData, $this->passwordField, 'update');

        // build the where clause
        if (!empty($where))
        {
            $count = 0;
            foreach ($where as $field => $value )
            {
                if (array_key_exists($field, $model->_columns)) {
                    $dataTypes .= $model->_columns[$field];
                }

                if ( $count > 0 ) {
                    $where_clause .= ' AND ';
                }

                $where_clause .= $field . '=?';
                $where_values[] = $value;

                $count++;
            }
        }
        else if (
            ($this->getIdFieldName($model)) &&
            (array_key_exists($this->getIdFieldName($model), $this->data))
        )
        {
            $idFieldName = $this->getIdFieldName($model);
            $dataTypes .= $model->_columns[$idFieldName];

            $where_clause .= $idFieldName . '=?';
            $where_values[] = $this->data[$idFieldName];
        }
        else
        {
            //There is nothing to update, so return false
            return false;
        }

        $values = array_merge($values, $where_values);
        $sql = "UPDATE {$table} SET {$placeholders} WHERE {$where_clause}";

        return $db->execute($sql, $values);
    }





    /**
     * Delete a record by its field ID when you are not worried about managing fk constraints.
     * If you are worried about fk constraints; use the deleteWhere() method instead.
     * Call it like so:
     * 
     *      $userModel->deleteById('25', 'id');
     * 
     * But if the model table's id field is table-name prefixed (like 'tableName_id'), or you 
     *  specidied the id field of the table using an 'idField' property on the model, then you
     *  do not need to pass in the fieldName argument. Just call it like this:
     *  
     *      $userModel->deleteById('25');
     * 
     * 
     * @param int $idFieldValue the value to match on the table's given ID field
     * @param string $idFieldName if the id field is named something else-not 'id' or table-name prefixed,
     *     default '' (blank string). If this is not blank, this is the field that will be used as the ID field value. 
     * @return bool
     */
    public function deleteById(string $idFieldValue, string $idFieldName = ''): bool
    {
        $table = $this->getTable();
        $model = $this;
        $db = $this->connect();

        // Step 1: Decide which column name to use for deletion
        if ($idFieldName !== '') {
            $field = $idFieldName;
        }
        else 
        {
            $field = $this->getIdFieldName($model);
        }

        $sql = "DELETE FROM {$table} WHERE {$field} = ?";
        return $db->execute($sql, [$idFieldValue]);
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
    public function deleteWhere($criteria = [])
    {
        // get the DGZ_DBAdapter
        $db = $this->connect(); 
        $table = $this->getTable();

        // Step 1: Handle children first (if _hasChild is defined)
        if (!empty($this->_hasChild)) {
            foreach ($criteria as $field => $value) {
                if (!array_key_exists($field, $this->_columns)) {
                    return "The field {$field} does not exist in the {$table} table.";
                }

                foreach ($this->_hasChild as $childClass => $childFkField) {
                    $childModel = $this->resolveClass($childClass);

                    // Resolve optional FK: default to strtolower(ThisClass)_id
                    if (empty($childFkField)) {
                        $childFkField = strtolower($this->shortName(static::class)) . '_id';
                    }

                    $childColumns = $childModel->getColumnDataTypes();

                    if (!array_key_exists($childFkField, $childColumns)) {
                        return "The field {$childFkField} does not exist in the {$childModel->getTable()} table.";
                    }

                    // Build WHERE and execute deletion on the child table
                    $childTable = $childModel->getTable();
                    $where = [$childFkField => $value];
                    $this->delete($childTable, $where);
                }
            }
        }

        // Step 2: Handle current table deletion
        foreach ($criteria as $field => $value) {
            if (!array_key_exists($field, $this->_columns)) {
                return "The field {$field} does not exist in the {$table} table.";
            }
        }

        $this->delete($table, $criteria);
        return true;
    }





    /**
     * Executes a SQL query through the database adapter.
     * Works for SELECT, INSERT, UPDATE, DELETE, and others.
     * If $params are provided, it runs as a prepared statement.
     * 
     * @param $query //just pass it your SQL query string
     * @return mixed //returns true if you are updating of deleting, it returns the last inserted ID if you are inserting, 
     *          it returns the result set if you are selecting, it returns false if the operation fails.
     */
    public function query(string $sql, array $params = [])
    {
        $db = $this->connect();

        try {
            // Run the query via the adapter (driver handles prepared statements)
            $result = $db->query($sql, $params);

            // If the driver’s query() returns rows, just return them
            if (is_array($result) && !empty($result)) {
                return $result;
            }

            // Otherwise, for INSERT/UPDATE/DELETE, check affected rows or insert ID
            if (stripos($sql, 'INSERT') === 0) {
                return $db->insert_id();
            }

            if (stripos($sql, 'UPDATE') === 0 || stripos($sql, 'DELETE') === 0) {
                return true;
            }

            return false;
        } catch (Exception $e) {
            throw new Exception("Query failed: " . $e->getMessage());
        }
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
     *         $orderBy is used in any case, as long as it is not blank.
     * Here is an example of how to call it:
     *    $user = container(Users::class);
     *    $where = ['users_id' => $userId];
     *    $thisUser = $user->selectWhere(['users_phone_number', 'users_mobile_money_account'], $where);
     * 
     * Example usage:
     *      $user = container(Users::class);
     *
     *      // 1. Simple WHERE
     *       $result = $user->selectWhere([], ['users_id' => 5]);
     *
     *       // 2. Select specific columns
     *       $result = $user->selectWhere(['users_name', 'users_email'], ['users_status' => 'active']);
     *
     *       // 3. With ordering
     *       $result = $user->selectWhere(['users_name'], [], 'ORDER BY users_name ASC');
     * 
     * @param array $columns
     * @param array $criteria
     * @param string $orderBy
     * @return array|bool|string
     */
    public function selectWhere(array $columns = [], array $criteria = [], string $orderBy = '')
    {
        $db = $this->connect();
        $model = $this;
        $table = strtolower($model->getTable());
        $colDataTypes = $model->getColumnDatatypes();

        $columnsToSelect = [];
        $whereParts = [];
        $params = [];

        // ------------------------------
        // 1️⃣ Validate and determine columns to select
        // ------------------------------
        if (!empty($columns)) {
            foreach ($columns as $column) {
                if (!array_key_exists($column, $colDataTypes)) {
                    dgzie(
                    "The field {$column} does not exist in the {$table} table",
                    DGZ_Exception::FIELD_NOT_FOUND_ON_TABLE,
                    "Field not found on table. Make sure it exists."
                    );
                }
                $fields_to_select[] = $column;
            }
        } else {
            // Default: all columns
            $fields_to_select = array_keys($colDataTypes);
        }

        // ------------------------------
        // 2️⃣ Validate and collect criteria
        // ------------------------------
        if (!empty($criteria)) {
            foreach ($criteria as $key => $value) {
                if (!array_key_exists($key, $colDataTypes)) {
                    dgzie(
                    "The field {$key} does not exist in the {$table} table",
                    DGZ_Exception::FIELD_NOT_FOUND_ON_TABLE,
                    "COLUMNS",
                    $columns,
                    'CRITERIA',
                    $criteria
                    );
                }
                $where[$key] = $value;
            }
        }

        // ------------------------------
        // 3️⃣ Build SQL dynamically
        // ------------------------------
        $columns_sql = implode(', ', $fields_to_select);
        $sql = "SELECT {$columns_sql} FROM {$table}";

        if (!empty($whereParts)) {
            $sql .= " WHERE " . implode(' AND ', $whereParts);
        }

        if (!empty($where)) {
            $clauses = [];
            foreach ($where as $field => $value) {
                $clauses[] = "{$field} = ?";
                $params[] = $value;
            }
            $sql .= " WHERE " . implode(' AND ', $clauses);
        }

        if (!empty($orderBy)) {
            // Ensure the orderBy is safe (basic whitelist pattern)
            if (!preg_match('/^ORDER BY\s+[a-zA-Z0-9_ ,]+(ASC|DESC)?$/i', $orderBy)) {
                dgzie(
                    "Invalid ORDER BY clause",
                    'REQUEST SQL: '. $sql,
                    "The SQL query 'SELECT FROM {$table} ...' ORDER BY clause format is wrong. Check it."
                );
            }
            $sql .= " {$orderBy}";
        }

        // ------------------------------
        // 4️⃣ Run query via adapter
        // ------------------------------
        $results = $db->query($sql, $params);

        // ------------------------------
        // 5️⃣ Return results
        // ------------------------------
        if (is_array($results) && !empty($results)) {
            return $results;
        }

        return false;
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
        $db = $this->connect();
        $model = $this;
        $table = $model->getTable();
        $dataClean = [];

        // ✅ 1. Filter valid columns & handle password field logic
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $model->_columns)) {
                $dataClean[$key] = $value;

                // If this column is a password field, handle hashing/salt logic
                if (in_array($key, $this->passwordField)) {
                    $dataClean['key'] = $this->getSalt();
                }
            }
        }

        // ✅ 2. Prepare SQL parts using the adapter helper
        list($fields, $placeholders, $values) = $db->prepareInsertOrUpdate(
            $dataClean,
            $this->passwordField,
            'insert'
        );

        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$placeholders})";

        try {
            // ✅ 3. Execute via adapter (uses driver internally)
            $success = $db->execute($sql, array_values($values));

            if ($success) {
                // ✅ 4. Return insert ID from adapter
                return $db->insert_id();
            }

            return false;
        }
        catch (Exception $e) {
            // ✅ 5. Handle duplicate entry (MySQL error 1062)
            if (str_contains($e->getMessage(), '1062')) {
                return '1062';
            }

            // Otherwise, let the global error handler or logger catch it
            throw $e;
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
     * $updated = $blog->updateObject($data, $where);
     *
     * @param array $data an array of 'fieldName' => 'value' pairs for the DB table fields to be updated
     * @param array $where. An array of 'key' - 'value' pairs which will be used to build the 'WHERE' clause
     * @return bool
     */
    public function updateObject($data, $where)
    {
        $model = $this;
        $table = $model->getTable();
        $db = $this->connect();

        $dataClean = [];

        // ✅ 1. Filter valid columns & handle password field logic
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $model->_columns)) {

                // do not allow developers to insert into/update primary key field 
                if ($key != $this->getIdFieldName())
                {
                    $dataClean[$key] = $value;  
                }

                // Handle AES_ENCRYPT() salt for password fields
                if (in_array($key, $this->passwordField)) {
                    $dataClean['key'] = $this->getSalt();
                }
            }
        }

        // ✅ 2. Prepare SQL parts for update via driver helper
        list($fields, $placeholders, $values) = $db->prepareInsertOrUpdate(
            $dataClean,
            $this->passwordField,
            'update'
        );

        // ✅ 3. Build WHERE clause and merge params
        $whereClauseParts = [];
        $whereValues = [];
        foreach ($where as $field => $value) {
            $whereClauseParts[] = "{$field} = ?";
            $whereValues[] = $value;
        }

        $whereClause = implode(' AND ', $whereClauseParts);
        $params = array_merge($values, $whereValues);

        $sql = "UPDATE {$table} SET {$placeholders} WHERE {$whereClause}";

        // ✅ 4. Execute the update using the driver
        try {
            $success = $db->execute($sql, $params);

            // ✅ 5. Check affected rows
            return $success && $db->getAffectedRows() > 0;
        }
        catch (Exception $e) {
            throw $e;
        }
    }



    /**
     * You wouldn't call this method directly in code, but rather a method in your model prepares the args for this method & calls it
     * @return bool true or false for whether the deletion was successful or not
     */
    public function delete(string $table, array $where = []): bool
    {
        $db = $this->connect();

        // If no WHERE clause, perform a full table delete (TRUNCATE style)
        if (empty($where)) {
            $sql = "DELETE FROM {$table}";
            return $db->execute($sql);
        }

        // Step 1: Build WHERE clause
        $whereClauses = [];
        $params = [];

        foreach ($where as $field => $value) {
            $whereClauses[] = "{$field} = ?";
            $params[] = $value;
        }

        $whereSql = implode(' AND ', $whereClauses);

        // Step 2: Run deletion via adapter
        $sql = "DELETE FROM {$table} WHERE {$whereSql}";
        return $db->execute($sql, $params);
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
     * @param array $data array containing the username & password to authenticate the user with
     * @return array|bool It returns false if the login fails, or an array of all fields in your users table
     */
    public function authenticateUser(array $data)
    {
        $model = $this; 
        $table = $model->getTable();
        $db = $this->connect();
        $columns = $model->_columns;

        $emailField = '';
        $emailValue = '';
        $passwordField = '';
        $passwordValue = '';
        $salt = '';

        // ✅ Identify which fields are password vs email/username
        foreach ($data as $key => $value) {
            if (!array_key_exists($key, $columns)) {
                continue;
            }

            if (in_array($key, $this->passwordField)) {
                $passwordField = $key;
                $passwordValue = $value;
                $salt = $this->getSalt();
            } else {
                $emailField = $key;
                $emailValue = $value;
            }
        }

        if (!$emailField || !$passwordField) {
            throw new Exception("Missing required fields for authentication.");
        }

        $condition = $db->encryptPasswordCondition($passwordField);

        // ✅ Build SQL with DB driver-agnostic placeholders
        $sql = "SELECT * FROM {$table} 
            WHERE {$emailField} = ? 
            AND {$condition}";

        // ✅ Execute using driver abstraction
        $params = [$emailValue, $passwordValue, $salt];
        $rows = $this->query($sql, $params); 

        // ✅ Return user row or false
        return $rows ? $rows[0] : false;
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
        $model = $this;
        $table = $model->getTable(); 
        $db = $this->connect();
        $columns = $model->getColumnDataTypes();

        if ($orderBy == '')
        {
            $defaultOrderField = "{$table}_name";
            $orderClause = array_key_exists($defaultOrderField, $columns)
            ? " ORDER BY {$defaultOrderField}"
            : '';
        }
        else
        {
            $orderClause = " ORDER BY {$orderBy}";
        }

        $query = "SELECT * FROM {$table}{$orderClause}";

        $rows = $db->query($query);

        return $rows ?? [];
    }


    /**
     * Sometimes this parent model needs to know what field is the ID field
     * of a child model 
     *
     * @param $model the model whose ID field we want to know
     *
     * @return mixed a string of the ID field name, or false if not found
     */
    public function getIdFieldName($model = null)
    { 
        if ($model == null)
        {
            $model = $this;
        }

        $adapter = $this->connect(); 
        $table = $model->getTable();
        $idFieldName = false;

        if (property_exists($model, 'id') && $model->id !== '')
        {
            // Developer explicitly defined the primary key
            $idFieldName = $model->id;
        }
        else if (isset($model->_columns["id"]))
        {
            // if the model's table has an 'id' field, use it
            $idFieldName = 'id';
        }
        else if (isset($model->_columns["{$table}_id"]))
        {
            // Conventional fallback (tableName_id)
            $idFieldName = $table.'_id';
        }
        else 
        {
            // Dynamic fallback (try to auto-detect from DB schema)
            $pk = $adapter->getPrimaryKeyField($table);
            if ($pk) {
                $idFieldName = $pk;
            }
        }

        return $idFieldName;
    }



    /**
     * This assumes that you follow the DGZ convention of having the following fields 
     * among the fields of your table:
     *      -tableName_id
     *      -tableName_name
     * 
     * Optionally pass in a language suffix, if the the field has a language suffix
     * (e.g. _en for English)
     */
    public function getNameFromId(int $id, string $lang = ''): string|false
    {
        $model = $this;
        $table = $model->getTable();
        $db = $this->connect();

        $fieldName = $lang !== ''
        ? "{$table}_name_{$lang}"
        : "{$table}_name";

        $sql = "SELECT {$fieldName} FROM {$table} WHERE {$table}_id = ?";
        $params = [$id];


        $result = $db->query($sql, $params);

        if ($result && isset($result[0][$fieldName])) {
            return $result[0][$fieldName];
        }
            return false;
    }



    /**
     * Grab everything from a record using its ID. If you did not follow the DGZ convention of prefixing
     * your table field names with the 'tablename_', you can pass it the actual id field name in $fieldName
     * If you did, just pass true to the second argument.
     * If however you pass only one argument-the actual id field value, this method will check if you
     * specified the id field of your model in a 'idField' property & use it, otherwise, it assumes that
     * your model table's id field is just 'id'.
     *
     * @param int $id the record value needed to be fetched from the table's ID field
     * @param string $idFieldName if the id field is named something else-not 'id' or table name-prefixed,
     *     default '' (blank string). If this is not blank, this is the field that will be used as the ID field. Use it to
     *     override all other choices and specify the exact table field name to match.
     * @return mixed
     */
    public function getById(int $id, string $idFieldName = ''): array|false
    {
        $table = $this->getTable();
        $db = $this->connect();
        $model = $this;

        // Determine the correct ID field
        if ($idFieldName !== '') {
            $idField = $idFieldName;
        } 
        else 
        {
            $idField = $this->getIdFieldName($model);
        }

        $sql = "SELECT * FROM {$table} WHERE {$idField} = ?";
        $params = [$id];

        $rows = $db->query($sql, $params);

        return $rows ? $rows[0] : false;
    }


    /**
     * Only use this method if you followed the DGZ table-naming convention of prefixing your table fields with 'tablename_'
     * This method could be useful for records like users, or locations etc which have names you might need to fetch.
     * It returns $result[0][$table.'_id'] The ID of the record having the given name, or false if no match is found.
     *
     * @param $name the name of the record whose ID you want to know
     *
     * @return mixed
     */
    public function getIdFromName(string $name): array|false
    {
        $db = $this->connect();
        $table = $this->getTable();
        $model = $this;

        // Determine the correct ID field
        if (property_exists($model, 'idField')) {
            $idField = $model->idField;
        }
        else 
        {
            $idField = "{$table}_id";
        }

        $sql = "SELECT {$idField} FROM {$table} WHERE {$table}_name = ?";
        $params = [$name];

        $rows = $db->query($sql, $params);

        return $rows ? $rows[0] : false;
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
        $model = $this;
        $table = $model->getTable();
        $db = $this->connect();

        $query = "SELECT * FROM {$table} WHERE {$table}_name = ?";
        $params = [$recordName];

        $result = $db->query($query);

        return $result ?? [];
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
    public function getDefaultRecord(): array|false
    {
        $model = $this; 
        $table = $model->getTable();
        $db = $this->connect();
        $sql = "SELECT * FROM ".$table." WHERE ".$table."_default_record = 1";

        $rows = $db->query($sql);

        return $rows ? $rows[0] : false;
    }



    /**
     * This method takes only 1 mandatory argument; the columns of the fields to grab
     * The other three ($where, $order and $sort) are optional
     *
     * This is for situations where you want to grab only selected fields from the DB and nothing else
     * 
     * Example Usage
     *      // Example 1: select some fields
     *      $users = $userModel->selectOnly(['user_id', 'user_name']);
     *      
     *      // Example 2: select with WHERE
     *      $admins = $userModel->selectOnly(['user_id', 'user_email'], ['role' => 'admin']);
     *      
     *      // Example 3: ordered results
     *      $recent = $userModel->selectOnly(['blog_id', 'blog_title'], null, 'created_at', 'DESC');
     *
     * @param array $columns of fields to grab
     * @param array $where optional, where the keys are column names, & the values are the values the columns have to match to be selected
     * @param string $order - string which should be the column name you wish the results to be ordered by e.g. 'username'
     * @param string $sort - a string of the the type of sorting you want done on the result e.g. 'ASC' or 'DESC'
     *
     * @return mixed
     */
    public function selectOnly(array $columns, ?array $where = null, string $order = '', string $sort = 'ASC'): array|string|false
    {
        $model = $this;
        $db = $this->connect();
        $table = $model->getTable();
        $columnTypes = $model->getColumnDataTypes();

        // ✅ Validate that all requested columns exist in the table schema
        foreach ($columns as $column) {
            if (!array_key_exists($column, $columnTypes)) {
                return "The field '{$column}' does not exist in the '{$table}' table.";
            }
        }

        // ✅ Columns to select
        $columns_needed = implode(',', $columns);

        // ✅ Default ORDER BY clause
        $orderBy = $order !== ''? $order : $columns[0];
        $sort = strtoupper($sort) === 'DESC' ? 'DESC' : 'ASC'; 

        // ✅ Build WHERE clause safely
        $whereClause = '';
        $params = [];

        if (!empty($where)) {
            $conditions = [];
            foreach ($where as $field => $value) {
                // Skip invalid fields
                if (!array_key_exists($field, $columnTypes)) continue;

                $conditions[] = "{$field} = ?";
                $params[] = $value;
            }

            if (!empty($conditions)) {
                $whereClause = 'WHERE ' . implode(' AND ', $conditions);
            }
        }

        // ✅ Final SQL
        $sql = "SELECT {$columns_needed} FROM {$table} {$whereClause} ORDER BY {$orderBy} {$sort}";

        // ✅ Execute using adapter
        $result = $db->query($sql, $params);

        return $result ?: false;
    }



    /**
     * Get the total number of records in this table
     *
     * @return mixed
     */
    public function getCount()
    { 
        $model = $this;
        $table = $model->getTable();
        $db = $this->connect();
        $query = "SELECT COUNT(*) AS number FROM $table";

        $total = $db->query($query);
        if ($total)
        {
            $totalRecs = $total[0]['number'];

            return $totalRecs;
        }
    }



    public function getPaginated($start, $numPerPage)
    {
        $model = $this;
        $table = $model->getTable();
        $db = $this->connect();
        $query = "SELECT * FROM $table LIMIT $start, $numPerPage";


        $chunk = $db->query($query);

        if ($chunk)
        {
            return $chunk;
        }
    }



    public function timeNow(): string
    {
        return date("Y-m-d H:i:s");
    }
}




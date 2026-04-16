<?php

namespace Dorguzen\Tests\Manual;


use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model as Model;


class UsersPostgresTestModel extends Model {
    protected $table = 'users';

    protected $_columns = array();

    protected $data = [];


    protected $_hasParent = [];


    protected $_hasChild = [];


    public function __construct(?Config $config)
    {
        return parent::__construct($config);
    }
}
<?php

namespace Dorguzen\Models;

use Dorguzen\Config\Config;
use Dorguzen\Core\DGZ_Model;

class Refresh_tokens extends DGZ_Model
{
    protected $_columns = [];
    protected $data     = [];
    protected $id       = 'refresh_tokens_id';
    protected $table    = 'dgz_refresh_tokens';

    public function __construct(Config $config)
    {
        parent::__construct($config);
    }
}

<?php 

use Dorguzen\Core\Kernel\CliKernel;


// Ensure errors are visible in CLI
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Autoload classes
require_once __DIR__ . '/../../vendor/autoload.php';

// Boot the CLI kernel
$kernel = new CliKernel();
$kernel->bootstrap();
<?php

$basePath = __DIR__.'/../';

require_once $basePath.'vendor/autoload.php';

use Voxus\Core\connection\DB;
use Voxus\Core\connection\sqlite;

$dotenv = Dotenv\Dotenv::createImmutable($basePath);
$dotenv->load();

$sqlitePath = $basePath.'database/sqlite/'.getenv('DATABASE_NAME').'.sqlite3';
DB::init(new sqlite($sqlitePath));




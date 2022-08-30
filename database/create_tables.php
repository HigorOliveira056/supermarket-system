<?php
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(str_replace('\database', '', __DIR__));
$dotenv->load();

use InfraDataBase\Connection;
use InfraDataBase\Table;

$conn = new Connection;

$table = new Table('product', $conn);

$table->setPrimaryKey('id', 'int');
$table->setColumn('description', 'varchar(50)');

$table->down();

<?php
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(str_replace('\database', '', __DIR__));
$dotenv->load();

use InfraDataBase\Connection;
use InfraDataBase\Table;

$conn = new Connection;

// table type product
$tableCategories = new Table('category_product', $conn);
$tableCategories->setPrimaryKey('id', 'int identity(1,1)');
$tableCategories->setColumn('name', 'varchar(50) not null');
$tableCategories->setColumn('description', 'varchar(255)');
$tableCategories->up();

// table taxes
$tableTaxes = new Table('taxes', $conn);
$tableTaxes->setPrimaryKey('id', 'int identity(1,1)');
$tableTaxes->setColumn('name', 'varchar(50) not null');
$tableTaxes->up();

// table  type product - taxes
$tableTaxesTypeProduct = new Table('category_product_taxes', $conn, false);
$tableTaxesTypeProduct->setForeignKey('category_product_id', 'int not null', 'category_product', 'id');
$tableTaxesTypeProduct->setForeignKey('taxe_id', 'int not null', 'taxes', 'id');
$tableTaxesTypeProduct->setColumn('percentual', 'float not null');
$tableTaxesTypeProduct->up();

// table product
$tableProduct = new Table('product', $conn);
$tableProduct->setPrimaryKey('id', 'int identity(1,1)');
$tableProduct->setForeignKey('category_id', 'int not null', 'category_product', 'id');
$tableProduct->setColumn('name', 'varchar(50) not null');
$tableProduct->setColumn('description', 'varchar(255) not null');
$tableProduct->setColumn('price', 'money not null');
$tableProduct->up();

<?php
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(str_replace('\database', '', __DIR__));
$dotenv->load();

use InfraDataBase\Connection;
use InfraDataBase\Table;

$conn = new Connection;

try {
    // table categories
    $tableCategories = new Table('category_product', $conn);
    $tableCategories->setPrimaryKey('id', 'int identity(1,1)');
    $tableCategories->setColumn('name', 'varchar(50) not null');
    $tableCategories->setColumn('description', 'varchar(255)');
    $tableCategories->up();
    
    // table taxes
    $tableTaxes = new Table('taxes', $conn);
    $tableTaxes->setPrimaryKey('id', 'int identity(1,1)');
    $tableTaxes->setColumn('name', 'varchar(50) not null');
    $tableTaxes->setColumn('percentual', 'float not null');
    $tableTaxes->up();
    
    // table pivot categories - taxes
    $tableTaxesTypeProduct = new Table('category_product_taxes', $conn, false);
    $tableTaxesTypeProduct->setForeignKey('category_product_id', 'int not null', 'category_product', 'id');
    $tableTaxesTypeProduct->setForeignKey('taxe_id', 'int not null', 'taxes', 'id');
    $tableTaxesTypeProduct->up();
    
    // table product
    $tableProduct = new Table('product', $conn);
    $tableProduct->setPrimaryKey('id', 'int identity(1,1)');
    $tableProduct->setForeignKey('category_id', 'int not null', 'category_product', 'id');
    $tableProduct->setColumn('name', 'varchar(50) not null');
    $tableProduct->setColumn('description', 'varchar(255) not null');
    $tableProduct->setColumn('price', 'money not null');
    $tableProduct->up();

    //table client
    $tableClient = new Table('client', $conn);
    $tableClient->setPrimaryKey('id', 'int identity(1,1)');
    $tableClient->setColumn('name', 'varchar(255) not null');
    $tableClient->setColumn('email', 'varchar(255) not null');
    $tableSales->up();


    //table seller
    $tableSeller = new Table('seller', $conn);
    $tableSeller->setPrimaryKey('id', 'int identity(1,1)');
    $tableSeller->setColumn('name', 'varchar(255) not null');
    $tableSeller->setColumn('email', 'varchar(255) not null');
    $tableSales->up();

    // table sales
    $tableSales = new Table('product', $conn);
    $tableSales->setPrimaryKey('id', 'int identity(1,1)');
    $tableSales->setColumn('seller', 'varchar(50) not null');
    $tableSales->setColumn('client', 'varchar(255) not null');
    $tableSales->setColumn('total_quantity_products', 'int not null');
    $tableSales->setColumn('total_price', 'money not null');
    $tableSales->up();

}catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}


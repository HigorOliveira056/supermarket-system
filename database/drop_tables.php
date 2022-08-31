<?php
require_once 'vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(str_replace('\database', '', __DIR__));
$dotenv->load();

use InfraDataBase\Connection;
use InfraDataBase\Table;

$conn = new Connection;

try {
    $tableProduct = new Table('product', $conn);
    $tableProduct->down();
    
    $tableTaxesTypeProduct = new Table('category_product_taxes', $conn);
    $tableTaxesTypeProduct->down();
    
    $tableCategories = new Table('category_product', $conn);
    $tableCategories->down();
    
    $tableTaxes = new Table('taxes', $conn);
    $tableTaxes->down();
}catch(\Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}


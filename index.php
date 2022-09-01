<?php
require_once "vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

use App\Domain\CategoryProducts;
use App\Domain\Taxes;
use App\Domain\Product;
use App\Domain\Sales;
use App\Domain\SalesProduct;
use App\Domain\Client;
use App\Domain\Seller;
use App\Repository\CategoryProductsRepository;
use App\Repository\TaxesRepository;
use App\Repository\ProductRepository;


$taxes = new Taxes;
$taxes->id = 1;
$taxes->name = 'ICMS';
$taxes->percentual = 18;

// $respositoryTaxes = new TaxesRepository;
// $respositoryTaxes->save($taxes);

$category = new CategoryProducts;
$category->id = 2;
$category->description = 'computadores';
$category->name = 'tecnologia';
$category->addTax($taxes);


// $respository = new CategoryProductsRepository;
// $respository->save($category);

$product = new Product;
$product->id = 1;
$product->name = 'celular';
$product->description = 'Samsung A2';
$product->price = 1200.00;
$product->category = $category;

$repository_product = new ProductRepository;
// $repository_product->delete($product);
// $repository_product->update($product);
// $repository_product->save($product);
foreach ($repository_product->getAll() as $prod) {
    echo $prod->toJson();
}


// $client = new Client;
// $seller = new Seller;

// $sales = new Sales;
// $sales->id = 2;
// $sales->seller = $seller;
// $sales->client = $client;
// $sales_product = new SalesProduct;
// $sales_product->insertProduct($sales, $product, 1);

// $sales_product2 = new SalesProduct;
// $sales_product2->insertProduct($sales, $product, 1);

// $sales->addProduct($sales_product);
// $sales->addProduct($sales_product2);

// echo $sales->toJson();
// echo $product->getPrice(), PHP_EOL, $product->getTotalTaxes(), PHP_EOL, $product->percentualTaxes();

die;

setlocale(LC_ALL, 'pt_BR.utf8');
date_default_timezone_set('America/Sao_Paulo');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once 'routes/api.php';

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

$pos = strpos($uri, '?');
if ($pos !== false) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        http_response_code(404);
    break;
    case FastRoute\Dispatcher::FOUND:
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];
        header('Content-Type: application/json');
        $response = $handler($vars);
        if ($response instanceof App\Services\Json)
            echo $response;
    break;
}
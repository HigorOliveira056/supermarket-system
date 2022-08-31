<?php

use App\Controllers\ProductController;
use App\Controllers\CategoryProductsController;
use App\Controllers\TaxesController;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $route) {
    $route->addGroup('/product', function (FastRoute\RouteCollector $route_group) {
        $route_group->get('/', ProductController::class . '::' . 'showAll');
        $route_group->get('/{id:[0-9]+}', ProductController::class . '::' . 'show');
        $route_group->post('/', ProductController::class . '::' . 'save');
        $route_group->put('/{id:[0-9]+}', ProductController::class . '::' . 'update');
        $route_group->delete('/{id:[0-9]+}', ProductController::class . '::' . 'delete');
    });

    $route->addGroup('/category', function (FastRoute\RouteCollector $route_group) {
        $route_group->get('/', CategoryProductsController::class . '::' . 'showAll');
        $route_group->get('/{id:[0-9]+}', CategoryProductsController::class . '::' . 'show');
        $route_group->post('/', CategoryProductsController::class . '::' . 'save');
        $route_group->put('/{id:[0-9]+}', CategoryProductsController::class . '::' . 'update');
        $route_group->delete('/{id:[0-9]+}', CategoryProductsController::class . '::' . 'delete');
    });

    $route->addGroup('/taxes', function (FastRoute\RouteCollector $route_group) {
        $route_group->get('/', TaxesController::class . '::' . 'showAll');
        $route_group->get('/{id:[0-9]+}', TaxesController::class . '::' . 'show');
        $route_group->post('/', TaxesController::class . '::' . 'save');
        $route_group->put('/{id:[0-9]+}', TaxesController::class . '::' . 'update');
        $route_group->delete('/{id:[0-9]+}', TaxesController::class . '::' . 'delete');
    });
});

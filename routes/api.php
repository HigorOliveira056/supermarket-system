<?php

use App\Controllers\ProductController;

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $route) {
    $route->addGroup('/product', function (FastRoute\RouteCollector $route_group) {
        $route_group->get('/', ProductController::class . '::' . 'showAll');
        $route_group->get('/{id:[0-9]+}', ProductController::class . '::' . 'show');
        $route_group->post('/', ProductController::class . '::' . 'save');
        $route_group->put('/{id:[0-9]+}', ProductController::class . '::' . 'update');
        $route_group->delete('/{id:[0-9]+}', ProductController::class . '::' . 'delete');
    });
});

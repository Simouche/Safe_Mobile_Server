<?php
require_once "Autoload.php";

$router = new Router(new Request);

$router->get('/get-all-clients', function () {
    return Client::getClients();
});

$router->get('/synchronize-clients', function ($request) {
    return Client::synchronizeClients($request);
});


$router->get('/get-all-products', function () {
    return Product::getProducts();
});

$router->get('/get-all-providers', function () {
    return Provider::getProviders();
});

$router->post('/add-products', function (IRequest $request) {
    return Product::addProducts($request);
});

$router->post('/add-purchases', function (IRequest $request) {
    error_log("hit\n", 3, "logs/access.txt");
    return Purchase::addPurchases($request);
});

//$router->
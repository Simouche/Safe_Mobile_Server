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

$router->get('/get-all-providers',function (){
    return Provider::getProviders();
});

//$router->
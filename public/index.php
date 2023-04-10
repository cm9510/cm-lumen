<?php
$app = require_once __DIR__.'/../bootstrap/app.php';

# Load Routes
$app->router->get('/', function () use ($app){
    return $app->router->app->version();
});
$app->router->group(['namespace' => 'App\Http\Controllers\Admin','prefix'=>'a'], function ($router) {
    require __DIR__.'/../routes/admin.php';
});
$app->router->group(['namespace' => 'App\Http\Controllers\Api','prefix'=>'c'], function ($router) {
    require __DIR__.'/../routes/api.php';
});
$app->router->group(['namespace' => 'App\Http\Controllers', 'prefix'=>'m'], function ($router) {
    require __DIR__.'/../routes/common.php';
});

$app->run();

<?php
/** @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group(['namespace'=> 'Admin', 'prefix'=> 'w'], function ($self) {
    $self->get('hello', 'Home@hello');
    $self->get('page', 'Home@page');
});

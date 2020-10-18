<?php
require "../core/boostrap.php";

use Voxus\Core\routing\Router;


$router = new Router();
$router->get('/users',\Voxus\App\Controller\UserController::class,'getAll');
$router->get('/user/{id}',\Voxus\App\Controller\UserController::class,'get');
$router->post('/create_user',\Voxus\App\Controller\UserController::class,'post');

$router->register();

<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\UserController;
use Streamline\Core\Router;

use App\Controllers\HomeController;

$router = new Router();

$router->get('/', HomeController::class . ':index')->alias('home');
$router->get('/user/register', UserController::class . ':register')->alias('user_register');
$router->post('/user/register', UserController::class . ':store')->alias('user_store');
$router->get('/user/[id:%d]', UserController::class . ':show')->alias('user_show', ['id' => 2]);

$router->run();
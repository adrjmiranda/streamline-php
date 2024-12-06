<?php

use App\Controllers\ErrorController;

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(rootPath());
$dotenv->load();

use App\Controllers\UserController;
use Streamline\Core\Router;

use App\Controllers\HomeController;

$router = new Router();

$router->setErrorContent(404, ErrorController::class . ':notFound');
$router->setErrorContent(500, ErrorController::class . ':serverError');

$router->get('/', HomeController::class . ':index')->alias('home');
$router->get('/user/register', UserController::class . ':register')->alias('user_register');
$router->post('/user/register', UserController::class . ':store')->alias('user_store');

$router->get('/user/[id:%d]', UserController::class . ':show');
$router->get('/user/edit/[id:%d]', UserController::class . ':edit');
$router->post('/user/edit/[id:%d]', UserController::class . ':update');

$router->get('/user/delete/[id:%d]', UserController::class . ':delete');

$router->run();
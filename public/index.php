<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(rootPath());
$dotenv->load();

// Router
use Streamline\Core\Router;

// Controllers
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\ErrorController;

// Middlewares
use App\Middlewares\CacheMiddleware;
use App\Middlewares\StoreCacheMiddleware;

$router = new Router();

$router->setErrorContent(404, ErrorController::class . ':notFound');
$router->setErrorContent(500, ErrorController::class . ':serverError');

$router->get('/', HomeController::class . ':index')->addMiddleware(CacheMiddleware::class)->addMiddleware(StoreCacheMiddleware::class)->alias('home');
$router->get('/user/register', UserController::class . ':register')->alias('user_register');
$router->post('/user/register', UserController::class . ':store')->alias('user_store');

$router->get('/user/[id:%d]', UserController::class . ':show');
$router->get('/user/edit/[id:%d]', UserController::class . ':edit');
$router->post('/user/edit/[id:%d]', UserController::class . ':update');

$router->get('/user/delete/[id:%d]', UserController::class . ':delete');

$router->get('/user/login', UserController::class . ':login')->alias('user_login');
$router->post('/user/auth', UserController::class . ':auth')->alias('user_auth');

// Run router
$router->run();
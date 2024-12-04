<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Middlewares\MaintenanceMiddleware;
use Streamline\Core\Router;

use App\Controllers\HomeController;
use App\Middlewares\HomeMiddleware;

$router = new Router();

$router->get('/', HomeController::class . ':index')->alias('home');

$router->run();
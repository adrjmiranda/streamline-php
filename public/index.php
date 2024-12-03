<?php

require_once __DIR__ . '/../vendor/autoload.php';

// Routing
use App\Middlewares\SecondMiddleware;
use App\Middlewares\ThirdMiddleware;
use Streamline\Core\Router;

// Middlewares
use App\Middlewares\MainMiddleware;
use App\Middlewares\TestMiddleware;
use App\Middlewares\FirstMiddleware;

//Controllers
use App\Controllers\TestController;

$router = new Router();

$router->post('/store/[id:%d]', TestController::class . ':store')->addMiddleware(TestMiddleware::class);

$router->get('/', TestController::class . ':index')->addMiddleware(TestMiddleware::class)->addMiddleware(MainMiddleware::class);
$router->get('/show', TestController::class . ':show')->addMiddleware(TestMiddleware::class);
$router->delete('/delete', TestController::class . ':delete')->addMiddleware(TestMiddleware::class);

$router->group('/add', function (Router $router): void {
  $router->post('/users/[name:%t]', TestController::class . ':store')->addMiddleware(TestMiddleware::class);
  $router->post('/users', TestController::class . ':store')->addMiddleware(TestMiddleware::class);
}, [FirstMiddleware::class, SecondMiddleware::class, ThirdMiddleware::class]);

$router->group('/add', function (Router $router): void {
  $router->post('/posts/[name:%t]', TestController::class . ':store')->addMiddleware(TestMiddleware::class);
  $router->post('/posts', TestController::class . ':store')->addMiddleware(TestMiddleware::class);
}, [FirstMiddleware::class, SecondMiddleware::class]);

$router->run();
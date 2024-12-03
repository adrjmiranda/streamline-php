<?php

require_once __DIR__ . "/../vendor/autoload.php";

// Routing
use Streamline\Core\Router;

// Middlewares
use App\Controllers\MainMiddleware;
use App\Controllers\TestMiddleware;

//Controllers
use App\Controllers\TestController;

$app = new Router();

$app->post("/store/[id:%d]", TestController::class . ":store")->addMiddleware(TestMiddleware::class);
// $app->post("/users/[name:%t]", TestController::class . ":store")->addMiddleware(TestMiddleware::class);
// $app->post("/users/adriano", TestController::class . ":store")->addMiddleware(TestMiddleware::class);
$app->get("/", TestController::class . ":index")->addMiddleware(TestMiddleware::class)->addMiddleware(MainMiddleware::class);
$app->get("/show", TestController::class . ":show")->addMiddleware(TestMiddleware::class);
$app->delete("/delete", TestController::class . ":delete")->addMiddleware(TestMiddleware::class);

$app->run();
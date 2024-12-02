<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Src\Routing\Router;

$app = new Router();

$app->get("/", TestController::class . ":index")->addMiddleware("TestMiddleware");
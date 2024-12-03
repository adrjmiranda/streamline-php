<?php

namespace Streamline\Core;

use Exception;
use Streamline\Routing\DynamicRouteValidator;
use Streamline\Routing\Request;
use Streamline\Routing\Response;
use Streamline\Routing\Route;
use Streamline\Routing\RouteCollection;
use Streamline\Routing\RouteRules;
use Streamline\Routing\StaticRouteValidator;

/**
 * Class responsible for managing all application routes and performing 
 * the final processing of the request and response.
 * 
 * @package Src\Routing
 */
class Router
{
  private Request $request;
  private Response $response;
  private array $args = [];


  public function __construct()
  {
    $this->request = new Request();
    $this->response = new Response();
    $this->args = [];
  }

  private function add(string $method, string $uri, string $handle): Route
  {
    [$controllerNamespace, $action] = explode(':', $handle);

    if (!class_exists($controllerNamespace)) {
      throw new Exception("Controller {$controllerNamespace} does not exist", 500);
    }

    if (!method_exists($controllerNamespace, $action)) {
      throw new Exception("The {$method} method does not exist in the {$controllerNamespace} controller", 500);
    }

    $route = new Route($method, $uri, $controllerNamespace, $action);

    if (DynamicRouteValidator::containsDynamicSegment($uri)) {
      DynamicRouteValidator::validateAndAddRoute($uri, $route);
    } else {
      StaticRouteValidator::validateAndAddRoute($uri, $route);
    }

    return $route;
  }

  public function __call(string $method, array $args): ?Route
  {
    $method = strtoupper($method);

    if (in_array($method, RouteRules::getEnabledMethods())) {
      if (!isset($args[0]) || !isset($args[1])) {
        throw new Exception("Two parameters are expected when defining the request method. Ex: \$app->get(string \$uri, string \$handle)", 500);
      }

      $uri = preg_replace('/\s*/', '', $args[0]);
      $handle = $args[1];

      return $this->add(strtoupper($method), $uri, $handle);
    } else {
      throw new Exception("Method Not Enabled", 400);
    }
  }

  public function run()
  {
    dd(RouteCollection::getStaticRoutes(), RouteCollection::getDynamicRoutes());
  }
}
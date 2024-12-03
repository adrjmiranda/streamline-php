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
 * @package Streamline\Core
 */
class Router
{
  /**
   * Request instance containing the request information
   * 
   * @var Request
   */
  private Request $request;

  /**
   * Response instance containing all response configurations
   * 
   * @var Response
   */
  private Response $response;

  /**
   * List of arguments that will be passed to the 
   * controller action
   * 
   * @var array
   */
  private array $args = [];


  /**
   * Method responsible for instantiating the Router 
   * class and initializing its properties
   */
  public function __construct()
  {
    $this->request = new Request();
    $this->response = new Response();
    $this->args = [];
  }

  /**
   * Method responsible for creating Route instance
   * 
   * @param string $method
   * @param string $uri
   * @param string $handle
   * @throws \Exception
   * @return Route
   */
  private function createRoute(string $method, string $uri, string $handle): ?Route
  {
    [$controllerNamespace, $action] = explode(':', $handle);

    if (!class_exists($controllerNamespace)) {
      throw new Exception("Controller {$controllerNamespace} does not exist", 500);
    }

    if (!method_exists($controllerNamespace, $action)) {
      throw new Exception("The {$method} method does not exist in the {$controllerNamespace} controller", 500);
    }

    $route = new Route($method, $uri, $controllerNamespace, $action);

    return $route;
  }

  /**
   * Method responsible for adding a Route instance to the route list
   * 
   * @param string $method
   * @param string $uri
   * @param string $handle
   * @return \Streamline\Routing\Route
   */
  private function add(string $method, string $uri, string $handle): Route
  {
    $route = $this->createRoute($method, $uri, $handle);

    if (DynamicRouteValidator::containsDynamicSegment($uri)) {
      DynamicRouteValidator::validateAndAddRoute($uri, $route);
    } else {
      StaticRouteValidator::validateAndAddRoute($uri, $route);
    }

    return $route;
  }

  /**
   * Method responsible for extracting the uri and handle 
   * from the http function arguments
   * 
   * @param array $args
   * @throws \Exception
   * @return array
   */
  private function getUriAndHandle(array $args): array
  {
    if (!isset($args[0]) || !isset($args[1])) {
      throw new Exception("Two parameters are expected when defining the request method. Ex: \$app->get(string \$uri, string \$handle)", 500);
    }

    $uri = preg_replace('/\s*/', '', $args[0]);
    $handle = $args[1];

    return [$uri, $handle];
  }

  /**
   * Method responsible for handling route definition http methods. 
   * Ex: get, post, delete
   * 
   * @param string $method
   * @param array $args
   * @throws \Exception
   * @return Route
   */
  public function __call(string $method, array $args): ?Route
  {
    $method = strtoupper($method);
    if (in_array($method, RouteRules::getEnabledMethods())) {
      [$uri, $handle] = $this->getUriAndHandle($args);

      return $this->add($method, $uri, $handle);
    } else {
      throw new Exception("Method Not Enabled", 400);
    }
  }

  public function run()
  {
    dd(RouteCollection::getStaticRoutes(), RouteCollection::getDynamicRoutes());
  }
}
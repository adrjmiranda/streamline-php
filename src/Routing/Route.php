<?php

namespace Streamline\Routing;

use Exception;

/**
 * Class responsible for representing a route 
 * in the list of defined routes
 * 
 * @package Streamline\Routing
 */
class Route
{
  /**
   * The route's HTTP method
   * 
   * @var string
   */
  private string $method;

  /**
   * The route URI
   * 
   * @var string
   */
  private string $uri;

  /**
   * The namespace of the control class 
   * responsible for handling the request 
   * for the route
   * 
   * @var string
   */
  private string $controllerNamespace;

  /**
   * The control method that handles the request
   * 
   * @var string
   */
  private string $action;

  /**
   * List of middleware defined 
   * for the route
   * 
   * @var array
   */
  private array $middlewares = [];

  /**
   * Method responsible for instantiating an 
   * instance of a route
   * 
   * @param string $method
   * @param string $uri
   * @param string $controllerNamespace
   * @param string $action
   */
  public function __construct(string $method, string $uri, string $controllerNamespace, string $action, array $groupMiddlewares = [])
  {
    $this->method = $method;
    $this->uri = $uri;
    $this->controllerNamespace = $controllerNamespace;
    $this->action = $action;

    $this->middlewares = array_merge($this->middlewares, $groupMiddlewares);
  }

  /**
   * Returns the HTTP method defined by the route
   * 
   * @return string
   */
  public function getMethod(): string
  {
    return $this->method;
  }

  /**
   * Method responsible for returning URI 
   * defined for the route
   * 
   * @return string
   */
  public function getUri(): string
  {
    return $this->uri;
  }

  /**
   * Method responsible for returning 
   * the namespace of the control class 
   * defined for the route
   * 
   * @return string
   */
  public function getControllerNamespace(): string
  {
    return $this->controllerNamespace;
  }

  /**
   * Method responsible for returning the 
   * name of the controlling class method
   * 
   * @return string
   */
  public function getAction(): string
  {
    return $this->action;
  }

  /**
   * Method responsible for returning the 
   * list of middleware defined for the route
   * 
   * @return array
   */
  public function getMiddlewares(): array
  {
    return $this->middlewares;
  }

  /**
   * Method responsible for adding a middleware 
   * to the route's middleware list
   * 
   * @param string $middlewareNamespance
   * @throws \Exception
   * @return Route
   */
  public function addMiddleware(string $middlewareNamespance): ?static
  {
    if (!class_exists($middlewareNamespance)) {
      throw new Exception("Middleware {$middlewareNamespance} does not exist", 500);
    }

    if (in_array($middlewareNamespance, $this->middlewares)) {
      throw new Exception("Trying to add existing {$middlewareNamespance} middleware", 500);
    }
    $this->middlewares[] = $middlewareNamespance;

    return $this;
  }
}
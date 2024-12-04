<?php

namespace Streamline\Routing;

use Exception;
use Streamline\Core\Router;

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
   * The name (alias) of the route
   * 
   * @var 
   */
  private ?string $alias = null;


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

    $this->middlewares = array_merge($groupMiddlewares, $this->middlewares);
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

  /**
   * Method responsible for replaces arguments with their 
   * respective values ​​in the uri for a dynamic route where 
   * an alias has been defined
   * 
   * @return string
   */
  private function fillsValuesInDynamicUri(string $uri, array $args): string
  {
    $uriSegments = UriParser::getUriSegments($uri);
    $argsKeys = array_keys($args);

    $newUriSegments = [];

    foreach ($uriSegments as $uriSegment) {
      if (DynamicRouteValidator::containsDynamicSegment($uriSegment)) {
        $argName = UriParser::getPatternArgName($uriSegment);

        if (!in_array($argName, $argsKeys)) {
          throw new Exception("It is necessary to pass all parameters to route '{$uri}'. Missing '{$argName}' parameter", 500);
        }

        $argValue = $args[$argName];
        $newUriSegments[] = $argValue;
      } else {
        $newUriSegments[] = $uriSegment;
      }
    }

    return '/' . implode('/', $newUriSegments);
  }

  /**
   * Method responsible for defining a alias for the route
   * 
   * @param string $name
   * @throws \Exception
   * @return void
   */
  public function alias(string $name, array $args = []): void
  {
    if (Router::aliasAlreadyRegistered($name)) {
      throw new Exception("Alias '{$name}' already defined.It is not possible to define two routes with the same name (alias)", 500);
    }

    $uri = $this->getUri();

    if (DynamicRouteValidator::containsDynamicSegment($uri) && empty($args)) {
      throw new Exception("To define the alias of a dynamic route, you must also define the arguments. Empty arguments passed to '{$uri}'", 500);
    }

    Router::addAlias($name);
    $this->alias = $name;

    if (DynamicRouteValidator::containsDynamicSegment($uri)) {
      $this->uri = $this->fillsValuesInDynamicUri($uri, $args);
    }
  }

  /**
   * Method responsible for returning the route 
   * name or null if the name has not been defined
   * 
   * @return string|null
   */
  public function getAlias(): ?string
  {
    return $this->alias;
  }

  public function getUriFromRouteAlias(string $alias): string
  {
    $allRoutes = array_merge(RouteCollection::getStaticRoutes(), RouteCollection::getDynamicRoutes());

    foreach ($allRoutes as $uri => $route) {
      if ($route->getAlias() === $alias) {
        return $uri;
      }
    }

    throw new Exception("Alias {$alias} has not been defined for any route", 500);
  }
}
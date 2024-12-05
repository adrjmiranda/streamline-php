<?php

namespace Streamline\Routing;

/**
 * Class responsible for handling dynamic and 
 * static route collections
 * 
 * @package Streamline\Routing
 */
class RouteCollection
{
  /**
   * List of routes with static uri
   * 
   * @var array
   */
  private static array $staticRoutes = [];

  /**
   * List of routes with dynamic uri
   * 
   * @var array
   */
  private static array $dynamicRoutes = [];

  /**
   * Method responsible for adding a route to a route list
   * 
   * @param string $uri
   * @param string $method
   * @param \Streamline\Routing\Route $route
   * @param array $list
   * @return void
   */
  private static function addRoute(string $uri, string $method, Route $route, array &$routeList): void
  {
    $routeList[$method][$uri] = $route;
  }

  /**
   * Method responsible for adding a route 
   * to the list of static routes
   * 
   * @param string $uri
   * @param string $method
   * @param \Streamline\Routing\Route $route
   * @return void
   */
  public static function addStaticRoute(string $uri, string $method, Route $route): void
  {
    self::addRoute($uri, $method, $route, self::$staticRoutes);
  }

  /**
   * Method responsible for adding a route 
   * to the dynamic route list
   * 
   * @param string $uri
   * @param string $method
   * @param \Streamline\Routing\Route $route
   * @return void
   */
  public static function addDynamicRoute(string $uri, string $method, Route $route): void
  {
    self::addRoute($uri, $method, $route, self::$dynamicRoutes);
  }

  /**
   * Method responsible for returning 
   * the list of static routes
   * 
   * @return array
   */
  public static function getStaticRoutes(string $method): array
  {
    return self::$staticRoutes[$method] ?? [];
  }

  /**
   * Method responsible for returning 
   * the list of dynamic routes
   * 
   * @return array
   */
  public static function getDynamicRoutes(string $method): array
  {
    return self::$dynamicRoutes[$method] ?? [];
  }

  /**
   * Method responsible for checking whether a uri 
   * is already defined in a route in the dynamic route 
   * list or in the static route list
   * 
   * @param string $uri
   * @return bool
   */
  public static function uriAlreadyRegistered(string $uri, string $method): bool
  {
    return StaticRouteValidator::staticRouteAlreadyExists($uri, $method) || DynamicRouteValidator::dynamicRouteAlreadyExists($uri, $method);
  }
}
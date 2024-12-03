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
   * Method responsible for adding a route 
   * to the list of static routes
   * 
   * @param string $uri
   * @param \Streamline\Routing\Route $route
   * @return void
   */
  public static function addStaticRoute(string $uri, Route $route): void
  {
    self::$staticRoutes[$uri] = $route;
  }

  /**
   * Method responsible for adding a route 
   * to the dynamic route list
   * 
   * @param string $uri
   * @param \Streamline\Routing\Route $route
   * @return void
   */
  public static function addDynamicRoute(string $uri, Route $route): void
  {
    self::$dynamicRoutes[$uri] = $route;
  }

  /**
   * Method responsible for returning 
   * the list of static routes
   * 
   * @return array
   */
  public static function getStaticRoutes(): array
  {
    return self::$staticRoutes;
  }

  /**
   * Method responsible for returning 
   * the list of dynamic routes
   * 
   * @return array
   */
  public static function getDynamicRoutes(): array
  {
    return self::$dynamicRoutes;
  }

  /**
   * Method responsible for checking whether a uri 
   * is already defined in a route in the dynamic route 
   * list or in the static route list
   * 
   * @param string $uri
   * @return bool
   */
  public static function uriAlreadyRegistered(string $uri): bool
  {
    return StaticRouteValidator::staticRouteAlreadyExists($uri) || DynamicRouteValidator::dynamicRouteAlreadyExists($uri);
  }
}
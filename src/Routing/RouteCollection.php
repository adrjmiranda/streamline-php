<?php

namespace Streamline\Routing;

class RouteCollection
{
  private static array $staticRoutes = [];
  private static array $dynamicRoutes = [];

  public static function addStaticRoute(string $uri, Route $route): void
  {
    self::$staticRoutes[$uri] = $route;
  }

  public static function addDynamicRoute(string $uri, Route $route): void
  {
    self::$dynamicRoutes[$uri] = $route;
  }

  public static function getStaticRoutes(): array
  {
    return self::$staticRoutes;
  }

  public static function getDynamicRoutes(): array
  {
    return self::$dynamicRoutes;
  }

  public static function uriAlreadyRegistered(string $uri): bool
  {
    return StaticRouteValidator::staticRouteAlreadyExists($uri) || DynamicRouteValidator::dynamicRouteAlreadyExists($uri);
  }
}
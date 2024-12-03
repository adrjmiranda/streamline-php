<?php

namespace Streamline\Routing;

use Exception;

class StaticRouteValidator
{
  public static function hasConflictWithStaticRoute(string $dynamicUri): bool
  {
    $staticKeys = array_keys(RouteCollection::getStaticRoutes());

    foreach ($staticKeys as $staticKey) {
      if (DynamicRouteValidator::dynamicSegmentCorrespondsWithStaticSegment($dynamicUri, $staticKey)) {
        return true;
      }
    }

    return false;
  }

  public static function staticRouteAlreadyExists(string $uri): bool
  {
    $staticKeys = array_keys(RouteCollection::getStaticRoutes());

    return in_array($uri, $staticKeys);
  }

  public static function validateAndAddRoute(string $uri, Route $route): void
  {
    if (DynamicRouteValidator::hasConflictWithDynamicRoute($uri)) {
      throw new Exception("Static URI {$uri} in conflict with dynamic URI already created", 500);
    }

    RouteCollection::addStaticRoute($uri, $route);
  }
}
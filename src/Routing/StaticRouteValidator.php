<?php

namespace Streamline\Routing;

use Exception;

/**
 * Class responsible for handling and storing 
 * routes defined with static uri
 * 
 * @package Streamline\Routing
 */
class StaticRouteValidator
{
  /**
   * Method responsible for checking whether a uri that will 
   * be defined for a static route has already been defined 
   * in a dynamic route
   * 
   * @param string $dynamicUri
   * @return bool
   */
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

  /**
   * Method responsible for checking whether a given uri has 
   * already been defined in the list of static routes
   * 
   * @param string $uri
   * @return bool
   */
  public static function staticRouteAlreadyExists(string $uri): bool
  {
    $staticKeys = array_keys(RouteCollection::getStaticRoutes());

    return in_array($uri, $staticKeys);
  }

  /**
   * Method responsible for validating and 
   * adding a route to the list of static 
   * routes
   * 
   * @param string $uri
   * @param \Streamline\Routing\Route $route
   * @throws \Exception
   * @return void
   */
  public static function validateAndAddRoute(string $uri, Route $route): void
  {
    if (DynamicRouteValidator::hasConflictWithDynamicRoute($uri)) {
      throw new Exception("Static URI {$uri} in conflict with dynamic URI already created", 500);
    }

    RouteCollection::addStaticRoute($uri, $route);
  }
}
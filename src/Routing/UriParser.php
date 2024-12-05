<?php

namespace Streamline\Routing;

use Exception;

/**
 * Class responsible for treating and analyzing 
 * the content of the uri defined for a route
 * 
 * @package Streamline\Routing
 */
class UriParser
{
  /**
   * Method responsible for returning the list 
   * of segments from a uri
   * 
   * @param string $uri
   * @return array
   */
  public static function getUriSegments(string $uri): array
  {
    return explode('/', trim($uri, '/'));
  }

  /**
   * Method responsible for returning the index of the 
   * list of parameter type patterns allowed for definition 
   * in a dynamic route
   * 
   * @param string $segment
   * @return string
   */
  public static function getPatternIndex(string $segment): string
  {
    return explode(':', str_replace(['[', ']'], '', $segment))[1];
  }

  /**
   * Method responsible for returns the name of the 
   * dynamic route argument
   * 
   * @param string $segment
   * @return string
   */
  public static function getPatternArgName(string $segment): string
  {
    return explode(':', str_replace(['[', ']'], '', $segment))[0];
  }

  /**
   * Method responsible for searching for a uri 
   * in a list of routes based on the route alias
   * 
   * @param string $alias
   * @param array $routeList
   * @return string
   */
  public static function searchUriByAlias(string $alias, array $routeList): string
  {
    $uriFound = '';

    foreach ($routeList as $httpMethod => $routes) {
      foreach ($routes as $uri => $route) {
        if ($route->getAlias() !== null && $route->getAlias() === $alias) {
          $uriFound = $uri;
          break;
        }
      }
    }

    return $uriFound;
  }

  /**
   * Method responsible for returning the route 
   * uri based on its alias
   * 
   * @param string $alias
   * @throws \Exception
   * @return null|string
   */
  public static function getUriFromRouteAlias(string $alias): ?string
  {
    $uriFound = self::searchUriByAlias($alias, RouteCollection::getStaticRouteList()) ?: self::searchUriByAlias($alias, RouteCollection::getDynamicRouteList());

    if (empty($uriFound)) {
      throw new Exception("Alias {$alias} has not been defined for any route", 500);
    }

    return $uriFound;
  }
}


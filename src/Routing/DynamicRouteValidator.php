<?php

namespace Streamline\Routing;

use Exception;

/**
 * Class responsible for handling and validating 
 * routes defined with dynamic uri
 * 
 * @package Streamline\Routing
 */
class DynamicRouteValidator
{
  /**
   * Method responsible for checking whether a static 
   * uri corresponds to a dynamic uri
   * 
   * @param string $patternUri
   * @param string $staticUri
   * @return bool
   */
  public static function dynamicSegmentCorrespondsWithStaticSegment(string $patternUri, string $staticUri): bool
  {
    $patternSegments = UriParser::getUriSegments($patternUri);
    $staticSegments = UriParser::getUriSegments($staticUri);

    if (count($patternSegments) !== count($staticSegments)) {
      return false;
    }

    foreach ($patternSegments as $index => $patternSegment) {
      $staticSegment = $staticSegments[$index];
      if (self::containsDynamicSegment($patternSegment)) {
        $patternIndex = UriParser::getPatternIndex($patternSegment);
        if (!preg_match(RouteRules::getParametersRules()[$patternIndex], $staticSegment)) {
          return false;
        }
      } else if ($staticSegment !== $patternSegment) {
        return false;
      }
    }

    return true;
  }

  /**
   * Method responsible for checking whether a dynamic uri 
   * has the definition of a non-mandatory parameter before a 
   * static segment or a mandatory parameter
   * 
   * @param string $dynamicUri
   * @return bool
   */
  private static function nonMandatoryParameterIsNotAtTheEnd(string $dynamicUri): bool
  {
    $segments = UriParser::getUriSegments($dynamicUri);

    $nonMandatoryParameterAlreadyDefined = false;

    foreach ($segments as $segment) {
      if (self::containsDynamicSegment($segment)) {
        $patternIndex = UriParser::getPatternIndex($segment);

        if (str_contains($patternIndex, '?')) {
          $nonMandatoryParameterAlreadyDefined = true;
        } else if ($nonMandatoryParameterAlreadyDefined) {
          return true;
        }
      }
    }

    return false;
  }

  /**
   * Method responsible for checking whether the definition 
   * pattern of a parameter within a dynamic uri is correct
   * 
   * @param string $dynamicUri
   * @return bool
   */
  private static function dynamicSegmentPatternIsCorrect(string $dynamicUri): bool
  {
    $segments = UriParser::getUriSegments($dynamicUri);

    $patternKeys = array_keys(RouteRules::getParametersRules());

    foreach ($segments as $segment) {
      if (self::containsDynamicSegment($segment)) {
        $patternIndex = UriParser::getPatternIndex($segment);
        if (!in_array($patternIndex, $patternKeys)) {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * Method responsible for checking whether a uri 
   * has a dynamic parameter definition
   * 
   * @param string $uri
   * @return bool
   */
  public static function containsDynamicSegment(string $uri): bool
  {
    return preg_match(RouteRules::getParameterPattern(), $uri);
  }

  /**
   * Method responsible for checking whether a uri that 
   * will be defined for a dynamic route has already been 
   * defined in a static route
   * 
   * @param string $staticUri
   * @param string $method
   * @return bool
   */
  public static function hasConflictWithDynamicRoute(string $staticUri, string $method): bool
  {
    $dynamicKeys = array_keys(RouteCollection::getDynamicRoutes($method));

    foreach ($dynamicKeys as $dynamicKey) {
      if (self::dynamicSegmentCorrespondsWithStaticSegment($dynamicKey, $staticUri)) {
        return true;
      }
    }

    return false;
  }

  /**
   * Method responsible for checking whether a given uri 
   * has already been defined in the dynamic route list
   * 
   * @param string $uri
   * @param string $method
   * @return bool
   */
  public static function dynamicRouteAlreadyExists(string $uri, string $method): bool
  {
    $dynamicKeys = array_keys(RouteCollection::getDynamicRoutes($method));

    return in_array($uri, $dynamicKeys);
  }

  /**
   * Method responsible for checking whether a defined 
   * dynamic route matches the uri sent in the request
   * 
   * @param string $uri
   * @param string $method
   * @return string|null
   */
  public static function uriMatchesWithDynamicRoute(string $uri, string $method): string|null
  {
    $dynamicKeys = array_keys(RouteCollection::getDynamicRoutes($method));
    foreach ($dynamicKeys as $dynamicKey) {
      if (self::dynamicSegmentCorrespondsWithStaticSegment($dynamicKey, $uri)) {
        return $dynamicKey;
      }
    }

    return null;
  }

  /**
   * Method responsible for returning the list of 
   * arguments that will be passed to the controller 
   * method
   * 
   * @param string $uriPattern
   * @param string $requestUri
   * @return array
   */
  public static function getDynamicRouteArguments(string $uriPattern, string $requestUri): array
  {
    $args = [];

    $patternSegments = UriParser::getUriSegments($uriPattern);
    $requestUriSegments = UriParser::getUriSegments($requestUri);

    foreach ($patternSegments as $index => $patternSegment) {
      $requestUriSegment = $requestUriSegments[$index];
      if (self::containsDynamicSegment($patternSegment)) {
        $patternIndex = UriParser::getPatternIndex($patternSegment);
        $argName = UriParser::getPatternArgName($patternSegment);

        if (preg_match(RouteRules::getParametersRules()[$patternIndex], $requestUriSegment)) {
          $args[$argName] = $requestUriSegment;
        }
      }
    }

    return $args;
  }

  /**
   * Method responsible for validating and adding a 
   * route to the dynamic route list
   * 
   * @param string $uri
   * @param string $method
   * @param \Streamline\Routing\Route $route
   * @throws \Exception
   * @return void
   */
  public static function validateAndAddRoute(string $uri, string $method, Route $route): void
  {
    if (!self::dynamicSegmentPatternIsCorrect($uri)) {
      throw new Exception("Error trying to set uri {$uri}. Default setting for dynamic url parameter incorrect. Ex: [id:%d]", 500);
    }

    if (self::nonMandatoryParameterIsNotAtTheEnd($uri)) {
      throw new Exception("Error trying to set uri {$uri}. Non-mandatory parameters can only be defined at the end of routes", 500);
    }

    if (StaticRouteValidator::hasConflictWithStaticRoute($uri, $method)) {
      throw new Exception("Dynamic URI {$uri} conflicting with static URI already created", 500);
    }

    RouteCollection::addDynamicRoute($uri, $method, $route);
  }
}
<?php

namespace Streamline\Routing;

use Exception;

class DynamicRouteValidator
{
  private static function dynamicSegmentCorrespondsWithStaticSegment(string $patternUri, string $staticUri): bool
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

  public static function containsDynamicSegment(string $uri): bool
  {
    return preg_match(RouteRules::getParameterPattern(), $uri);
  }

  public static function hasConflictWithDynamicRoute(string $staticUri): bool
  {
    $dynamicKeys = array_keys(RouteCollection::getDynamicRoutes());

    foreach ($dynamicKeys as $dynamicKey) {
      if (self::dynamicSegmentCorrespondsWithStaticSegment($dynamicKey, $staticUri)) {
        return true;
      }
    }

    return false;
  }

  public static function dynamicRouteAlreadyExists(string $uri): bool
  {
    $dynamicKeys = array_keys(RouteCollection::getDynamicRoutes());

    return in_array($uri, $dynamicKeys);
  }

  public static function validateAndAddRoute(string $uri, Route $route): void
  {
    if (!self::dynamicSegmentPatternIsCorrect($uri)) {
      throw new Exception("Error trying to set uri {$uri}. Default setting for dynamic url parameter incorrect. Ex: [id:%d]", 500);
    }

    if (self::nonMandatoryParameterIsNotAtTheEnd($uri)) {
      throw new Exception("Error trying to set uri {$uri}. Non-mandatory parameters can only be defined at the end of routes", 500);
    }

    if (StaticRouteValidator::hasConflictWithStaticRoute($uri)) {
      throw new Exception("Dynamic URI {$uri} conflicting with static URI already created", 500);
    }

    RouteCollection::addDynamicRoute($uri, $route);
  }
}
<?php

namespace Streamline\Routing;

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
}


<?php

namespace Streamline\Routing;

/**
 * Class responsible for managing 
 * the main system settings
 * 
 * @package Streamline\Routing
 */
class RouteRules
{
  /**
   * Method responsible for providing the list of allowed HTTP methods.
   * 
   * @param array $customHttpMethods
   * @return array
   */
  public static function getEnabledMethods(array $customHttpMethods = []): array
  {
    return $customHttpMethods ?: [
      'GET',
      'POST',
      'PUT',
      'DELETE',
      'PATCH'
    ];
  }

  /**
   * Method responsible for providing the rules for route parameters
   * 
   * @return array
   */
  public static function getParametersRules(): array
  {
    return [
      '?d' => '/^\d*$/',
      '%d' => '/^\d+$/',
      '?t' => '/^[a-zA-Z]*$/',
      '%t' => '/^[a-zA-Z]+$/',
      '?dt' => '/^[0-9a-zA-Z]*$/',
      '%dt' => '/^[0-9a-zA-Z]+$/',
    ];
  }

  /**
   * Method responsible for providing the pattern for route parameter
   * 
   * @return string
   */
  public static function getParameterPattern(): string
  {
    return '/\[(\s*[a-z]+(?:_[a-z]+)*\s*):(\s*(%|\?)[a-z]+\s*)\]/';
  }
}
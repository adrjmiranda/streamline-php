<?php

namespace Streamline\Routing;

/**
 * Class responsible for managing the main system settings
 * 
 * @package Streamline\Helpers
 */
class RouteRules
{
  /**
   * Method responsible for providing the list of allowed HTTP methods.
   * 
   * @return array
   */
  public static function getEnabledMethods(): array
  {
    return [
      'GET',
      'POST',
      'PUT',
      'DELETE',
      // 'PATCH',
      // 'HEAD',
      // 'OPTIONS',
      // 'TRACE',
      // 'CONNECT'
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
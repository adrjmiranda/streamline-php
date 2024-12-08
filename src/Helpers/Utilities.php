<?php

namespace Streamline\Helpers;

/**
 * Class responsible for providing useful methods for 
 * the framework, but not necessarily related
 * 
 * @package Streamline\Helpers
 */
class Utilities
{
  /**
   * Method responsible for returning the project root path
   * 
   * @return string
   */
  public static function rootPath(): string
  {
    $currentDir = __DIR__;
    $projectRoot = dirname($currentDir, 5);

    return $projectRoot;
  }
}

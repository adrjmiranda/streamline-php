<?php

namespace Streamline;

use Dotenv\Dotenv;
use Streamline\Helpers\Utilities;

/**
 * Class responsible for initializing 
 * essential framework dependencies
 * 
 * @package Streamline
 */
class Bootstrap
{
  /**
   * Method responsible for initializing 
   * framework dependencies
   * 
   * @return void
   */
  public static function initialize(): void
  {
    $dotenvPath = Utilities::rootPath() . '/.env';

    if (file_exists($dotenvPath)) {
      $dotenv = Dotenv::createImmutable(Utilities::rootPath());
      $dotenv->load();
    }
  }
}

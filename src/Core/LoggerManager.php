<?php

namespace Streamline\Core;

use Streamline\Helpers\Logger;

/**
 * Class responsible for providing a single
 *  Logger instance for the application
 * 
 * @package Streamline\Core
 */
class LoggerManager
{
  /**
   * An instance of the Logger class
   * 
   * @var null|Logger
   */
  private static ?Logger $instance = null;

  /**
   * Method responsible for providing a Logger instance
   * 
   * @return \Streamline\Helpers\Logger
   */
  public static function getLogger(): Logger
  {
    if (!self::$instance) {
      self::$instance = new Logger(rootPath() . '/logs/app.log');
    }

    return self::$instance;
  }
}
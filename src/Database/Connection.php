<?php

namespace Streamline\Database;

use Exception;
use PDO;
use PDOException;
use Streamline\Helpers\DBConfig;
use Streamline\Helpers\Logger;
use Streamline\Helpers\Utilities;

/**
 * 
 * Class responsible for managing connection to the database
 * 
 * @package Streamline\Core\Database
 */
class Connection
{
  /**
   * Connection PDO instance
   * 
   * @var null|PDO
   */
  private static ?PDO $conn = null;

  /**
   * Class constructor. 
   * Set to prevent direct instance
   */
  private function __construct()
  {
  }

  /**
   * Method responsible for returning a specific configuration
   * 
   * @param string $key
   * @return mixed
   */
  private static function getConfig(string $key): mixed
  {
    $data = DBConfig::get();

    return $data[$key] ?? null;
  }

  /**
   * Connects and returns an instance of 
   * connection to the database
   * 
   * @return \PDO
   */
  public static function get(): PDO
  {
    if (self::$conn === null) {
      $dbHost = self::getConfig('dbHost');
      $dbPort = self::getConfig('dbPort');
      $dbName = self::getConfig('dbName');
      $dbCharset = self::getConfig('dbCharset');

      $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=%s", $dbHost, $dbPort, $dbName, $dbCharset);
      $dbUser = self::getConfig('dbUser');
      $dbPass = self::getConfig('dbPass');
      $options = self::getConfig('options');

      try {
        self::$conn = new PDO($dsn, $dbUser, $dbPass, $options);
      } catch (PDOException $pDOException) {
        (new Logger(Utilities::rootPath() . '/logs/database.log'))->error($pDOException->getMessage(), []);
        throw new Exception("Error Processing Request", 500);
      }
    }

    return self::$conn;
  }
}
<?php

namespace Streamline\Helpers;

use PDO;

/**
 * Class responsible for providing 
 * the basic database connection settings
 * 
 * @package Streamline\Helpers
 */
class DBConfig
{
  /**
   * The database host
   * 
   * @var string
   */
  private static string $dbHost;

  /**
   * The database server port
   * 
   * @var string
   */
  private static string $dbPort;

  /**
   * The name of the database
   * 
   * @var string
   */
  private static string $dbName;

  /**
   * Database character formatting
   * 
   * @var string
   */
  private static string $dbCharset;

  /**
   * The database user name
   * 
   * @var string
   */
  private static string $dbUser;

  /**
   * The database password
   * 
   * @var string
   */
  private static string $dbPass;

  /**
   * The connection configuration array using PDO
   * 
   * @var array
   */
  private static array $options;

  /**
   * Method responsible for returning the 
   * list of configurations for connecting 
   * to the database
   * 
   * @return array
   */
  public static function get(): array
  {
    self::$dbHost = $_ENV['DB_HOST'] ?? '';
    self::$dbPort = $_ENV['DB_PORT'] ?? '';
    self::$dbName = $_ENV['DB_NAME'] ?? '';
    self::$dbCharset = $_ENV['DB_CHARSET'] ?? '';
    self::$dbUser = $_ENV['DB_USER'] ?? '';
    self::$dbPass = $_ENV['DB_PASS'] ?? '';

    self::$options = [
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
    ];

    return [
      'options' => self::$options,
      'dbHost' => self::$dbHost,
      'dbPort' => self::$dbPort,
      'dbName' => self::$dbName,
      'dbCharset' => self::$dbCharset,
      'dbUser' => self::$dbUser,
      'dbPass' => self::$dbPass
    ];
  }
}
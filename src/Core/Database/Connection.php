<?php

namespace Streamline\Core\Database;

use PDO;
use PDOException;

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

  private function __construct()
  {
  }

  private static function getConfig(string $key): mixed
  {
    $data = require_once rootPath() . '/app/config/database.php';

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

      $dsn = sprintf("mysql:host=%s;port=%d;dbname=%s;charset=%s", $dbHost, $dbPort, $dbName, $dbCharset);
      $dbUser = self::getConfig('dbUser');
      $dbPass = self::getConfig('dbPass');
      $options = self::getConfig('options');

      try {
        self::$conn = new PDO($dsn, $dbUser, $dbPass, $options);
      } catch (PDOException $pDOException) {
        //throw $th;
        // TODO: add to log
      }
    }

    return self::$conn;
  }

  /**
   * Executes a database query with parameters
   * 
   * @param string $sql
   * @param array $params
   * @return array
   */
  public static function query(string $sql, array $params = []): array
  {
    $stmt = self::get()->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
  }

  /**
   * Executes an SQL command (INSERT, UPDATE, DELETE)
   * 
   * @param string $sql
   * @param array $params
   * @return int
   */
  public static function execute(string $sql, array $params = []): int
  {
    $stmt = self::get()->prepare($sql);
    $stmt->execute($params);

    return $stmt->rowCount();
  }

  /**
   * Start a transaction
   * 
   * @return void
   */
  public static function beginTransaction(): void
  {
    self::get()->beginTransaction();
  }

  /**
   * Confirm the transaction
   * 
   * @return void
   */
  public static function commit(): void
  {
    self::get()->commit();
  }

  /**
   * Undo the transaction
   * 
   * @return void
   */
  public static function rollBack(): void
  {
    self::get()->rollBack();
  }
}
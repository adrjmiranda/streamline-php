<?php

namespace Streamline\Core;

use PDO;
use PDOException;
use Streamline\Database\Connection;
use Streamline\Helpers\Logger;

/**
 * Class responsible for providing standard 
 * communication methods with database entities
 * 
 * @package Streamline\Core\Database
 */
abstract class Model
{
  /**
   * Stores the connection to the database
   * 
   * @var PDO
   */
  protected PDO $conn;

  /**
   * The Logger instance
   * 
   * @var null|Logger 
   */
  private ?Logger $logger = null;

  /**
   * Method responsible for initializing an instance 
   * of the class and obtaining value for $conn
   */
  public function __construct()
  {
    $this->conn = Connection::get();
    $this->logger = new Logger(rootPath() . '/logs/database.log');
  }

  /**
   * Method responsible for providing a way for the child 
   * class to define the name of the table in the database
   * 
   * @return string
   */
  abstract protected function getTableName(): string;

  /**
   * Method responsible for returning a row from a database table
   * 
   * @param int $id
   * @return mixed
   */
  public function find(int $id): ?object
  {
    try {
      $sql = "SELECT * FROM {$this->getTableName()} WHERE id = :id LIMIT  1";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetch() ?: null;
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return null;
    }
  }

  /**
   * Method responsible for returning all rows from a database table
   * 
   * @return array
   */
  public function all(): array
  {
    try {
      $sql = "SELECT * FROM {$this->getTableName()}";
      $stmt = $this->conn->query($sql);

      return $stmt->fetchAll();
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return [];
    }
  }

  /**
   * Method responsible for adding a new row to a table in the database
   * 
   * @param array $data
   * @return int
   */
  public function create(array $data): int
  {
    try {
      $fields = array_keys($data);

      $columns = implode(',', $fields);
      $placeholders = ':' . implode(', :', $fields);
      $sql = "INSERT INTO {$this->getTableName()} ($columns) VALUES ($placeholders)";

      $stmt = $this->conn->prepare($sql);
      $stmt->execute($data);

      return (int) $this->conn->lastInsertId();
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return 0;
    }
  }

  /**
   * Method responsible for updating a row in a table in the database
   * 
   * @param int $id
   * @param array $data
   * @return bool
   */
  public function update(int $id, array $data): bool
  {
    try {
      $fields = array_keys($data);
      $values = array_values($data);

      $columns = implode(' = ?, ', $fields) . ' = ?';
      $sql = "UPDATE {$this->getTableName()} SET $columns WHERE id = ?";

      $stmt = $this->conn->prepare($sql);

      return $stmt->execute([...$values, $id]);
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return false;
    }
  }

  /**
   * Method responsible for removing a row in the database
   * 
   * @param int $id
   * @return bool
   */
  public function delete(int $id): bool
  {
    try {
      $sql = "DELETE FROM {$this->getTableName()} WHERE id = :id";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);

      return $stmt->execute();
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return false;
    }
  }

  /**
   * Method responsible for executing a query in the database based 
   * on equality conditions of at least one parameter
   * 
   * @param array $conditions
   * @return array
   */
  public function where(array $conditions): array
  {
    try {
      $clauses = [];
      $params = [];

      foreach ($conditions as $column => $value) {
        $clauses[] = "$column = :$column";
        $params[":$column"] = $value;
      }

      $sql = "SELECT * FROM {$this->getTableName()} WHERE " . implode(' AND ', $clauses);
      $stmt = $this->conn->prepare($sql);
      $stmt->execute($params);

      return $stmt->fetchAll();
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return [];
    }
  }

  /**
   * Method responsible for returning data according 
   * to a displacement in the table and limit quantity per page
   * 
   * @param int $limit
   * @param int $offset
   * @return array
   */
  public function paginate(int $limit, int $offset): array
  {
    try {
      $sql = "SELECT * FROM {$this->getTableName()} LIMIT :limit OFFSET :offset";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
      $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll();
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return [];
    }
  }

  /**
   * Method responsible for returning the number of rows in a database table
   * 
   * @return int|null
   */
  public function count(): ?int
  {
    try {
      $sql = "SELECT COUNT(*) FROM {$this->getTableName()}";
      $stmt = $this->conn->query($sql);

      return (int) $stmt->fetchColumn();
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return null;
    }
  }

  /**
   * Start a transaction
   * 
   * @return void
   */
  protected function beginTransaction(): void
  {
    $this->conn->beginTransaction();
  }

  /**
   * Confirm the transaction
   * 
   * @return void
   */
  protected function commit(): void
  {
    $this->conn->commit();
  }

  /**
   * Undo the transaction
   * 
   * @return void
   */
  protected function rollback(): void
  {
    $this->conn->rollBack();
  }

  /**
   * Method responsible for executing a custom query on the database
   * 
   * @param string $sql
   * @param array $params
   * @return array
   */
  public function query(string $sql, array $params = []): array
  {
    try {
      $stmt = $this->conn->prepare($sql);
      $stmt->execute($params);

      return $stmt->fetchAll();
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return [];
    }
  }

  /**
   * Method responsible for handling exceptions that occur during database operations
   * 
   * @param \PDOException $pDOException
   * @param string $method
   * @return void
   */
  protected function handleException(PDOException $pDOException, string $method): void
  {
    $this->logger->error("Error when calling method {method}.\n" . $pDOException->getMessage(), [
      'method' => $method
    ]);
  }
}
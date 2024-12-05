<?php

namespace Streamline\Core\Database;

use PDO;
use PDOException;

abstract class Model
{
  protected PDO $conn;

  public function __construct()
  {
    $this->conn = Connection::get();
  }

  abstract protected function getTableName(): string;

  protected function find(int $id): ?array
  {
    try {
      $sql = "SELECT * FROM {$this->getTableName()} WHERE id = :id LIMIT  1";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':id', $id, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return null;
    }
  }

  protected function all(): array
  {
    try {
      $sql = "SELECT * FROM {$this->getTableName()}";
      $stmt = $this->conn->query($sql);

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return [];
    }
  }

  protected function create(array $data): int
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

  protected function update(int $id, array $data): bool
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

  protected function delete(int $id): bool
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

  protected function where(array $conditions): array
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

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return [];
    }
  }

  protected function paginate(int $limit, int $offset): array
  {
    try {
      $sql = "SELECT * FROM {$this->getTableName()} LIMIT :limit OFFSET :offset";
      $stmt = $this->conn->prepare($sql);
      $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
      $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
      $stmt->execute();

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return [];
    }
  }

  protected function count(): ?int
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

  protected function query(string $sql, array $params = []): array
  {
    try {
      $stmt = $this->conn->prepare($sql);
      $stmt->execute($params);

      return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $pDOException) {
      $this->handleException($pDOException, __METHOD__);

      return [];
    }
  }

  protected function handleException(PDOException $pDOException, string $method): void
  {
    // TODO: ...
  }
}
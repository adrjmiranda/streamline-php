<?php

namespace Streamline\Helpers;

use PDO;
use Streamline\Core\SessionManager;
use Streamline\Database\Connection;

/**
 * Trait responsável por armazenar os métodos 
 * de validação pré-definidos
 * 
 * @package Streamline\Database\Connection
 */
trait Validations
{
  /**
   * Method responsible for checking if a field is empty
   * 
   * @param string $field
   * @param array $params
   * @return bool
   */
  private function required(string $field, array $params): bool
  {
    $var = $this->processedVar($this->request->getOnlyBodyParameters($field));

    if ($var === null) {
      $this->processedData[$field] = null;
      return false;
    }

    $this->processedData[$field] = $var;
    return true;
  }

  /**
   * Method responsible for checking whether a field 
   * has a minimum number of characters
   * 
   * @param string $field
   * @param array $params
   * @return bool
   */
  private function min(string $field, array $params): bool
  {
    $var = $this->processedVar($this->request->getOnlyBodyParameters($field));
    $min = (int) $params[0];

    if (strlen($var) < $min) {
      $this->processedData[$field] = null;
      return false;
    }

    $this->processedData[$field] = $var;
    return true;
  }

  /**
   * Method responsible for checking whether a 
   * field has a maximum number of characters
   * 
   * @param string $field
   * @param array $params
   * @return bool
   */
  private function max(string $field, array $params): bool
  {
    $var = $this->processedVar($this->request->getOnlyBodyParameters($field));
    $max = (int) $params[0];

    if (strlen($var) > $max) {
      $this->processedData[$field] = null;
      return false;
    }

    $this->processedData[$field] = $var;
    return true;
  }

  /**
   * Method responsible for checking 
   * whether a given field is a valid email
   * 
   * @param string $field
   * @param array $params
   * @return bool
   */
  private function email(string $field, array $params): bool
  {
    $email = $this->processedVar($this->request->getOnlyBodyParameters($field));

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $this->processedData[$field] = null;
      return false;
    }

    $this->processedData[$field] = $email;
    return true;
  }

  /**
   * Method responsible for checking whether a given field has 
   * the same value as a field that is defined as unique in the 
   * database
   * 
   * @param string $field
   * @param array $params
   * @return bool
   */
  private function unique(string $field, array $params): bool
  {
    $var = $this->processedVar($this->request->getOnlyBodyParameters($field));
    $tableName = $params[0];

    $conn = Connection::get();
    $sql = "SELECT * FROM {$tableName} WHERE {$field} = :{$field} LIMIT 1";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":{$field}", $var);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($row)) {
      $this->processedData[$field] = null;
      return false;
    }

    $this->processedData[$field] = $var;
    return true;
  }

  /**
   * Method responsible for checking whether the csrf 
   * token defined in the session is the same as the 
   * one sent in the request
   * 
   * @param string $field
   * @param array $params
   * @return bool
   */
  private function csrf(string $field, array $params): bool
  {
    $csrfToken = $this->processedVar($this->request->getOnlyBodyParameters($field));

    $session = new SessionManager();

    $sessionCsrfToken = $session->get($field);

    if ($csrfToken !== $sessionCsrfToken) {
      $this->processedData[$field] = null;
      return false;
    }

    $this->processedData[$field] = $csrfToken;
    return true;
  }
}

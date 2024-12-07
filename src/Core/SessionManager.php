<?php

namespace Streamline\Core;

/**
 * Class responsible for managing sessions
 * 
 * @package Streamline\Core
 */
class SessionManager
{
  /**
   * Method responsible for instantiating the class
   */
  public function __construct()
  {
    if (session_status() === PHP_SESSION_NONE) {
      session_start();
    }
  }

  /**
   * Method responsible for defining an item within the global variable $_SESSION
   * 
   * @param string $key
   * @param mixed $value
   * @return void
   */
  public function set(string $key, mixed $value): void
  {
    $_SESSION[$key] = $value;
  }

  /**
   * Method responsible for returning an item in the session
   * 
   * @param string $key
   * @param mixed $default
   * @return mixed
   */
  public function get(string $key, mixed $default = null): mixed
  {
    return $_SESSION[$key] ?? $default;
  }

  /**
   * Method responsible for removing a specific item from the session
   * 
   * @param string $key
   * @return void
   */
  public function remove(string $key): void
  {
    unset($_SESSION[$key]);
  }

  /**
   * Method responsible for checking whether a certain item is defined in the session
   * 
   * @param string $key
   * @return bool
   */
  public function has(string $key): bool
  {
    return isset($_SESSION[$key]);
  }

  /**
   * Method responsible for removing all items from the session
   * 
   * @return void
   */
  public function clear(): void
  {
    session_unset();
  }

  /**
   * Method responsible for destroying the session
   * 
   * @return void
   */
  public function destroy(): void
  {
    if (session_status() === PHP_SESSION_ACTIVE) {
      session_unset();
      session_destroy();
    }
  }

  /**
   * Method responsible for returning the current session ID
   * 
   * @return string
   */
  public function getId(): string
  {
    return session_id();
  }

  /**
   * Method responsible for regenerating the session ID
   * 
   * @param bool $deleteOldSession
   * @return void
   */
  public function regenerateId(bool $deleteOldSession = true): void
  {
    session_regenerate_id($deleteOldSession);
  }
}
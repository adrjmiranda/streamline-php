<?php

namespace Streamline\Helpers;

use DateTime;
use RuntimeException;

/**
 * Class responsible for creating and saving log files
 * 
 * @package Streamline\Helpers
 */
class Logger
{
  /**
   * The address of the file 
   * where the logs will be saved
   * 
   * @var string
   */
  protected string $logFile;

  /**
   * Method responsible for instantiating the 
   * logs class by defining the log file path
   * 
   * @param string $logFile
   */
  public function __construct(string $logFile)
  {
    $this->logFile = $logFile;
  }

  /**
   * Method responsible for storing treated log message
   * 
   * @param string $level
   * @param string $message
   * @param array $context
   * @return void
   */
  public function log(string $level, string $message, array $context = []): void
  {
    $directory = dirname($this->logFile);

    if (!is_dir($directory)) {
      if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
        throw new RuntimeException(sprintf('Unable to create directory: %s', $directory));
      }
    }

    $timestamp = (new DateTime())->format('Y-m-d H:i:s');
    $formattedMessage = $this->interpolate($message, $context);

    $logEntry = sprintf("[%s] %s: %s\n", $timestamp, strtoupper($level), $formattedMessage);
    file_put_contents($this->logFile, $logEntry, FILE_APPEND);
  }

  /**
   * Method responsible for defining log information
   * 
   * @param string $message
   * @param array $context
   * @return void
   */
  public function info(string $message, array $context = []): void
  {
    $this->log('info', $message, $context);
  }

  /**
   * Method responsible for setting error log
   * 
   * @param string $message
   * @param array $context
   * @return void
   */
  public function error(string $message, array $context = []): void
  {
    $this->log('error', $message, $context);
  }

  /**
   * Method responsible for setting warning log
   * 
   * @param string $message
   * @param array $context
   * @return void
   */
  public function warning(string $message, array $context = []): void
  {
    $this->log('warning', $message, $context);
  }

  /**
   * Method responsible for setting debug log
   * 
   * @param string $message
   * @param array $context
   * @return void
   */
  public function debug(string $message, array $context = []): void
  {
    $this->log('debug', $message, $context);
  }

  /**
   * Method responsible for replacing parameters in the 
   * log message according to the context passed by array
   * 
   * @param string $message
   * @param array $context
   * @return string
   */
  private function interpolate(string $message, array $context): string
  {
    $replacements = [];
    foreach ($context as $key => $value) {
      $replacements["{{$key}}"] = $value;
    }

    return strtr($message, $replacements);
  }
}
<?php

namespace Streamline\Core;

/**
 * Class responsible for managing the cache
 * 
 * @package Streamline\Core
 */
class Cache
{
  /**
   * The path to the directory where 
   * the cache files will be stored
   * 
   * @var string
   */
  private string $cacheDir;

  /**
   * Method responsible for instantiating the cache class
   * 
   * @param string $cacheDir
   */
  public function __construct(string $cacheDir)
  {
    $this->cacheDir = rtrim($cacheDir, '/') . '/';

    if (!is_dir($this->cacheDir)) {
      mkdir($this->cacheDir, 0755, true);
    }
  }

  /**
   * Method responsible for creating a cache file based on a key
   * 
   * @param string $key
   * @param mixed $value
   * @param int $ttl
   * @return void
   */
  public function set(string $key, mixed $value, int $ttl = 3600): void
  {
    $cacheFile = $this->getCacheFile($key);
    $data = [
      'expiry' => time() + $ttl,
      'value' => $value
    ];
    file_put_contents($cacheFile, serialize($data));
  }

  /**
   * Method responsible for returning the 
   * contents of the cache file if it exists
   * 
   * @param string $key
   * @return mixed
   */
  public function get(string $key): mixed
  {
    $cacheFile = $this->getCacheFile($key);

    if (!file_exists($cacheFile)) {
      return null;
    }

    $data = unserialize(file_get_contents($cacheFile));

    if ($data['expiry'] < time()) {
      unlink($cacheFile);
      return null;
    }

    return $data['value'];
  }

  /**
   * Method responsible for removing a cache file
   * 
   * @param string $key
   * @return void
   */
  public function delete(string $key): void
  {
    $cacheFile = $this->getCacheFile($key);

    if (file_exists($cacheFile)) {
      unlink($cacheFile);
    }
  }

  /**
   * Method responsible for removing all cache files
   * 
   * @return void
   */
  public function clear(): void
  {
    $files = glob($this->cacheDir . '*');
    foreach ($files as $file) {
      if (is_file($file)) {
        unlink($file);
      }
    }
  }

  /**
   * Method responsible for returning the 
   * unique key for the cache file name
   * 
   * @param string $key
   * @return string
   */
  private function getCacheFile(string $key): string
  {
    return $this->cacheDir . md5($key) . '.cache';
  }
}
<?php

$dbHost = $_ENV['DB_HOST'] ?? '';
$dbPort = $_ENV['DB_PORT'] ?? '';
$dbName = $_ENV['DB_NAME'] ?? '';
$dbCharset = $_ENV['DB_CHARSET'] ?? '';
$dbUser = $_ENV['DB_USER'] ?? '';
$dbPass = $_ENV['DB_PASS'] ?? '';

$options = [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
];

return [
  'options' => $options,
  'dbHost' => $dbHost,
  'dbPort' => $dbPort,
  'dbName' => $dbName,
  'dbCharset' => $dbCharset,
  'dbUser' => $dbUser,
  'dbPass' => $dbPass
];
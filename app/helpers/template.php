<?php

$baseUrl = function (): string {
  $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];

  return "{$protocol}://{$host}";
};

return [
  'baseUrl' => $baseUrl
];
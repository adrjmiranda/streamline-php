<?php

use Streamline\Routing\Route;
use Streamline\Routing\UriParser;

$baseUrl = function (): string {
  $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'];

  return "{$protocol}://{$host}";
};

$toUri = function (string $alias): string {
  return UriParser::getUriFromRouteAlias($alias);
};

return [
  'baseUrl' => $baseUrl,
  'toUri' => $toUri
];
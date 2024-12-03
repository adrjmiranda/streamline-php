<?php

namespace Streamline\Routing;

class UriParser
{
  public static function getUriSegments(string $uri): array
  {
    return explode('/', trim($uri, '/'));
  }

  public static function getPatternIndex(string $segment): string
  {
    return explode(':', str_replace(['[', ']'], '', $segment))[1];
  }
}


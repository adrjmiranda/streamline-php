<?php

namespace App\Middlewares;

use Streamline\Core\Cache;
use Streamline\Routing\Request;
use Streamline\Routing\Response;

class StoreCacheMiddleware
{
  public function handle(Request $request, callable $next): mixed
  {
    $response = $next($request);

    if ($response instanceof Response) {
      $cache = new Cache(rootPath() . '/cache');
      $cacheKey = $this->generateCacheKey($request);

      $cache->set($cacheKey, $response->getBody(), 10);
    }

    return $response;
  }

  private function generateCacheKey(Request $request): string
  {
    return md5($request->getUri());
  }
}
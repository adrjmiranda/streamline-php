<?php

namespace App\Middlewares;

use Streamline\Core\Cache;
use Streamline\Routing\Request;
use Streamline\Routing\Response;

class CacheMiddleware
{
  public function handle(Request $request, callable $next): mixed
  {
    $cache = new Cache(rootPath() . '/cache');
    $cacheKey = $this->generateCacheKey($request);

    $cacheContent = $cache->get($cacheKey);

    if ($cacheContent !== null) {
      $response = new Response();
      $response->setBody($cacheContent)->send();
    }

    return $next($request);
  }

  private function generateCacheKey(Request $request): string
  {
    return md5($request->getUri());
  }
}
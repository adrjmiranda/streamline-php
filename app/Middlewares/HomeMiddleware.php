<?php

namespace App\Middlewares;

use Streamline\Routing\Request;

class HomeMiddleware
{
  public function handle(Request $request, callable $next)
  {
    var_dump('Home Middleware');

    return $next($request);
  }
}
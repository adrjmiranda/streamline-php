<?php

namespace App\Middlewares;

use Streamline\Routing\Request;

class MaintenanceMiddleware
{
  public function handle(Request $request, callable $next)
  {
    var_dump('Maintenance Middleware');

    return $next($request);
  }
}
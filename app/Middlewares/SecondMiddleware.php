<?php

namespace App\Middlewares;

use Streamline\Routing\Request;
use Streamline\Routing\Response;

class SecondMiddleware
{
  public function index(Request $request, Response $response, array $args): Response
  {
    return $response;
  }

  public function show(Request $request, Response $response, array $args): Response
  {
    return $response;
  }

  public function store(Request $request, Response $response, array $args): Response
  {
    return $response;
  }
}
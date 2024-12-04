<?php

namespace App\Controllers;

use Streamline\Routing\Request;
use Streamline\Routing\Response;

class HomeController
{
  public function index(Request $request, Response $response, array $args = []): Response
  {
    $response->setBody('Home Controller');

    return $response;
  }
}
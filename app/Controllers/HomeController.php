<?php

namespace App\Controllers;

use Streamline\Routing\Request;
use Streamline\Routing\Response;

class HomeController extends Controller
{
  public function index(Request $request, Response $response, array $args = []): Response
  {
    $response->setBody($this->view('home', [
      'name' => 'Home'
    ]));

    return $response;
  }
}
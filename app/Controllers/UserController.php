<?php

namespace App\Controllers;

use Streamline\Routing\Request;
use Streamline\Routing\Response;

class UserController extends Controller
{
  public function register(Request $request, Response $response, array $args = []): Response
  {
    $response->setBody($this->view('register', [
      'pageTitle' => 'Register Now'
    ]));

    return $response;
  }

  public function store(Request $request, Response $response, array $args = []): Response
  {
    $data = $request->getBody();

    dd($data);

    return $response;
  }

  public function show(Request $request, Response $response, array $args = []): Response
  {
    $data = $request->getBody();

    dd($data);

    return $response;
  }
}
<?php

namespace App\Controllers;

use Streamline\Routing\Request;
use Streamline\Routing\Response;

class ErrorController extends Controller
{
  public function notFound(Request $request, Response $response, array $args = []): Response
  {
    $response->setBody($this->view('not-found', [
      'pageTitle' => 'Error 404'
    ]));

    return $response;
  }

  public function serverError(Request $request, Response $response, array $args = []): Response
  {
    $response->setBody($this->view('server-error', [
      'pageTitle' => 'Error 500'
    ]));

    return $response;
  }
}
<?php

namespace App\Controllers;

use Streamline\Core\Template;
use Streamline\Routing\Request;
use Streamline\Routing\Response;

class HomeController
{
  public function index(Request $request, Response $response, array $args = []): Response
  {
    $viewsPath = rootPath() . '/app/Views';
    $template = new Template($viewsPath);
    $view = $template->render('home', [
      'title' => 'Home Page',
      'name' => 'Home'
    ]);

    $response->setBody($view);

    return $response;
  }
}